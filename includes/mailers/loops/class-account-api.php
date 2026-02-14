<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Loops;

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
	 * Transactional ID
	 *
	 * @var string
	 *
	 * @since 1.3.0
	 */
	protected $transactional_id;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 * @param string $transactional_id Transactional ID.
	 */
	public function __construct( $api_key, $transactional_id ) {
		$this->api_key          = $api_key;
		$this->transactional_id = $transactional_id;
	}

	/**
	 * Send email
	 *
	 * @param array $args Email arguments.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args ) {
		$args['transactionalId'] = $this->transactional_id;
		$response                = wp_remote_request(
			'https://app.loops.so/api/v1/transactional',
			[
				'method'  => 'POST',
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_key,
				],
				'body'    => wp_json_encode( $args ),
				'timeout' => 60,
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

		return $body;
	}
}
