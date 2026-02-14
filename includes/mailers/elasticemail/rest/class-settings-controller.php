<?php
/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailers\ElasticEmail\REST;

use QuillSMTP\Mailer\Provider\REST\Settings_Controller as Abstract_Settings_Controller;

/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 */
class Settings_Controller extends Abstract_Settings_Controller {

	/**
	 * Retrieves schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_schema() {}

	/**
	 * Register controller routes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		parent::register_routes();
		// Get from emails using account id.
		register_rest_route(
			$this->namespace,
			"/{$this->rest_base}" . '/(?P<id>[^\/\?]+)/from-emails',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_account' ),
					'permission_callback' => array( $this, 'get_account_permissions_check' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get items
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_account( $request ) { // phpcs:ignore

		try {
			$account_id  = $request->get_param( 'id' );
			$account_api = $this->mailer->accounts->connect( $account_id );
			if ( is_wp_error( $account_api ) ) {
				throw new Exception( $account_api->get_error_message() );
			}

			$result = $account_api->get_account();

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$options = [
				[
					'value' => $result['data']['email'],
					'label' => $result['data']['email'],
				],
			];

			return new \WP_REST_Response(
				[
					'success' => true,
					'options' => $options,
				],
				200
			);
		} catch ( Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'ElasticEmail Getting User Profile Error', 'quill-smtp' ),
				array(
					'code'  => 'elasticemail_get_user_profile_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					],
				)
			);

			return new \WP_Error( 'elasticemail_get_user_profile_error', $e->getMessage() );
		}
	}

	/**
	 * Checks if a given request has access to get items.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_account_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}
}
