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
use QuillSMTP\Log_Handlers\Log_Handler_DB;
use QuillSMTP\Email_Test\Email_Test;
use QuillSMTP\Tasks;
use QuillSMTP\Reports\Summary_Email;
use QuillSMTP\Site\Site;

/**
 * QuillSMTP Main Class.
 * This class is responsible for initializing the plugin.
 *
 * @since 1.0.0
 */
final class QuillSMTP {

	/**
	 * Tasks
	 *
	 * @since 1.0.0
	 *
	 * @var Tasks
	 */
	public $tasks;

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
		$this->load_objects();
		$this->load_dependencies();
		$this->init_hooks();
		add_action( 'init', array( $this, 'init_objects' ) );
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
	 * Load Objects.
	 *
	 * @since 1.0.0
	 */
	public function load_objects() {
		$this->tasks = new Tasks( 'quillsmtp' );
		Summary_Email::instance();
		Site::instance();
	}

	/**
	 * Initialize Objects.
	 *
	 * @since 1.0.0
	 */
	public function init_objects() {
		Admin_Loader::instance();
		Admin::instance();
		REST_API::instance();
		Mailers::instance();
		Email_Test::instance();
	}

	/**
	 * Dependencies Loader.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// functions.
		include_once QUILLSMTP_PLUGIN_DIR . 'includes/functions.php';
	}

	/**
	 * Initialize hooks
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Replace the default PHPMailer class with our own.
		add_action( 'plugins_loaded', array( $this, 'replace_phpmailer' ) );

		// Register log handlers.
		add_filter( 'quillsmtp_register_log_handlers', array( $this, 'register_log_handlers' ) );
	}

	/**
	 * Register log handlers
	 *
	 * @param  array $handlers Handlers array to filter.
	 * @return array
	 */
	public function register_log_handlers( $handlers ) {
		$handlers[] = new Log_Handler_DB();
		return $handlers;
	}
}
