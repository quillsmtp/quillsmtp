<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTPcom;

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
	 * Sender name
	 *
	 * @var string
	 */
	protected $sender_name;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_key API key.
	 * @param array $sender_name Sender name.
	 */
	public function __construct( $api_key, $sender_name ) {
		$this->api_key     = $api_key;
		$this->sender_name = $sender_name;
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
			'https://api.smtp.com/v4/messages',
			[
				'method'  => 'POST',
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_key,
				],
				'body'    => wp_json_encode( $args ),
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

		if ( ! empty( $body['error'] ) ) {
			return new WP_Error( 'send_error', $body['error'] );
		}

		return $body;
	}

	/**
	 * Get sender name
	 *
	 * @return string
	 */
	public function get_sender_name() {
		return $this->sender_name;
	}
}
