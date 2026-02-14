<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTPcom\REST;

use Exception;
use QuillSMTP\Mailer\Provider\REST\Account_Controller as Abstract_Account_Controller;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Creatable;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Gettable;
use WP_Error;
use WP_REST_Request;

/**
 * Account_Controller class.
 *
 * @since 1.0.0
 */
class Account_Controller extends Abstract_Account_Controller {
	use Account_Controller_Gettable, Account_Controller_Creatable;

	/**
	 * Register controller routes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		parent::register_routes();

		$this->register_gettable_route();
		$this->register_creatable_route();
	}

	/**
	 * Get credentials schema
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_credentials_schema() {
		return [
			'api_key'     => [
				'type'     => 'string',
				'required' => true,
			],
			'sender_name' => [
				'type'     => 'string',
				'required' => true,
			],
		];
	}

	/**
	 * Get account id & name
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error array of id & name if success.
	 */
	protected function get_account_info( $request ) {
		$credentials  = $request->get_param( 'credentials' );
		$api_key      = $credentials['api_key'] ?? '';
		$account_name = $request->get_param( 'name' );
		$account_id   = $request->get_param( 'id' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'invalid_api_key', __( 'Invalid API key.', 'quill-smtp' ) );
		}

		$api_key  = sanitize_text_field( $api_key );
		$response = wp_remote_request(
			'https://api.smtp.com/v4/account',
			[
				'method'  => 'GET',
				'headers' => [
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				],
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

		if ( $body['status'] !== 'success' ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response.', 'quill-smtp' ) );

		}

		return [
			'id'   => $account_id,
			'name' => $account_name,
		];
	}

}
