<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail\REST;

use QuillSMTP\Mailer\Provider\REST\Account_Controller as Abstract_Account_Controller;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Creatable;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Gettable;
use WP_Error;
use WP_REST_Request;

/**
 * Account_Controller class.
 *
 * @since 1.3.0
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
			'client_id'     => [
				'type'     => 'string',
				'required' => true,
			],
			'client_secret' => [
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
		$credentials   = $request->get_param( 'credentials' );
		$client_id     = $credentials['client_id'] ?? '';
		$client_secret = $credentials['client_secret'] ?? '';
		$account_name  = $request->get_param( 'name' );
		$account_id    = $request->get_param( 'id' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new WP_Error( 'invalid_credentials', __( 'Client ID and Client Secret are required.', 'quillsmtp' ) );
		}

		return [
			'id'   => $account_id,
			'name' => $account_name,
		];
	}

}
