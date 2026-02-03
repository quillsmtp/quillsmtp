<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\PostMark;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\Postmark\PostmarkClient;
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
	 * Message Stream ID
	 *
	 * @var string
	 */
	protected $message_stream_id;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 * @param string $message_stream_id Message Stream ID.
	 */
	public function __construct( $api_key, $message_stream_id ) {
		$this->api_key           = $api_key;
		$this->message_stream_id = $message_stream_id;
	}

	/**
	 * Get Client.
	 *
	 * @since 1.0.0
	 *
	 * @return PostmarkClient
	 */
	public function get_client() {
		return new PostmarkClient( $this->api_key );
	}

	/**
	 * Get Message Stream ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_message_stream_id() {
		return $this->message_stream_id;
	}

	/**
	 * Send batch emails
	 *
	 * Postmark supports up to 500 emails per batch request.
	 * Each email in the batch is a separate message with its own recipient.
	 *
	 * @see https://postmarkapp.com/developer/api/email-api#send-batch-emails
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject
	 *     @type string $html                HTML body
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with personalization data
	 *     @type string $tag                 Tag for tracking (optional)
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

		// Postmark limit: 500 messages per batch
		if ( count( $batch_args['to'] ) > 500 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 500 recipients per batch.', 'quillsmtp' ) );
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
		$from       = ! empty( $from_name ) ? "{$from_name} <{$from_email}>" : $from_email;

		// Build array of email messages for batch sending
		$messages = array();

		foreach ( $recipients as $email ) {
			$message = array(
				'From'    => $from,
				'To'      => $email,
				'Subject' => $batch_args['subject'] ?? '',
			);

			// Add HTML body
			if ( ! empty( $batch_args['html'] ) ) {
				$html = $batch_args['html'];
				// Replace personalization variables for this recipient
				if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
					foreach ( $batch_args['recipient_variables'][ $email ] as $key => $value ) {
						$html = str_replace( '{{' . $key . '}}', $value, $html );
					}
				}
				$message['HtmlBody'] = $html;
			}

			// Add text body
			if ( ! empty( $batch_args['text'] ) ) {
				$text = $batch_args['text'];
				// Replace personalization variables for this recipient
				if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
					foreach ( $batch_args['recipient_variables'][ $email ] as $key => $value ) {
						$text = str_replace( '{{' . $key . '}}', $value, $text );
					}
				}
				$message['TextBody'] = $text;
			}

			// Add reply-to
			if ( ! empty( $batch_args['reply_to'] ) ) {
				$message['ReplyTo'] = $batch_args['reply_to'];
			}

			// Add tag
			if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
				$message['Tag'] = $batch_args['tags'][0]; // Postmark supports one tag per message
			}

			// Add message stream
			if ( ! empty( $this->message_stream_id ) ) {
				$message['MessageStream'] = $this->message_stream_id;
			}

			// Add custom headers
			if ( ! empty( $batch_args['headers'] ) && is_array( $batch_args['headers'] ) ) {
				$message['Headers'] = array();
				foreach ( $batch_args['headers'] as $name => $value ) {
					$message['Headers'][] = array(
						'Name'  => $name,
						'Value' => $value,
					);
				}
			}

			// Enable tracking
			$message['TrackOpens'] = true;
			$message['TrackLinks'] = 'HtmlOnly';

			$messages[] = $message;
		}

		try {
			$client  = $this->get_client();
			$results = $client->sendEmailBatch( $messages );

			$sent_count = 0;
			$failed     = array();

			// Results is a DynamicResponseModel - iterate over it
			// Each result has: To, SubmittedAt, MessageID, ErrorCode, Message
			foreach ( $results as $index => $result ) {
				// Use property access (not getter methods) - DynamicResponseModel uses __get magic method
				if ( $result->Message === 'OK' ) {
					$sent_count++;
				} else {
					$failed[] = array(
						'email' => $recipients[ $index ] ?? '',
						'error' => $result->Message ?? 'Unknown error',
					);
				}
			}

			$response = array(
				'id'         => $results[0]->MessageID ?? '',
				'message'    => sprintf(
					/* translators: 1: sent count, 2: total count */
					__( 'Sent %1$d of %2$d emails successfully.', 'quillsmtp' ),
					$sent_count,
					count( $recipients )
				),
				'sent_count' => $sent_count,
				'failed'     => $failed,
			);

			$this->log_batch_emails( $batch_args, $recipients, $sent_count > 0 ? $response : new WP_Error( 'all_failed', __( 'All emails failed to send.', 'quillsmtp' ) ) );

			if ( $sent_count === 0 ) {
				return new WP_Error(
					'batch_send_failed',
					__( 'All emails in batch failed to send.', 'quillsmtp' ),
					array( 'failed' => $failed )
				);
			}

			return $response;

		} catch ( \Exception $e ) {
			$error = new WP_Error(
				'postmark_exception',
				$e->getMessage(),
				array( 'code' => $e->getCode() )
			);

			$this->log_batch_emails( $batch_args, $recipients, $error );

			return $error;
		}
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
					if ( ( $conn['mailer'] ?? '' ) === 'postmark' ) {
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
			'postmark',
			$connection_id,
			$account_id,
			$response
		);
	}
}
