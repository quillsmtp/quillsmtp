<?php
/**
 * REST API: class REST_API
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage REST_API
 */

namespace QuillSMTP\REST_API;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\REST_API\Controllers\V1\REST_Settings_Controller;

/**
 * REST_API class is mainly responsible for registering routes.
 *
 * @since 1.0.0
 */
class REST_API {

	/**
	 *  Class singleton instance
	 *
	 * @since 1.0.0
	 *
	 * @var object $_instance The singleton instance.
	 */
	private static $_instance = null;

	/**
	 * Get instance as a singleton.
	 *
	 * @since 1.0.0
	 *
	 * @return self $_instance An instance of the REST_API class
	 */
	public static function instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning the singletone.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {
	} /* do nothing */

	/**
	 * REST_API constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register REST API routes
	 *
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		$controllers = array(
			REST_Settings_Controller::class,
		);

		foreach ( $controllers as $controller ) {
			$controller = new $controller();
			$controller->register_routes();
		}
	}
}
