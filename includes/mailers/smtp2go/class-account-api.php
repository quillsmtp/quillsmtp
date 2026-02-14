<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTP2GO;

defined( 'ABSPATH' ) || exit;

use WP_Error;

/**
 * Account_API class.
 *
 * @since 1.0.0
 */
class Account_API {

	/**
	 * API
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * API endpoint URL
	 *
	 * @var string
	 */
	protected $api_endpoint = 'https://api.smtp2go.com/v3/email/send';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Send email
	 *
	 * @param array $args Email arguments.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args ) {
		$args['api_key'] = $this->api_key;
		$response        = wp_remote_request(
			'https://api.smtp2go.com/v3/email/send',
			[
				'method'  => 'POST',
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => $this->api_key,
				],
				'body'    => wp_json_encode( $args ),
				'timeout' => 60,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'empty_response', __( 'Empty response.', 'quill-smtp' ) );
		}

		$body = json_decode( $body, true );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response.', 'quill-smtp' ) );
		}

		return $body;
	}

	/**
	 * Send a chunk of emails using cURL multi
	 *
	 * @since 1.0.0
	 *
	 * @param array    $emails_data   Array of email data, keyed by recipient email.
	 *                                Each entry should contain: 'request_data' => array (the full API request body)
	 * @param callable $log_callback  Optional callback for logging. Receives: $email, $request_data, $response, $http_code, $error
	 *
	 * @return array Chunk results with 'sent_count', 'failed', 'message_ids'
	 */
	public function send_chunk( $emails_data, $log_callback = null ) {
		$mh      = curl_multi_init();
		$handles = array();
		$results = array(
			'sent_count'  => 0,
			'failed'      => array(),
			'message_ids' => array(),
		);

		// Create cURL handles for each email
		foreach ( $emails_data as $email => $data ) {
			$request_data = $data['request_data'];

			// Add API key to request body (SMTP2GO requires this)
			$request_data['api_key'] = $this->api_key;

			// Log the request data before sending
			if ( is_callable( $log_callback ) ) {
				call_user_func( $log_callback, $email, $request_data, null, null, null, 'request' );
			}

			// Create cURL handle
			$ch = curl_init( $this->api_endpoint );
			curl_setopt_array(
				$ch,
				array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_POST           => true,
					CURLOPT_POSTFIELDS     => wp_json_encode( $request_data ),
					CURLOPT_HTTPHEADER     => array(
						'Content-Type: application/json',
						'Accept: application/json',
					),
					CURLOPT_TIMEOUT        => 30,
					CURLOPT_SSL_VERIFYPEER => true,
				)
			);

			// Add to multi handle
			curl_multi_add_handle( $mh, $ch );
			$handles[ $email ] = array(
				'handle'       => $ch,
				'request_data' => $request_data,
			);
		}

		// Execute all requests
		$active = null;
		do {
			$status = curl_multi_exec( $mh, $active );
			if ( $active ) {
				curl_multi_select( $mh );
			}
		} while ( $active && $status === CURLM_OK );

		// Collect results
		foreach ( $handles as $email => $handle_data ) {
			$ch           = $handle_data['handle'];
			$request_data = $handle_data['request_data'];
			$response     = curl_multi_getcontent( $ch );
			$http_code    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			$error        = curl_error( $ch );

			// Log the response
			if ( is_callable( $log_callback ) ) {
				call_user_func( $log_callback, $email, $request_data, $response, $http_code, $error, 'response' );
			}

			if ( $error ) {
				$results['failed'][ $email ] = $error;
			} else {
				$parsed = $this->parse_response( $response, $http_code );

				if ( $parsed['success'] ) {
					$results['sent_count']++;
					if ( ! empty( $parsed['message_id'] ) ) {
						$results['message_ids'][ $email ] = $parsed['message_id'];
					}
				} else {
					$results['failed'][ $email ] = $parsed['error'] ?? __( 'Unknown error', 'quill-smtp' );
				}
			}

			// Cleanup
			curl_multi_remove_handle( $mh, $ch );
			curl_close( $ch );
		}

		curl_multi_close( $mh );

		// Log batch emails to QuillSMTP email log using data from first email
		$first_email_data = reset( $emails_data );
		if ( ! empty( $first_email_data['request_data'] ) ) {
			$request_data = $first_email_data['request_data'];
			$batch_args   = array(
				'subject'       => $request_data['subject'] ?? '',
				'html_body'     => $request_data['html_body'] ?? '',
				'text_body'     => $request_data['text_body'] ?? '',
				'sender'        => $request_data['sender'] ?? '',
				'headers'       => $request_data['custom_headers'] ?? [],
				'connection_id' => $first_email_data['connection_id'] ?? '',
				'account_id'    => $first_email_data['account_id'] ?? '',
			);
			$recipients   = array_keys( $emails_data );
			$log_result   = empty( $results['failed'] ) ? $results : new \WP_Error( 'smtp2go_batch_partial_failure', implode( ', ', $results['failed'] ) );
			$this->log_batch_emails( $batch_args, $recipients, $log_result );
		}

		return $results;
	}

	/**
	 * Parse API response from SMTP2GO
	 *
	 * @since 1.0.0
	 *
	 * @param string $response  Raw response body.
	 * @param int    $http_code HTTP response code.
	 *
	 * @return array Parsed result with 'success', 'message_id', 'error' keys.
	 */
	public function parse_response( $response, $http_code ) {
		$result = array(
			'success'    => false,
			'message_id' => '',
			'error'      => '',
		);

		if ( empty( $response ) ) {
			$result['error'] = __( 'Empty response from SMTP2GO', 'quill-smtp' );
			return $result;
		}

		$body = json_decode( $response, true );

		if ( ! is_array( $body ) ) {
			$result['error'] = __( 'Invalid JSON response from SMTP2GO', 'quill-smtp' );
			return $result;
		}

		// SMTP2GO success response structure
		if ( isset( $body['data']['succeeded'] ) && $body['data']['succeeded'] > 0 ) {
			$result['success'] = true;

			// Get message ID if available
			if ( ! empty( $body['data']['email_id'] ) ) {
				$result['message_id'] = $body['data']['email_id'];
			}
		} elseif ( isset( $body['data']['error'] ) ) {
			$result['error'] = $body['data']['error'];
		} elseif ( isset( $body['data']['failures'] ) && ! empty( $body['data']['failures'] ) ) {
			$result['error'] = implode( ', ', $body['data']['failures'] );
		} elseif ( $http_code >= 400 ) {
			$result['error'] = sprintf(
				/* translators: %d: HTTP status code */
				__( 'HTTP error %d from SMTP2GO', 'quill-smtp' ),
				$http_code
			);
		} else {
			$result['error'] = __( 'Unknown error from SMTP2GO', 'quill-smtp' );
		}

		return $result;
	}

	/**
	 * Log batch emails to QuillSMTP email log
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args  Original batch arguments
	 * @param array $recipients  Valid recipient emails
	 * @param mixed $result      API response (array on success, WP_Error on failure)
	 */
	protected function log_batch_emails( $batch_args, $recipients, $result ) {
		// Check if email logging function exists
		if ( ! function_exists( 'quillsmtp_get_email_log' ) ) {
			return;
		}

		// Get connection info from settings if not provided
		$connection_id = $batch_args['connection_id'] ?? '';
		$account_id    = $batch_args['account_id'] ?? '';

		// If no connection info, try to get from settings
		if ( empty( $connection_id ) || empty( $account_id ) ) {
			$settings    = get_option( 'quillsmtp_settings', array() );
			$connections = $settings['connections'] ?? [];

			// Find SMTP2GO connection from default or fallback
			foreach ( [ 'default_connection', 'fallback_connection' ] as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'smtp2go' ) {
						$connection_id = $settings[ $key ];
						$account_id    = $conn['account_id'] ?? '';
						break;
					}
				}
			}
		}

		$status   = is_wp_error( $result ) ? 'failed' : 'succeeded';
		$response = is_wp_error( $result ) ? $result->get_error_message() : $result;

		// Log one entry for the batch (not per recipient to avoid log spam)
		$subject     = $batch_args['subject'] ?? '';
		$body        = $batch_args['html_body'] ?? $batch_args['text_body'] ?? '';
		$headers     = $batch_args['headers'] ?? [];
		$attachments = [];
		$from        = $batch_args['sender'] ?? '';
		$recipients_data = [
			'to'       => implode( ', ', $recipients ),
			'cc'       => '',
			'bcc'      => '',
			'reply_to' => '',
		];

		quillsmtp_get_email_log()->handle(
			$subject . ' [Batch: ' . count( $recipients ) . ' recipients]',
			$body,
			$headers,
			$attachments,
			$from,
			$recipients_data,
			$status,
			'smtp2go',
			$connection_id,
			$account_id,
			$response
		);
	}
}
