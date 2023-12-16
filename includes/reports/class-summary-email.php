<?php
/**
 * Summary Email class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

namespace QuillSMTP\Reports;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\QuillSMTP;

/**
 * Summary Email class.
 *
 * @since 1.0.0
 */
class Summary_Email {

	/**
	 * Class instance
	 *
	 * @since 1.0.0
	 *
	 * @var self instance
	 */
	private static $instance = null;

	/**
	 * Get class instance
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'quillsmtp_loaded', array( $this, 'summary_email_task' ), 100 );
	}

	/**
	 * Summary email task
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function summary_email_task() {
		// schedule task.
		add_action(
			'init',
			function() {
				error_log( QuillSMTP::instance()->tasks->get_next_timestamp( 'summary_email' ) );
				if ( QuillSMTP::instance()->tasks->get_next_timestamp( 'summary_email' ) === false ) {
					$date = new \DateTime( 'next monday 2pm', self::wp_timezone() );
					QuillSMTP::instance()->tasks->schedule_recurring(
						$date->getTimestamp(),
						WEEK_IN_SECONDS,
						'summary_email'
					);
				}
			}
		);

		// scheduled task callback.
		QuillSMTP::instance()->tasks->register_callback(
			'summary_email',
			array( $this, 'handle_summary_email_task' )
		);
	}

	/**
	 * Handle summary email task.
	 *
	 * @since 1.0.0
	 *
	 * @param string $trigger Trigger.
	 * @return void
	 */
	public function handle_summary_email_task( $trigger = 'cron' ) {
		$email   = get_option( 'admin_email' );
		$message = $this->build_email();
		wp_mail(
			$email,
			__( 'QuillSMTP Test Email', 'quillsmtp' ),
			$message,
			$this->get_headers()
		);
	}

	/**
	 * Retrieves the WordPress timezone.
	 *
	 * If the `wp_timezone` function is available, it is used to retrieve the timezone.
	 * Otherwise, the `wp_timezone_string` function is used to retrieve the timezone string,
	 * which is then used to create a new \DateTimeZone object.
	 *
	 * @return \DateTimeZone The WordPress timezone.
	 */
	public static function wp_timezone() {

		if ( function_exists( 'wp_timezone' ) ) {
			return wp_timezone();
		}

		return new \DateTimeZone( self::wp_timezone_string() );
	}

	/**
	 * Retrieves the WordPress timezone string.
	 *
	 * If the function `wp_timezone_string` exists, it is used to retrieve the timezone string.
	 * Otherwise, it checks if a timezone string is set in the WordPress options.
	 * If a timezone string is found, it is returned.
	 * If no timezone string is set, it calculates the timezone offset based on the GMT offset
	 * and returns the offset in the format "+HH:MM" or "-HH:MM".
	 *
	 * @return string The WordPress timezone string or the timezone offset.
	 */
	public static function wp_timezone_string() {

		if ( function_exists( 'wp_timezone_string' ) ) {
			return wp_timezone_string();
		}

		$timezone_string = get_option( 'timezone_string' );

		if ( $timezone_string ) {
			return $timezone_string;
		}

		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

		return $tz_offset;
	}

	/**
	 * Get the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return string The email headers.
	 */
	public function get_headers() {
		$admin_name  = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );

		$headers  = "From: {$admin_name} <{$admin_email}>\r\n";
		$headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";

		return apply_filters( 'quillsmtp_email_test_headers', $headers, $this );
	}

	/**
	 * Get the email content type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The email content type.
	 */
	public function get_content_type() {

		$content_type = apply_filters( 'quillsmtp_email_test_content_type', 'text/html', $this );

		return apply_filters( 'quillsmtp_email_content_type', $content_type, $this );
	}

	/**
	 * Build the email.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function build_email() {
		/*
		 * Generate an HTML email.
		 */

		ob_start();

		$this->get_template_part( 'summary-email', false );

		$message = ob_get_clean();

		return apply_filters( 'quillsmtp_email_test_message', $message, $this );
	}

	/**
	 * Retrieve a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Optional. Default null.
	 * @param bool   $load Maybe load.
	 *
	 * @return string
	 */
	public function get_template_part( $name = null, $load = true ) {
		$template = QUILLSMTP_PLUGIN_DIR . 'includes/reports/templates/';
		if ( isset( $name ) ) {
			$template .= $name . '.php';
		}
		$template = apply_filters( 'quillsmtp_get_template_part', $template, $name, $load );

		// Return the part that is found.
		return load_template( $template, $load );
	}
}
