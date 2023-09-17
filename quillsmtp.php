<?php
/**
 * Plugin Name: Quill SMTP
 *
 * Description: A plugin to send emails using SMTP instead of the default PHP mail() function.
 *
 * Version: 1.0.0
 *
 * Author: QuillForms
 *
 * Author URI: https://quillforms.com
 *
 * Text Domain: quillsmtp
 *
 * Domain Path: /languages
 *
 * @package QuillSMTP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
define( 'QUILLSMTP_VERSION', '1.0.0' );
define( 'QUILLSMTP_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUILLSMTP_URL', plugin_dir_url( __FILE__ ) );
define( 'QUILLSMTP_BASENAME', plugin_basename( __FILE__ ) );

// Require the autoloader.
require_once QUILLSMTP_DIR . 'includes/autoload.php';

// Initialize the plugin.
QuillSMTP\QuillSMTP::instance();
