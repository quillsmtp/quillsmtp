<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\REST;

use Exception;
use QuillForms\Addon\Provider\REST\Account_Controller as Abstract_Account_Controller;
use WP_Error;
use WP_REST_Request;

/**
 * Account_Controller class.
 *
 * @since 1.3.0
 */
class Account_Controller extends Abstract_Account_Controller {

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

		return [
			'id'   => '',
			'name' => '',
		];
	}

}
