<?php
/**
 * REST_Settings_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\REST_API\Controllers\V1;

use QuillSMTP\Abstracts\REST_Controller;
use QuillSMTP\Settings;

/**
 * REST_Settings_Controller class.
 *
 * @since 1.0.0
 */
class REST_Settings_Controller extends REST_Controller {

	/**
	 * REST Base
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Register the routes for the controller.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			"/{$this->rest_base}",
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update' ),
					'permission_callback' => array( $this, 'update_permissions_check' ),
					'args'                => rest_get_endpoint_args_for_schema( $this->get_schema(), WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	/**
	 * Retrieves schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_schema() {
		$schema = array(
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'settings',
			'type'                 => 'object',
			'additionalProperties' => false,
			'properties'           => array(
				'general' => array(
					'type'                 => 'object',
					'additionalProperties' => false,
					'properties'           => array(
						'from_email'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'force_from_email' => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'from_name'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'force_from_name'  => array(
							'type'    => 'boolean',
							'default' => false,
						),
					),
				),
			),
		);
		return $schema;
	}

	/**
	 * Retrieves settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get( $request ) { // phpcs:ignore
		$groups   = $request->get_param( 'groups' )
			? explode( ',', $request->get_param( 'groups' ) )
			: array();
		$settings = Settings::get_all();

		$result = array();
		foreach ( $this->get_schema()['properties'] as $group_key => $group_schema ) {
			if ( in_array( $group_key, $groups, true ) ) {
				$result[ $group_key ] = array();
				foreach ( $group_schema['properties'] as $setting_key => $setting_schema ) {
					$result[ $group_key ][ $setting_key ] = $settings[ $setting_key ] ?? $setting_schema['default'];
				}
			}
		}

		return new WP_REST_Response( $result, 200 );
	}

	/**
	 * Checks if a given request has access to get settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_permissions_check( $request ) {
		$capability = 'manage_quillforms';
		return current_user_can( $capability, $request );
	}

	/**
	 * Updates settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update( $request ) {
		$settings = $request->get_json_params();
		Settings::update_many( $settings );
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Checks if a given request has access to update settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_permissions_check( $request ) {
		$capability = 'manage_quillforms';
		return current_user_can( $capability, $request );
	}

}
