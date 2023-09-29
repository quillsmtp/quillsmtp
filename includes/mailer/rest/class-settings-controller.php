<?php
/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailer\REST;

use QuillSMTP\Mailer\Mailer;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Settings_Controller abstract class.
 *
 * @since 1.0.0
 */
abstract class Settings_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'qsmtp/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Rest endpoint.
	 *
	 * @var string
	 */
	protected $rest_endpoint = '/settings';

	/**
	 * Mailer
	 *
	 * @var Mailer
	 */
	protected $mailer;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Mailer $mailer Mailer.
	 */
	public function __construct( $mailer ) {
		$this->mailer    = $mailer;
		$this->rest_base = "mailers/{$this->mailer->slug}{$this->rest_endpoint}";

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register controller routes
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
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete' ),
					'permission_callback' => array( $this, 'delete_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Retrieves schema, conforming to JSON Schema.
	 * Should include context for gettable data
	 * Should specify additionalProperties & readonly to specify updatable data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	abstract public function get_schema();

	/**
	 * Retrieves settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get( $request ) { // phpcs:ignore
		$settings = $this->mailer->settings->get();
		$settings = rest_filter_response_by_context( $settings, $this->get_schema(), 'view' );
		return new WP_REST_Response( $settings, 200 );
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
		$capability = 'manage_quillsmtp';
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
		$updated  = $this->mailer->settings->update( $settings );
		if ( $updated ) {
			return new WP_REST_Response( array( 'success' => true ) );
		} else {
			return new WP_Error( "quillsmtp_{$this->mailer->slug}_settings_update", esc_html__( 'Cannot update settings', 'quillsmtp' ) );
		}
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
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}

	/**
	 * Delete settings.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete( $request ) { // phpcs:ignore
		$this->mailer->settings->delete();
		return new WP_REST_Response( array( 'success' => true ) );
	}

	/**
	 * Checks if a given request has access to delete settings.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}

}
