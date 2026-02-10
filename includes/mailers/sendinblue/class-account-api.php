<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendInBlue;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\Brevo\Client\Configuration;
use QuillSMTP\Vendor\GuzzleHttp\Client as GuzzleClient;
use QuillSMTP\Vendor\Brevo\Client\Api\TransactionalEmailsApi;
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
	 * Sending domain.
	 *
	 * @var string
	 */
	protected $sending_domain;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_key API key.
	 * @param array $sending_domain Sending domain.
	 */
	public function __construct( $api_key, $sending_domain ) {
		$this->api_key        = $api_key;
		$this->sending_domain = $sending_domain;
	}

	/**
	 * Get Brevo api_instance
	 *
	 * @since 1.0.0
	 *
	 * @return TransactionalEmailsApi
	 */
	public function get_api_instance() {
		$config       = Configuration::getDefaultConfiguration()->setApiKey( 'api-key', $this->api_key );
		$api_instance = new TransactionalEmailsApi( new GuzzleClient(), $config );

		return $api_instance;
	}

	/**
	 * Send batch emails
	 *
	 * Brevo (Sendinblue) doesn't have a native batch API like Mailgun.
	 * This method sends individual emails in a loop but handles them efficiently.
	 * For true bulk sending, Brevo recommends using their Campaign API.
	 *
	 * @see https://developers.brevo.com/reference/sendtransacemail
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject (can contain {{params.key}} placeholders)
	 *     @type string $html                HTML body (can contain {{params.key}} placeholders)
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with personalization data
	 *     @type array  $tags                Tags for tracking (optional)
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

		// Brevo recommends max 99 emails per batch for transactional
		// For larger volumes, they recommend using Campaign API
		if ( count( $batch_args['to'] ) > 500 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 500 recipients per batch.', 'quillsmtp' ) );
		}

		$recipients = [];
		foreach ( $batch_args['to'] as $email ) {
			if ( ! is_email( $email ) ) {
				continue;
			}
			$recipients[] = $email;
		}

		if ( empty( $recipients ) ) {
			return new WP_Error( 'no_valid_recipients', __( 'No valid recipient emails found.', 'quillsmtp' ) );
		}

		// Build the sender object
		$sender = [
			'email' => $batch_args['from_email'] ?? '',
		];
		if ( ! empty( $batch_args['from_name'] ) ) {
			$sender['name'] = $batch_args['from_name'];
		}

		// Track results
		$sent_count   = 0;
		$failed       = [];
		$message_ids  = [];

		// Get API instance
		$api_instance = $this->get_api_instance();

		// Send to each recipient individually with personalization
		foreach ( $recipients as $email ) {
			// Build the email body
			$email_body = [
				'sender'  => $sender,
				'to'      => [
					[
						'email' => $email,
					],
				],
				'subject' => $batch_args['subject'] ?? '',
			];

			// Add HTML content
			if ( ! empty( $batch_args['html'] ) ) {
				$email_body['htmlContent'] = $batch_args['html'];
			}

			// Add text content
			if ( ! empty( $batch_args['text'] ) ) {
				$email_body['textContent'] = $batch_args['text'];
			}

			// Add reply-to
			if ( ! empty( $batch_args['reply_to'] ) ) {
				$email_body['replyTo'] = [
					'email' => $batch_args['reply_to'],
				];
			}

			// Add personalization params for this recipient
			if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
				$email_body['params'] = $batch_args['recipient_variables'][ $email ];
			}

			// Add tags
			if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
				$email_body['tags'] = array_slice( $batch_args['tags'], 0, 10 ); // Brevo max 10 tags
			}

			// Add custom headers
			if ( ! empty( $batch_args['headers'] ) && is_array( $batch_args['headers'] ) ) {
				$email_body['headers'] = $batch_args['headers'];
			}

			try {
				$send_smtp_email = new \QuillSMTP\Vendor\Brevo\Client\Model\SendSmtpEmail( $email_body );
				$result          = $api_instance->sendTransacEmail( $send_smtp_email );

				if ( $result->getMessageId() ) {
					$sent_count++;
					$message_ids[] = $result->getMessageId();
				} else {
					$failed[] = [
						'email' => $email,
						'error' => __( 'No message ID returned', 'quillsmtp' ),
					];
				}
			} catch ( \Exception $e ) {
				$failed[] = [
					'email' => $email,
					'error' => $e->getMessage(),
				];
			}
		}

		// Build result
		$result = [
			'id'          => ! empty( $message_ids ) ? $message_ids[0] : '',
			'message'     => sprintf(
				/* translators: 1: sent count, 2: total count */
				__( 'Sent %1$d of %2$d emails successfully.', 'quillsmtp' ),
				$sent_count,
				count( $recipients )
			),
			'sent_count'  => $sent_count,
			'failed'      => $failed,
			'message_ids' => $message_ids,
		];

		// Log the batch
		$this->log_batch_emails( $batch_args, $recipients, $sent_count > 0 ? $result : new WP_Error( 'all_failed', __( 'All emails failed to send.', 'quillsmtp' ) ) );

		// Return error if all failed
		if ( $sent_count === 0 ) {
			return new WP_Error(
				'batch_send_failed',
				__( 'All emails in batch failed to send.', 'quillsmtp' ),
				[ 'failed' => $failed ]
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

			// Find Sendinblue/Brevo connection from default or fallback
			foreach ( [ 'default_connection', 'fallback_connection' ] as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'sendinblue' ) {
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
		$subject         = $batch_args['subject'] ?? '';
		$body            = $batch_args['html'] ?? $batch_args['text'] ?? '';
		$headers         = $batch_args['headers'] ?? [];
		$attachments     = [];
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
			'sendinblue',
			$connection_id,
			$account_id,
			$response
		);
	}
}
