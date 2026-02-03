<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Mailjet;

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
	 * Secret key
	 *
	 * @var string
	 */
	protected $secret_key;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 * @param string $secret_key Secret key.
	 */
	public function __construct( $api_key, $secret_key ) {
		$this->api_key    = $api_key;
		$this->secret_key = $secret_key;
	}

	/**
	 * Send email
	 *
	 * @param array $args Email arguments.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args ) {
		$response = wp_remote_request(
			'https://api.mailjet.com/v3/send',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->secret_key ),
				),
				'body'    => wp_json_encode( $args ),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'empty_response', __( 'Empty response.', 'quillsmtp-pro' ) );
		}

		$body = json_decode( $body, true );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response.', 'quillsmtp-pro' ) );
		}

		return $body;
	}

	/**
	 * Send batch emails
	 *
	 * Mailjet v3.1 API supports up to 50 messages per request.
	 * Each message can have its own recipient and variables.
	 *
	 * @see https://dev.mailjet.com/email/guides/send-api-v31/
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject (can contain {{var:key}} variables)
	 *     @type string $html                HTML body (can contain {{var:key}} variables)
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with variable data
	 *     @type array  $tags                Custom ID/campaign for tracking (optional)
	 *     @type array  $headers             Custom headers (optional)
	 *     @type string $connection_id       Connection ID for logging (optional)
	 *     @type string $account_id          Account ID for logging (optional)
	 * }
	 *
	 * @return WP_Error|array
	 */
	public function send_batch( $batch_args ) {
		// Validate recipients
		if ( empty( $batch_args['to'] ) || ! is_array( $batch_args['to'] ) ) {
			return new WP_Error( 'invalid_recipients', __( 'Recipients array is required.', 'quillsmtp' ) );
		}

		// Mailjet limit: 50 messages per request
		if ( count( $batch_args['to'] ) > 50 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 50 recipients per batch.', 'quillsmtp' ) );
		}

		$recipients = array();
		foreach ( $batch_args['to'] as $email ) {
			if ( ! is_email( $email ) ) {
				continue;
			}
			$recipients[] = $email;
		}

		if ( empty( $recipients ) ) {
			return new WP_Error( 'no_valid_recipients', __( 'No valid recipient emails found.', 'quillsmtp' ) );
		}

		// Build from address
		$from_email = $batch_args['from_email'] ?? '';
		$from_name  = $batch_args['from_name'] ?? '';

		// Build messages array for Mailjet v3.1 API
		$messages = array();

		foreach ( $recipients as $email ) {
			$message = array(
				'From'    => array(
					'Email' => $from_email,
					'Name'  => $from_name,
				),
				'To'      => array(
					array(
						'Email' => $email,
					),
				),
				'Subject' => $batch_args['subject'] ?? '',
			);

			// Add HTML part
			if ( ! empty( $batch_args['html'] ) ) {
				$message['HTMLPart'] = $batch_args['html'];
			}

			// Add text part
			if ( ! empty( $batch_args['text'] ) ) {
				$message['TextPart'] = $batch_args['text'];
			}

			// Add reply-to
			if ( ! empty( $batch_args['reply_to'] ) ) {
				$message['ReplyTo'] = array(
					'Email' => $batch_args['reply_to'],
				);
			}

			// Add variables for this recipient
			if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
				$message['Variables'] = $batch_args['recipient_variables'][ $email ];
			}

			// Add custom ID/campaign
			if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
				$message['CustomID'] = $batch_args['tags'][0];
			}

			// Add custom headers
			if ( ! empty( $batch_args['headers'] ) && is_array( $batch_args['headers'] ) ) {
				$message['Headers'] = $batch_args['headers'];
			}

			// Enable tracking
			$message['TrackOpens']  = 'enabled';
			$message['TrackClicks'] = 'enabled';

			$messages[] = $message;
		}

		// Send via Mailjet v3.1 API
		$response = wp_remote_post(
			'https://api.mailjet.com/v3.1/send',
			array(
				'headers' => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->secret_key ),
				),
				'body'    => wp_json_encode( array( 'Messages' => $messages ) ),
				'timeout' => 120,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log_batch_emails( $batch_args, $recipients, $response );
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$body_data   = json_decode( $body, true );

		// Check for errors
		if ( $status_code >= 400 || empty( $body_data['Messages'] ) ) {
			$error_message = $body_data['ErrorMessage'] ?? __( 'Unknown API error.', 'quillsmtp' );
			$error         = new WP_Error(
				'mailjet_error',
				$error_message,
				array(
					'status' => $status_code,
					'body'   => $body,
				)
			);
			$this->log_batch_emails( $batch_args, $recipients, $error );
			return $error;
		}

		// Count successes and failures
		$sent_count = 0;
		$failed     = array();

		foreach ( $body_data['Messages'] as $index => $msg_result ) {
			if ( $msg_result['Status'] === 'success' ) {
				$sent_count++;
			} else {
				$failed[] = array(
					'email' => $recipients[ $index ] ?? '',
					'error' => $msg_result['Errors'][0]['ErrorMessage'] ?? 'Unknown error',
				);
			}
		}

		$result = array(
			'id'         => $body_data['Messages'][0]['To'][0]['MessageID'] ?? '',
			'message'    => sprintf(
				/* translators: 1: sent count, 2: total count */
				__( 'Sent %1$d of %2$d emails successfully.', 'quillsmtp' ),
				$sent_count,
				count( $recipients )
			),
			'sent_count' => $sent_count,
			'failed'     => $failed,
		);

		$this->log_batch_emails( $batch_args, $recipients, $sent_count > 0 ? $result : new WP_Error( 'all_failed', __( 'All emails failed to send.', 'quillsmtp' ) ) );

		if ( $sent_count === 0 ) {
			return new WP_Error(
				'batch_send_failed',
				__( 'All emails in batch failed to send.', 'quillsmtp' ),
				array( 'failed' => $failed )
			);
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
		if ( ! function_exists( 'quillsmtp_get_email_log' ) ) {
			return;
		}

		$connection_id = $batch_args['connection_id'] ?? '';
		$account_id    = $batch_args['account_id'] ?? '';

		if ( empty( $connection_id ) || empty( $account_id ) ) {
			$settings    = get_option( 'quillsmtp_settings', array() );
			$connections = $settings['connections'] ?? array();

			foreach ( array( 'default_connection', 'fallback_connection' ) as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'mailjet' ) {
						$connection_id = $settings[ $key ];
						$account_id    = $conn['account_id'] ?? '';
						break;
					}
				}
			}
		}

		$status   = is_wp_error( $result ) ? 'failed' : 'succeeded';
		$response = is_wp_error( $result ) ? $result->get_error_message() : $result;

		$from_email = $batch_args['from_email'] ?? '';
		$from_name  = $batch_args['from_name'] ?? '';
		$from       = ! empty( $from_name ) ? "{$from_name} <{$from_email}>" : $from_email;

		$subject         = $batch_args['subject'] ?? '';
		$body            = $batch_args['html'] ?? $batch_args['text'] ?? '';
		$headers         = $batch_args['headers'] ?? array();
		$attachments     = array();
		$recipients_data = array(
			'to'       => implode( ', ', $recipients ),
			'cc'       => '',
			'bcc'      => '',
			'reply_to' => $batch_args['reply_to'] ?? '',
		);

		quillsmtp_get_email_log()->handle(
			$subject . ' [Batch: ' . count( $recipients ) . ' recipients]',
			$body,
			$headers,
			$attachments,
			$from,
			$recipients_data,
			$status,
			'mailjet',
			$connection_id,
			$account_id,
			$response
		);
	}
}
