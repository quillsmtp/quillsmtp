<?php
/**
 * Plugin Name: Quill SMTP
 *
 * Description: A plugin to send emails using SMTP instead of the default PHP mail() function.
 *

 * Version: 1.8.3
 *
 * Author: quillforms
 *
 * Author URI: https://quillsmtp.com
 *
 * Text Domain: quill-smtp
 *
 * Domain Path: /languages
 *
 * License: GPLv2 or later
 *
 * @package QuillSMTP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
define( 'QUILLSMTP_PLUGIN_VERSION', '1.8.3' );
define( 'QUILLSMTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUILLSMTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'QUILLSMTP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'QUILLSMTP_PLUGIN_FILE', __FILE__ );
define( 'QUILLSMTP_SITE_URL', 'https://quillsmtp.com' );

// delete_option('quillsmtp_settings');

// Require dependencies.
require_once QUILLSMTP_PLUGIN_DIR . 'dependencies/build/vendor/scoper-autoload.php';
require_once QUILLSMTP_PLUGIN_DIR . 'dependencies/libraries/load.php';

// Require the autoloader.
require_once QUILLSMTP_PLUGIN_DIR . 'includes/autoload.php';

// Initialize the plugin.
quillsmtp_pre_init();

/**
 * Verify that we can initialize QuillSMTP , then load it.
 *
 * @since 1.0.0
 */
function quillsmtp_pre_init() {

	QuillSMTP\QuillSMTP::instance();
	register_activation_hook( __FILE__, array( QuillSMTP\Install::class, 'install' ) );

	// do quillsmtp_loaded action.
	add_action(
		'plugins_loaded',
		function () {
			do_action( 'quillsmtp_loaded' );
		}
	);
}
