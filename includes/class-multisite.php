<?php
/**
 * Multisite class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

namespace QuillSMTP;

use QuillSMTP\Settings;
use QuillSMTP\Admin\Admin;

/**
 * Multisite class.
 *
 * @since 1.0.0
 */
class Multisite {

	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var object
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
		if ( ! is_multisite() ) {
			return;
		}

		$main_site_id = get_network()->site_id;
		$settings     = get_blog_option( $main_site_id, Settings::OPTION_NAME, array() );
		// Check if global network settings enabled.
		$global_settings_enabled = $settings['global_network_settings'] ?? true;
		$admin                   = Admin::instance();
		add_action( 'network_admin_menu', array( $admin, 'create_admin_menu_pages' ) );
		// If global network settings enabled, remove the home and settings submenu pages from all sites except the main site.
		if ( $global_settings_enabled ) {
			add_action( 'admin_menu', array( $this, 'remove_submenu_pages' ), 999 );
			add_action( 'quillsmtp_before_get_settings', array( $this, 'get_global_network_settings' ) );
			add_action( 'quillsmtp_after_get_settings', array( $this, 'restore_blog_settings' ) );
			add_action( 'quillsmtp_before_get_mailers_settings', array( $this, 'get_global_network_settings' ) );
			add_action( 'quillsmtp_after_get_mailers_settings', array( $this, 'restore_blog_settings' ) );

			// Remove notices.
			add_action( 'network_admin_notices', array( $this, 'remove_notices' ), 1 );
		}
	}

	/**
	 * Remove Notices.
	 *
	 * @since 1.0.0
	 */
	public function remove_notices() {
		// Check if quillsmtp is the current page.
		$current_screen = get_current_screen();
		// Check if current screen has a quillsmtp string.
		if ( false === strpos( $current_screen->id, 'quillsmtp' ) ) {
			return;
		}

		// Remove notices.
		remove_all_actions( 'network_admin_notices' );
	}

	/**
	 * Remove submenu pages.
	 *
	 * @since 1.0.0
	 */
	public function remove_submenu_pages() {
		// Remove the home and settings submenu pages from all sites except the main site.
		remove_submenu_page( 'quillsmtp', 'quillsmtp' );
		remove_submenu_page( 'quillsmtp', 'quillsmtp&path=settings' );
	}

	/**
	 * Get global network settings.
	 *
	 * @since 1.0.0
	 */
	public function get_global_network_settings() {
		// Switch to the main site to get the global network settings.
		switch_to_blog( get_network()->site_id );
	}

	/**
	 * Restore blog settings.
	 *
	 * @since 1.0.0
	 */
	public function restore_blog_settings() {
		// Restore the current blog settings.
		restore_current_blog();
	}
}
