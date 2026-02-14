<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\ElasticEmail\REST;

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
			'api_key' => [
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
			'https://api.elasticemail.com/v2/account/load?apikey=' . $api_key,
			[
				'method'  => 'GET',
				'headers' => [
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
				],
			]
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );
		if ( ! isset( $body['success'] ) || ( isset( $body['success'] ) && ! $body['success'] ) ) {
			return new WP_Error( 'invalid_api_key', __( 'Invalid API key.', 'quill-smtp' ) );
		}

		if ( empty( $account_id ) ) {
			$account_id = $body['data']['publicaccountid'];
		}

		return [
			'id'   => $account_id,
			'name' => $account_name,
		];
	}

}
