<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Mailgun\REST;

use WP_Error;
use WP_REST_Request;
use QuillSMTP\Mailer\Provider\REST\Account_Controller as Abstract_Account_Controller;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Creatable;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Gettable;

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
			'api_key'     => [
				'type'     => 'string',
				'required' => true,
			],
			'domain_name' => [
				'type'     => 'string',
				'required' => true,
			],
			'region'      => [
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
		$credentials = $request->get_param( 'credentials' );
		$api_key     = $credentials['api_key'] ?? '';
		$domain_name = $credentials['domain_name'] ?? '';
		$region      = $credentials['region'] ?? '';

		if ( empty( $api_key ) ) {
			return new WP_Error( 'quillsmtp_mailgun_api_key_missing', __( 'API key is missing.', 'quillsmtp' ) );
		}

		if ( empty( $domain_name ) ) {
			return new WP_Error( 'quillsmtp_mailgun_domain_name_missing', __( 'Domain name is missing.', 'quillsmtp' ) );
		}

		if ( empty( $region ) ) {
			return new WP_Error( 'quillsmtp_mailgun_region_missing', __( 'Region is missing.', 'quillsmtp' ) );
		}
		$response = wp_remote_request(
			'eu' === $region ? 'https://api.eu.mailgun.net/v3/domains/' . $domain_name : 'https://api.mailgun.net/v3/domains/' . $domain_name,
			[
				'method'  => 'GET',
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $api_key ),
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $body['message'] ) ) {
			return new WP_Error( 'quillsmtp_mailgun_error', $body['message'] );
		}

		return [
			'id'   => $body['domain']['id'],
			'name' => $body['domain']['name'],
		];
	}

}
