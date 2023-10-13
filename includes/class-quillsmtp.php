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
use QuillSMTP\REST_API\REST_API;
use QuillSMTP\Mailers\Mailers;
use QuillSMTP\PHPMailer\PHPMailer;

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

		// Replace the default PHPMailer class with our own.
		add_action( 'plugins_loaded', array( $this, 'replace_phpmailer' ) );
	}

	/**
	 * Replace the default PHPMailer class with our own.
	 *
	 * @since 1.0.0
	 */
	public function replace_phpmailer() {
		global $phpmailer;

		if ( ! class_exists( '\PHPMailer\PHPMailer\PHPMailer', false ) ) {
				require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		}

		if ( ! class_exists( '\PHPMailer\PHPMailer\Exception', false ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
		}

		if ( ! class_exists( '\PHPMailer\PHPMailer\SMTP', false ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		}

		$phpmailer = new PHPMailer();
	}

	/**
	 * Initialize Objects.
	 *
	 * @since 1.0.0
	 */
	private function init_objects() {
		Admin_Loader::instance();
		Admin::instance();
		REST_API::instance();
		Mailers::instance();
	}
}
