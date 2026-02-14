<?php
/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailers\Gmail\REST;

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
	public function get_schema() {
		$schema = [
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'settings',
			'type'                 => 'object',
			'context'              => [ 'view' ],
			'properties'           => [
				'app' => [
					'type'       => 'object',
					'context'    => [ 'view' ],
					'properties' => [
						'client_id'     => [
							'type'     => 'string',
							'required' => true,
							'context'  => [ 'view' ],
						],
						'client_secret' => [
							'type'     => 'string',
							'required' => true,
							'context'  => [ 'view' ],
						],
					],
				],
			],
			'additionalProperties' => [
				'context' => [],
			],
		];

		return rest_default_additional_properties_to_false( $schema );
	}

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
					'callback'            => array( $this, 'get_profiles' ),
					'permission_callback' => array( $this, 'get_profiles_permissions_check' ),
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
	public function get_profiles( $request ) { // phpcs:ignore

		try {
			$account_id  = $request->get_param( 'id' );
			$account_api = $this->mailer->accounts->connect( $account_id );
			if ( is_wp_error( $account_api ) ) {
				throw new Exception( $account_api->get_error_message() );
			}

			$result = $account_api->get_profile();
			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$options = [
				[
					'value' => $result->emailAddress,
					'label' => $result->emailAddress,
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
				esc_html__( 'Gmail Getting User Profile Error', 'quill-smtp' ),
				array(
					'code'  => 'gmail_get_user_profile_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					],
				)
			);

			return new \WP_Error( 'gmail_get_user_profile_error', $e->getMessage() );
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
	public function get_profiles_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}
}
