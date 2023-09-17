<?php
/**
 * Main Class: QuillSMTP
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

namespace QuillSMTP;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Admin\Admin;
use QuillSMTP\Admin\Admin_Loader;

/**
 * QuillSMTP Main Class.
 * This class is responsible for initializing the plugin.
 *
 * @since 1.0.0
 */
final class QuillSMTP {

	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var QuillSMTP
	 */
	private static $instance;

	/**
	 * QuillSMTP Instance.
	 *
	 * Instantiates or reuses an instance of QuillSMTP.
	 *
	 * @since  1.0.0
	 * @static
	 *
	 * @return self - Single instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_objects();
	}

	/**
	 * Initialize Objects.
	 *
	 * @since 1.0.0
	 */
	private function init_objects() {
		Admin_Loader::instance();
		Admin::instance();
	}
}
