<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\MailerSend;

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
	protected $api_token;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_token API key.
	 */
	public function __construct( $api_token ) {
		$this->api_token = $api_token;
	}

	/**
	 * Send email
	 *
	 * @param array $args Email arguments.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args ) {
		$response = wp_remote_post(
			'https://api.mailersend.com/v1/email',
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_token,
				],
				'body'    => wp_json_encode( $args ),
				'timeout' => 60,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 'Accepted' !== $response_message ) {
			return new WP_Error( 'mailersend_error', $response_message, $body );
		}

		$body = json_decode( $body, true );

		return $body;
	}

	/**
	 * Send bulk emails
	 *
	 * MailerSend's bulk email endpoint accepts an array of email objects,
	 * each with its own recipient and personalization data.
	 *
	 * @see https://developers.mailersend.com/api/v1/email.html#send-bulk-emails
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject (can contain {{var}} placeholders)
	 *     @type string $html                HTML body (can contain {{var}} placeholders)
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with personalization data
	 *     @type array  $tags                Tags for tracking (optional)
	 *     @type string $connection_id       Connection ID for logging (optional)
	 *     @type string $account_id          Account ID for logging (optional)
	 * }
	 *
	 * @return WP_Error|array
	 */
	public function send_batch( $batch_args ) {
		// Validate recipients
		if ( empty( $batch_args['to'] ) || ! is_array( $batch_args['to'] ) ) {
			return new WP_Error( 'invalid_recipients', __( 'Recipients array is required.', 'quill-smtp' ) );
		}

		// MailerSend limits: 500 emails per bulk request (5 for trial accounts)
		if ( count( $batch_args['to'] ) > 500 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 500 recipients per batch.', 'quill-smtp' ) );
		}

		$recipients = [];
		foreach ( $batch_args['to'] as $email ) {
			if ( ! is_email( $email ) ) {
				continue;
			}
			$recipients[] = $email;
		}

		if ( empty( $recipients ) ) {
			return new WP_Error( 'no_valid_recipients', __( 'No valid recipient emails found.', 'quill-smtp' ) );
		}

		// Build the from object
		$from = [
			'email' => $batch_args['from_email'] ?? '',
		];
		if ( ! empty( $batch_args['from_name'] ) ) {
			$from['name'] = $batch_args['from_name'];
		}

		// Build array of email objects for bulk sending
		$emails = [];

		foreach ( $recipients as $email ) {
			$email_object = [
				'from'    => $from,
				'to'      => [
					[
						'email' => $email,
					],
				],
				'subject' => $batch_args['subject'] ?? '',
			];

			// Add HTML body
			if ( ! empty( $batch_args['html'] ) ) {
				$email_object['html'] = $batch_args['html'];
			}

			// Add text body
			if ( ! empty( $batch_args['text'] ) ) {
				$email_object['text'] = $batch_args['text'];
			}

			// Add reply-to
			if ( ! empty( $batch_args['reply_to'] ) ) {
				$email_object['reply_to'] = [
					'email' => $batch_args['reply_to'],
				];
			}

			// Add personalization data for this recipient
			if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
				$email_object['personalization'] = [
					[
						'email' => $email,
						'data'  => $batch_args['recipient_variables'][ $email ],
					],
				];
			}

			// Add tags
			if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
				$email_object['tags'] = array_slice( $batch_args['tags'], 0, 5 ); // Max 5 tags
			}

			// Add tracking settings
			$email_object['settings'] = [
				'track_clicks' => true,
				'track_opens'  => true,
			];

			$emails[] = $email_object;
		}
		
		// Send bulk email request
		$response = wp_remote_post(
			'https://api.mailersend.com/v1/bulk-email',
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_token,
				],
				'body' => wp_json_encode([
					'messages' => $emails,
				]),
				'timeout' => 120,
			]
		);

		if ( is_wp_error( $response ) ) {
			$this->log_batch_emails( $batch_args, $recipients, $response );
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$body_data   = json_decode( $body, true );

		// Check for success (202 Accepted)
		if ( $status_code !== 202 ) {
			$error_message = $body_data['message'] ?? __( 'Unknown API error.', 'quill-smtp' );
			$error         = new WP_Error( 'mailersend_bulk_error', $error_message, [ 'status' => $status_code, 'body' => $body ] );
			$this->log_batch_emails( $batch_args, $recipients, $error );
			return $error;
		}

		// Build success result
		$result = [
			'id'            => $body_data['bulk_email_id'] ?? '',
			'message'       => $body_data['message'] ?? __( 'Bulk email is being processed.', 'quill-smtp' ),
			'bulk_email_id' => $body_data['bulk_email_id'] ?? '',
		];

		// Log the batch emails
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

			// Find MailerSend connection from default or fallback
			foreach ( [ 'default_connection', 'fallback_connection' ] as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'mailersend' ) {
						$connection_id = $settings[ $key ];
						$account_id    = $conn['account_id'] ?? '';
						break;
					}
				}
			}
		}

		$status   = is_wp_error( $result ) ? 'failed' : 'succeeded';
		$response = is_wp_error( $result ) ? $result->get_error_message() : $result;

		// Build from string
		$from_email = $batch_args['from_email'] ?? '';
		$from_name  = $batch_args['from_name'] ?? '';
		$from       = ! empty( $from_name ) ? "{$from_name} <{$from_email}>" : $from_email;

		// Log one entry for the batch (not per recipient to avoid log spam)
		$subject     = $batch_args['subject'] ?? '';
		$body        = $batch_args['html'] ?? $batch_args['text'] ?? '';
		$headers     = [];
		$attachments = [];
		$recipients_data = [
			'to'       => implode( ', ', $recipients ),
			'cc'       => '',
			'bcc'      => '',
			'reply_to' => $batch_args['reply_to'] ?? '',
		];

		quillsmtp_get_email_log()->handle(
			$subject . ' [Batch: ' . count( $recipients ) . ' recipients]',
			$body,
			$headers,
			$attachments,
			$from,
			$recipients_data,
			$status,
			'mailersend',
			$connection_id,
			$account_id,
			$response
		);
	}

	/**
	 * Get bulk email status
	 *
	 * @since 1.0.0
	 *
	 * @param string $bulk_email_id Bulk email ID returned from send_batch
	 *
	 * @return WP_Error|array
	 */
	public function get_bulk_status( $bulk_email_id ) {
		$response = wp_remote_get(
			'https://api.mailersend.com/v1/bulk-email/' . $bulk_email_id,
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_token,
				],
				'timeout' => 30,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$body_data   = json_decode( $body, true );

		if ( $status_code !== 200 ) {
			return new WP_Error( 'mailersend_status_error', __( 'Failed to get bulk email status.', 'quill-smtp' ), [ 'status' => $status_code ] );
		}

		return $body_data['data'] ?? $body_data;
	}
}
