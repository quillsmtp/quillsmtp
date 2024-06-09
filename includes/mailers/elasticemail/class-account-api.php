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
