<?php
/**
 * Admin: Class Admin
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage admin
 */

namespace QuillSMTP\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Class.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Class Instance.
	 *
	 * @var Admin
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Admin Instance.
	 *
	 * Instantiates or reuses an instance of Admin.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @see Admin()
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
	 * Since this is a singleton class, it is better to have its constructor as a private.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->admin_hooks();
	}

	/**
	 * Admin Hooks.
	 *
	 * @since 1.0.0
	 */
	public function admin_hooks() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu_pages' ) );
	}

	/**
	 * Create Admin Menu Pages.
	 *
	 * @since 1.0.0
	 */
	public function create_admin_menu_pages() {
		add_menu_page(
			__( 'Quill SMTP', 'quillsmtp' ),
			__( 'Quill SMTP', 'quillsmtp' ),
			'manage_options',
			'quillsmtp',
			array( Admin_Loader::class, 'page_wrapper' ),
			'dashicons-email-alt',
			100
		);

		// General.
		add_submenu_page(
			'quillsmtp',
			__( 'General', 'quillsmtp' ),
			__( 'General', 'quillsmtp' ),
			'manage_options',
			'quillsmtp',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Email Test.
		add_submenu_page(
			'quillsmtp',
			__( 'Email Test', 'quillsmtp' ),
			__( 'Email Test', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=email-test',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Logs.
		add_submenu_page(
			'quillsmtp',
			__( 'Logs', 'quillsmtp' ),
			__( 'Logs', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=logs',
			array( Admin_Loader::class, 'page_wrapper' )
		);
	}
}
