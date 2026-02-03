<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SparkPost;

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
	 * Region
	 *
	 * @var string
	 */
	protected $region;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_key API key.
	 * @param array $region Region.
	 */
	public function __construct( $api_key, $region ) {
		$this->api_key = $api_key;
		$this->region  = $region;
	}

	/**
	 * Get the API URL based on region
	 *
	 * @since 1.0.0
	 *
	 * @return string API URL
	 */
	protected function get_api_url() {
		return 'eu' === $this->region
			? 'https://api.eu.sparkpost.com/api/v1'
			: 'https://api.sparkpost.com/api/v1';
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
			$this->get_api_url() . '/transmissions',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => $this->api_key,
				),
				'body'    => wp_json_encode(
					$args + array(
						'options' => array(
							'open_tracking'  => false,
							'click_tracking' => false,
							'transactional'  => true,
						),
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'empty_response', __( 'Empty response.', 'quillsmtp' ) );
		}

		$body = json_decode( $body, true );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response.', 'quillsmtp' ) );
		}

		if ( ! empty( $body['error'] ) ) {
			return new WP_Error( 'send_error', $body['error'] );
		}
		if( ! empty( $body['errors'] ) ) {
			return new WP_Error( 'send_error', $body['errors'][0]['message'] );
		}

		return $body;
	}

	/**
	 * Send batch emails
	 *
	 * SparkPost supports up to 1000 recipients per transmission using substitution_data
	 * for personalization.
	 *
	 * @see https://developers.sparkpost.com/api/transmissions/
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject (can contain {{key}} substitutions)
	 *     @type string $html                HTML body (can contain {{key}} substitutions)
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with substitution data
	 *     @type array  $tags                Tags/campaign for tracking (optional)
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

		// SparkPost limit: 1000 recipients per transmission
		if ( count( $batch_args['to'] ) > 1000 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 1000 recipients per batch.', 'quillsmtp' ) );
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

		// Build recipients array with substitution_data
		$sparkpost_recipients = array();
		foreach ( $recipients as $email ) {
			$recipient = array(
				'address' => array(
					'email' => $email,
				),
			);

			// Add substitution data for this recipient
			if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
				$recipient['substitution_data'] = $batch_args['recipient_variables'][ $email ];
			}

			$sparkpost_recipients[] = $recipient;
		}

		// Build the transmission payload
		$from_email = $batch_args['from_email'] ?? '';
		$from_name  = $batch_args['from_name'] ?? '';

		$payload = array(
			'recipients' => $sparkpost_recipients,
			'content'    => array(
				'from'    => array(
					'email' => $from_email,
					'name'  => $from_name,
				),
				'subject' => $batch_args['subject'] ?? '',
			),
			'options'    => array(
				'open_tracking'  => true,
				'click_tracking' => true,
				'transactional'  => true,
			),
		);

		// Add HTML content
		if ( ! empty( $batch_args['html'] ) ) {
			$payload['content']['html'] = $batch_args['html'];
		}

		// Add text content
		if ( ! empty( $batch_args['text'] ) ) {
			$payload['content']['text'] = $batch_args['text'];
		}

		// Add reply-to
		if ( ! empty( $batch_args['reply_to'] ) ) {
			$payload['content']['reply_to'] = $batch_args['reply_to'];
		}

		// Add custom headers
		if ( ! empty( $batch_args['headers'] ) && is_array( $batch_args['headers'] ) ) {
			$payload['content']['headers'] = $batch_args['headers'];
		}

		// Add campaign/tag
		if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
			$payload['campaign_id'] = $batch_args['tags'][0];
		}

		// Send the transmission
		$response = wp_remote_post(
			$this->get_api_url() . '/transmissions',
			array(
				'headers' => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => $this->api_key,
				),
				'body'    => wp_json_encode( $payload ),
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
		if ( ! empty( $body_data['errors'] ) ) {
			$error_message = $body_data['errors'][0]['message'] ?? __( 'Unknown API error.', 'quillsmtp' );
			$error         = new WP_Error(
				'sparkpost_error',
				$error_message,
				array(
					'status' => $status_code,
					'body'   => $body,
				)
			);
			$this->log_batch_emails( $batch_args, $recipients, $error );
			return $error;
		}

		// Success
		$result = array(
			'id'             => $body_data['results']['id'] ?? '',
			'message'        => __( 'Batch email sent successfully.', 'quillsmtp' ),
			'sent_count'     => $body_data['results']['total_accepted_recipients'] ?? count( $recipients ),
			'rejected_count' => $body_data['results']['total_rejected_recipients'] ?? 0,
			'failed'         => array(),
		);

		$this->log_batch_emails( $batch_args, $recipients, $result );

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
					if ( ( $conn['mailer'] ?? '' ) === 'sparkpost' ) {
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
			'sparkpost',
			$connection_id,
			$account_id,
			$response
		);
	}
}
