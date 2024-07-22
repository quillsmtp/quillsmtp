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
		$response = wp_remote_request(
			'https://api.mailersend.com/v1/email',
			[
				'method'  => 'POST',
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
}
