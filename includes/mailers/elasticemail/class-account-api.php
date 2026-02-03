<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\ElasticEmail;

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
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_key API key.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Send email
	 *
	 * @param array  $args Email arguments.
	 * @param string $content_type Content type.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args, $content_type = 'application/json' ) {
		$response = wp_remote_request(
			'https://api.elasticemail.com/v2/email/send?apikey=' . $this->api_key,
			[
				'method'  => 'POST',
				'headers' => [
					'Content-Type' => $content_type,
				],
				'body'    => $args,
			]
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

		if ( ! isset( $body['success'] ) || ( isset( $body['success'] ) && ! $body['success'] ) ) {
			return new WP_Error( 'could_not_send', isset( $body['error'] ) ? $body['error'] : __( 'Could not send email.', 'quillsmtp' ) );
		}

		return $body;
	}

	/**
	 * Send batch emails using Elastic Email API v4
	 *
	 * Native bulk sending with personalization support.
	 * Uses {field} syntax for merge fields in subject and body.
	 *
	 * @see https://elasticemail.com/developers/api-documentation/rest-api#operation/emailsTransactionalPost
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     Batch email arguments.
	 *
	 *     @type string $from_email          Sender email address
	 *     @type string $from_name           Sender name
	 *     @type array  $to                  Array of recipient email addresses
	 *     @type string $subject             Email subject (can contain {field} merge tags)
	 *     @type string $html                HTML body (can contain {field} merge tags)
	 *     @type string $text                Plain text body (optional)
	 *     @type string $reply_to            Reply-to email (optional)
	 *     @type array  $recipient_variables Associative array keyed by email with merge field data
	 *     @type string $channel             Channel name for tracking (optional)
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

		// Elastic Email API v4 supports up to 1000 recipients per request
		if ( count( $batch_args['to'] ) > 1000 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 1000 recipients per batch.', 'quillsmtp' ) );
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

		// Build recipients array with MergeFields for personalization
		$api_recipients = [];
		foreach ( $recipients as $email ) {
			$recipient = [
				'Email' => $email,
			];

			// Add merge fields for this recipient
			if ( ! empty( $batch_args['recipient_variables'][ $email ] ) ) {
				$recipient['Fields'] = $batch_args['recipient_variables'][ $email ];
			}

			$api_recipients[] = $recipient;
		}

		// Build the API v4 payload
		$payload = [
			'Recipients' => $api_recipients,
			'Content'    => [
				'From'    => $batch_args['from_email'] ?? '',
				'Subject' => $batch_args['subject'] ?? '',
				'Body'    => [],
			],
		];

		// Add from name
		if ( ! empty( $batch_args['from_name'] ) ) {
			$payload['Content']['FromName'] = $batch_args['from_name'];
		}

		// Add HTML body
		if ( ! empty( $batch_args['html'] ) ) {
			$payload['Content']['Body'][] = [
				'ContentType' => 'HTML',
				'Content'     => $batch_args['html'],
			];
		}

		// Add text body
		if ( ! empty( $batch_args['text'] ) ) {
			$payload['Content']['Body'][] = [
				'ContentType' => 'PlainText',
				'Content'     => $batch_args['text'],
			];
		}

		// Add reply-to
		if ( ! empty( $batch_args['reply_to'] ) ) {
			$payload['Content']['ReplyTo'] = $batch_args['reply_to'];
		}

		// Add tracking options
		$payload['Options'] = [
			'TrackOpens'  => true,
			'TrackClicks' => true,
		];

		// Add channel/campaign for tracking
		if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
			$payload['Options']['ChannelName'] = $batch_args['tags'][0];
		}

		// Send via Elastic Email API v4
		$response = wp_remote_post(
			'https://api.elasticemail.com/v4/emails/transactional',
			[
				'headers' => [
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
					'X-ElasticEmail-ApiKey' => $this->api_key,
				],
				'body'    => wp_json_encode( $payload ),
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

		// Check for errors
		if ( $status_code >= 400 || ( isset( $body_data['Success'] ) && ! $body_data['Success'] ) ) {
			$error_message = $body_data['Error'] ?? $body_data['Message'] ?? __( 'Unknown API error.', 'quillsmtp' );
			$error         = new WP_Error( 'elasticemail_error', $error_message, [ 'status' => $status_code, 'body' => $body ] );
			$this->log_batch_emails( $batch_args, $recipients, $error );
			return $error;
		}

		// Success - API v4 returns TransactionID and MessageID
		$result = [
			'id'            => $body_data['TransactionID'] ?? $body_data['MessageID'] ?? '',
			'message'       => __( 'Batch email sent successfully.', 'quillsmtp' ),
			'sent_count'    => count( $recipients ),
			'failed'        => [],
			'transaction_id' => $body_data['TransactionID'] ?? '',
		];

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
			$connections = $settings['connections'] ?? [];

			foreach ( [ 'default_connection', 'fallback_connection' ] as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'elasticemail' ) {
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
		$headers         = [];
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
			'elasticemail',
			$connection_id,
			$account_id,
			$response
		);
	}

	/**
	 * Get user account
	 *
	 * @return object|WP_Error
	 */
	public function get_account() {
		$response = wp_remote_get(
			'https://api.elasticemail.com/v2/account/load?apikey=' . $this->api_key,
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json; charset=' . get_option( 'blog_charset' ),
					'Cache-Control' => 'no-cache',
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return new WP_Error( 'empty_response', __( 'Empty response.', 'quillsmtp' ) );
		}

		$body = json_decode( $body, true );

		if ( ! isset( $body['success'] ) || ( isset( $body['success'] ) && ! $body['success'] ) ) {
			return new WP_Error( 'invalid_api_key', __( 'Failed to get account.', 'quillsmtp' ) );
		}

		return $body;
	}
}
