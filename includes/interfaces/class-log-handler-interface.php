<?php
/**
 * Log Handler Interface
 * This class is forked from Woocommerce
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage Interface
 */

namespace QuillSMTP\Interfaces;

/**
 * QF Log Handler Interface
 *
 * Functions that must be defined to correctly fulfill log handler API.
 *
 * @since 1.0.0
 */
interface Log_Handler_Interface {

	/**
	 * Handle a log entry.
	 *
	 * @param int    $timestamp Log timestamp.
	 * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
	 * @param string $message Log message.
	 * @param array  $context Additional information for log handlers.
	 *
	 * @return bool False if value was not handled and true if value was handled.
	 */
	public function handle( $timestamp, $level, $message, $context );
}
