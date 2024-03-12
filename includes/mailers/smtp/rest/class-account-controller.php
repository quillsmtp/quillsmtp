<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTP\REST;

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
			'smtp_host'      => [
				'type'     => 'string',
				'required' => true,
			],
			'smtp_port'      => [
				'type'     => [ 'integer', 'string' ],
				'required' => true,
			],
			'encryption'     => [
				'type'     => 'string',
				'required' => true,
				'enum'     => [
					'none',
					'ssl',
					'tls',
				],
			],
			'auto_tls'       => [
				'type' => 'boolean',
			],
			'authentication' => [
				'type' => 'boolean',
			],
			'username'       => [
				'type' => 'string',
			],
			'password'       => [
				'type' => 'string',
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
		$credentials    = $request->get_param( 'credentials' );
		$smtp_host      = $credentials['smtp_host'] ?? '';
		$smtp_port      = $credentials['smtp_port'] ?? '';
		$encryption     = $credentials['encryption'] ?? '';
		$authentication = $credentials['authentication'] ?? false;
		$username       = $credentials['username'] ?? '';
		$password       = $credentials['password'] ?? '';
		$account_name   = $request->get_param( 'name' );
		$account_id     = $request->get_param( 'id' );

		if ( empty( $smtp_host ) ) {
			return new WP_Error( 'quillsmtp_rest_invalid_smtp_host', __( 'Invalid SMTP Host.', 'quillsmtp' ), array( 'status' => 400 ) );
		}

		if ( empty( $smtp_port ) ) {
			return new WP_Error( 'quillsmtp_rest_invalid_smtp_port', __( 'Invalid SMTP Port.', 'quillsmtp' ), array( 'status' => 400 ) );
		}

		if ( empty( $encryption ) ) {
			return new WP_Error( 'quillsmtp_rest_invalid_encryption', __( 'Invalid Encryption.', 'quillsmtp' ), array( 'status' => 400 ) );
		}

		if ( $authentication && empty( $username ) ) {
			return new WP_Error( 'quillsmtp_rest_invalid_username', __( 'Invalid Username.', 'quillsmtp' ), array( 'status' => 400 ) );
		}

		if ( $authentication && empty( $password ) ) {
			return new WP_Error( 'quillsmtp_rest_invalid_password', __( 'Invalid Password.', 'quillsmtp' ), array( 'status' => 400 ) );
		}

		return array(
			'id'   => $account_id,
			'name' => $account_name,
		);
	}

}
