<?php
/**
 * Some helper functions.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

use QuillSMTP\Interfaces\Logger_Interface;
use QuillSMTP\Logger;
use QuillSMTP\Settings;

defined( 'ABSPATH' ) || exit;


/**
 * Get a shared logger instance.
 * This function is forked from Woocommerce
 *
 * Use the quillsmtp_logging_class filter to change the logging class. You may provide one of the following:
 *     - a class name which will be instantiated as `new $class` with no arguments
 *     - an instance which will be used directly as the logger
 * In either case, the class or instance *must* implement Logger_Interface.
 *
 * @since 1.0.0
 * @see Logger_Interface
 *
 * @return Logger
 */
function quillsmtp_get_logger() {
	static $logger = null;

	$class = apply_filters( 'quillsmtp_logging_class', Logger::class );

	if ( null !== $logger && is_string( $class ) && is_a( $logger, $class ) ) {
		return $logger;
	}

	$implements = class_implements( $class );

	if ( is_array( $implements ) && in_array( Logger_Interface::class, $implements, true ) ) {
		$threshold = Settings::get( 'log_level', 'info' );
		$logger    = is_object( $class ) ? $class : new $class( null, $threshold );
	} else {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: class name 2: quillsmtp_logging_class 3: Logger_Interface */
				__( 'The class %1$s provided by %2$s filter must implement %3$s.', 'quillsmtp' ),
				'<code>' . esc_html( is_object( $class ) ? get_class( $class ) : $class ) . '</code>',
				'<code>quillsmtp_logging_class</code>',
				'<code>Logger_Interface</code>'
			),
			'1.0.0'
		);

		$logger = is_a( $logger, Logger::class ) ? $logger : new Logger();
	}

	return $logger;
}

/**
 * Trigger logging cleanup using the logging class.
 *
 * @since 1.0.0
 */
function quillsmtp_cleanup_logs() {
	$logger = quillsmtp_get_logger();

	if ( is_callable( array( $logger, 'clear_expired_logs' ) ) ) {
		$logger->clear_expired_logs();
	}
}
add_action( 'quillsmtp_cleanup_logs', 'quillsmtp_cleanup_logs' );

/**
 * Get Email Log instance.
 *
 * @since 1.0.0
 *
 * @return QuillSMTP\Email_Log\Handler_DB
 */
function quillsmtp_get_email_log() {

	return QuillSMTP\Email_Log\Handler_DB::get_instance();
}
