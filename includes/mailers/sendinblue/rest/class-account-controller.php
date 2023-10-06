<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendInBlue\REST;

use QuillSMTP\Vendor\Brevo\Client\Configuration;
use QuillSMTP\Vendor\Brevo\Client\Api\AccountApi;
use QuillSMTP\Vendor\GuzzleHttp\Client as GuzzleClient;
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
			'api_key'        => [
				'type'     => 'string',
				'required' => true,
			],
			'sending_domain' => [
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
		$api_key = $request->get_param( 'api_key' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'quillsmtp_sendinblue_api_key_missing', __( 'API key is missing.', 'quillsmtp' ) );
		}

		$config       = Configuration::getDefaultConfiguration()->setApiKey( 'api-key', $api_key );
		$api_instance = new AccountApi( GuzzleClient(), $config );

		try {
			$result = $api_instance->getAccount();
			error_log( wp_json_encode( $result ) );
			return [
				// 'id'   => $result->getId(),
				// 'name' => $result->getName(),
			];
		} catch ( \Exception $e ) {
			return new WP_Error( 'quillsmtp_sendinblue_api_key_invalid', __( 'API key is invalid.', 'quillsmtp' ) );
		}
	}

}
