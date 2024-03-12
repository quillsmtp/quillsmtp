<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\PostMark\REST;

use WP_Error;
use WP_REST_Request;
use QuillSMTP\Mailer\Provider\REST\Account_Controller as Abstract_Account_Controller;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Creatable;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Gettable;
use QuillSMTP\Vendor\Postmark\PostmarkClient;

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
			'api_key'           => [
				'type'     => 'string',
				'required' => true,
			],
			'message_stream_id' => [
				'type'     => 'string',
				'required' => false,
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
			return new WP_Error( 'quillsmtp_postmark_api_key_missing', __( 'API key is missing.', 'quillsmtp' ) );
		}

		try {
			$client = new PostmarkClient( $api_key );
			$server = $client->getServer();
			return [
				'id'   => $account_name,
				'name' => $account_id,
			];
		} catch ( \Exception $e ) {
			return new WP_Error( 'quillsmtp_postmark_api_key_invalid', __( 'API key is invalid.', 'quillsmtp' ) );
		}
	}

}
