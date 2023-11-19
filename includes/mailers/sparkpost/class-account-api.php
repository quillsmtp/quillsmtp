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
	 * Send email
	 *
	 * @param array $args Email arguments.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args ) {
		$response = wp_remote_request(
			'eu' === $this->region ? 'https://api.eu.sparkpost.com/api/v1/transmissions' : 'https://api.sparkpost.com/api/v1/transmissions',
			[
				'method'  => 'POST',
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => $this->api_key,
				],
				'body'    => wp_json_encode(
					$args + [
						'options' => [
							'open_tracking'  => false,
							'click_tracking' => false,
							'transactional'  => true,
						],
					]
				),
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

		if ( ! empty( $body['error'] ) ) {
			return new WP_Error( 'send_error', $body['error'] );
		}

		return $body;
	}
}
