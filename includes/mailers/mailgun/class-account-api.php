<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Mailgun;

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
	 * Domain Name
	 *
	 * @var string
	 */
	protected $domain_name;

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
	 * @param string $api_key API key.
	 * @param string $domain_name Domain name.
	 * @param string $region Region.
	 */
	public function __construct( $api_key, $domain_name, $region ) {
		$this->api_key     = $api_key;
		$this->domain_name = $domain_name;
		$this->region      = $region;
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
			? 'https://api.eu.mailgun.net/v3/' . $this->domain_name
			: 'https://api.mailgun.net/v3/' . $this->domain_name;
	}

	/**
	 * Send email
	 *
	 * @param array  $args Email arguments.
	 * @param string $content_type Content type.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args, $content_type = '' ) {
		$response = wp_remote_request(
			$this->get_api_url() . '/messages',
			[
				'method'  => 'POST',
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $this->api_key ),
					'Content-Type'  => $content_type,
				],
				'body'    => $args,
			]
		);

		return $this->parse_response( $response );
	}

	/**
	 * Send batch email (multiple recipients in a single API call)
	 *
	 * Uses Mailgun's recipient-variables feature for personalization.
	 * Max 1,000 recipients per batch (Mailgun limit).
	 *
	 * @see https://documentation.mailgun.com/docs/mailgun/user-manual/sending-messages/batch-sending
	 *
	 * @since 1.0.0
	 *
	 * @param array $batch_args {
	 *     @type string $from                 Sender address (e.g., "Name <email@domain.com>")
	 *     @type array  $to                   Array of recipient email addresses
	 *     @type string $subject              Email subject (can contain %recipient.key% variables)
	 *     @type string $html                 HTML body (can contain %recipient.key% variables)
	 *     @type string $text                 Plain text body (optional)
	 *     @type array  $recipient_variables  Associative array keyed by email with variable values
	 *     @type array  $headers              Custom headers (optional)
	 *     @type array  $tags                 Tags for tracking (optional)
	 *     @type string $connection_id        Connection ID for logging (optional)
	 *     @type string $account_id           Account ID for logging (optional)
	 * }
	 *
	 * @return WP_Error|array
	 */
	public function send_batch( $batch_args ) {
		// ----------------------------
		// Validate recipients
		// ----------------------------
		if ( empty( $batch_args['to'] ) || ! is_array( $batch_args['to'] ) ) {
			return new WP_Error( 'invalid_recipients', __( 'Recipients array is required.', 'quillsmtp' ) );
		}

		if ( count( $batch_args['to'] ) > 1000 ) {
			return new WP_Error( 'too_many_recipients', __( 'Maximum 1000 recipients per batch.', 'quillsmtp' ) );
		}

		$recipients = [];
		foreach ( $batch_args['to'] as $email ) {
			if ( ! is_email( $email ) ) {
				continue; // skip invalid emails
			}
			$recipients[] = $email;
		}

		if ( empty( $recipients ) ) {
			return new WP_Error( 'no_valid_recipients', __( 'No valid recipient emails found.', 'quillsmtp' ) );
		}

		// ----------------------------
		// Build recipient variables
		// ----------------------------
		$recipient_variables = [];

		if ( ! empty( $batch_args['recipient_variables'] ) && is_array( $batch_args['recipient_variables'] ) ) {
			// Use provided recipient variables
			foreach ( $recipients as $email ) {
				if ( isset( $batch_args['recipient_variables'][ $email ] ) ) {
					$recipient_variables[ $email ] = $batch_args['recipient_variables'][ $email ];
				} else {
					// Provide empty array for recipients without variables
					$recipient_variables[ $email ] = [];
				}
			}
		} else {
			// No recipient variables provided - create empty entries
			foreach ( $recipients as $email ) {
				$recipient_variables[ $email ] = [];
			}
		}

		// ----------------------------
		// Build Mailgun payload using http_build_query for proper array handling
		// ----------------------------
		$body_params = [
			'from'                => $batch_args['from'] ?? '',
			'subject'             => $batch_args['subject'] ?? '',
			'recipient-variables' => wp_json_encode( $recipient_variables ),
		];

		if ( ! empty( $batch_args['html'] ) ) {
			$body_params['html'] = $batch_args['html'];
		}

		if ( ! empty( $batch_args['text'] ) ) {
			$body_params['text'] = $batch_args['text'];
		}

		// ----------------------------
		// Custom headers
		// ----------------------------
		if ( ! empty( $batch_args['headers'] ) && is_array( $batch_args['headers'] ) ) {
			foreach ( $batch_args['headers'] as $name => $value ) {
				$body_params[ 'h:' . $name ] = $value;
			}
		}

		// ----------------------------
		// Tracking options
		// ----------------------------
		$body_params['o:tracking']        = 'yes';
		$body_params['o:tracking-clicks'] = 'htmlonly';
		$body_params['o:tracking-opens']  = 'yes';

		// ----------------------------
		// Build the request body string manually to handle multiple 'to' and 'o:tag' params
		// Mailgun expects: to=email1&to=email2&to=email3 (not to[0]=email1&to[1]=email2)
		// ----------------------------
		$body_string = http_build_query( $body_params );

		// Add multiple 'to' parameters (one for each recipient)
		foreach ( $recipients as $email ) {
			$body_string .= '&to=' . rawurlencode( $email );
		}

		// Add tags (multiple o:tag parameters)
		if ( ! empty( $batch_args['tags'] ) && is_array( $batch_args['tags'] ) ) {
			foreach ( $batch_args['tags'] as $tag ) {
				$body_string .= '&o:tag=' . rawurlencode( $tag );
			}
		}

		// ----------------------------
		// Send request
		// ----------------------------
		$response = wp_remote_post(
			$this->get_api_url() . '/messages',
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $this->api_key ),
					'Content-Type'  => 'application/x-www-form-urlencoded',
				],
				'body'    => $body_string,
				'timeout' => 120,
			]
		);

		$result = $this->parse_response( $response );

		// ----------------------------
		// Log emails if logging info provided
		// ----------------------------
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

			// Find Mailgun connection from default or fallback
			foreach ( [ 'default_connection', 'fallback_connection' ] as $key ) {
				if ( ! empty( $settings[ $key ] ) && isset( $connections[ $settings[ $key ] ] ) ) {
					$conn = $connections[ $settings[ $key ] ];
					if ( ( $conn['mailer'] ?? '' ) === 'mailgun' ) {
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
		$body        = $batch_args['html'] ?? $batch_args['text'] ?? '';
		$headers     = $batch_args['headers'] ?? [];
		$attachments = [];
		$from        = $batch_args['from'] ?? '';
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
			'mailgun',
			$connection_id,
			$account_id,
			$response
		);
	}


	/**
	 * Parse API response
	 *
	 * @since 1.0.0
	 *
	 * @param array|WP_Error $response HTTP response
	 *
	 * @return WP_Error|array Parsed response or error
	 */
	protected function parse_response( $response ) {
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

		// Check HTTP status code for errors
		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code >= 400 ) {
			$error_message = $body['message'] ?? __( 'Unknown API error.', 'quillsmtp' );
			return new WP_Error( 'api_error', $error_message, [ 'status' => $status_code ] );
		}

		return $body;
	}
}
