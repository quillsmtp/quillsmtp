<?php
/**
 * Utils class
 *
 * @since next.version
 *
 * @package QuillSMTP
 */

namespace QuillSMTP;

use WP_Error;
use WP_REST_Response;
use QuillSMTP\QuillSMTP;
use QuillSMTP\Security;

/**
 * Utils Class
 */
final class Utils {

	/**
	 * Get max execution time
	 *
	 * @return int
	 */
	public static function get_max_execution_time() {
		$max_execution_time = 30;

		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );

			if ( ! $max_execution_time ) {
				$max_execution_time = 30;
			}
		}

		// Decrease a little bit to avoid reaching the limit.
		$max_execution_time = $max_execution_time * 0.75;
		return apply_filters( 'quillsmtp_max_execution_time', $max_execution_time );
	}

	/**
	 * Is memory limit reached
	 *
	 * @return bool
	 */
	public static function is_memory_limit_reached() {
		$memory_limit = self::get_memory_limit();
		$memory_usage = memory_get_usage( true );
		$memory_limit = self::convert_to_bytes( $memory_limit );
		$memory_limit = $memory_limit * 0.75;

		return $memory_usage >= $memory_limit;
	}

	/**
	 * Get memory limit
	 *
	 * @return string
	 */
	public static function get_memory_limit() {
		$memory_limit = '128M';

		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );

			if ( ! $memory_limit ) {
				$memory_limit = '128M';
			}
		}

		return apply_filters( 'quillsmtp_memory_limit', $memory_limit );
	}

	/**
	 * Convert to bytes
	 *
	 * @param string $value
	 *
	 * @return int
	 */
	public static function convert_to_bytes( $value ) {
		$value     = trim( $value );
		$last      = strtolower( $value[ strlen( $value ) - 1 ] );
		$new_value = intval( $value );

		switch ( $last ) {
			case 'g':
				$new_value *= GB_IN_BYTES;
				break;
			case 'm':
				$new_value *= MB_IN_BYTES;
				break;
			case 'k':
				$new_value *= KB_IN_BYTES;
				break;
		}

		return $new_value;
	}

	/**
	 * Initialize WP_Filesystem.
	 *
	 * @return bool True if filesystem is initialized.
	 */
	private static function init_filesystem() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! WP_Filesystem() ) {
			return false;
		}

		return true;
	}

	/**
	 * Export items with pagination and file creation.
	 *
	 * @param array    $params
	 * @param callable $callback
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public static function export_items( $params, $callback ) {
		global $wp_filesystem;

		$file_id   = ! empty( $params['file_id'] ) ? $params['file_id'] : time();
		$file_path = self::get_temp_file_path( $params['file_prefix'], $file_id );

		if ( is_wp_error( $file_path ) ) {
			return $file_path;
		}

		if ( $params['download'] ) {
			self::export_json( $file_path );
		}

		if ( ! self::init_filesystem() ) {
			return new WP_Error(
				'quillsmtp_filesystem_error',
				esc_html__( 'Cannot initialize filesystem', 'quill-smtp' ),
				[ 'status' => 500 ]
			);
		}

		// Get existing content or start fresh.
		$existing_content = '';
		if ( $wp_filesystem->exists( $file_path ) ) {
			$existing_content = $wp_filesystem->get_contents( $file_path );
		}

		if ( $params['offset'] === 0 ) {
			$existing_content = "[\n";
		}

		$start_time         = microtime( true );
		$max_execution_time = self::get_max_execution_time();

		while ( ( microtime( true ) - $start_time ) < $max_execution_time && ! self::is_memory_limit_reached() ) {
			$logs = call_user_func( $callback, $params['filter'], $params['offset'], $params['limit'] );

			if ( empty( $logs ) ) {
				// Remove trailing comma and newline, then close the array.
				$existing_content = rtrim( $existing_content, ",\n" ) . "\n]\n";
				$wp_filesystem->put_contents( $file_path, $existing_content, FS_CHMOD_FILE );

				return new WP_REST_Response(
					[
						'status'  => 'done',
						'file_id' => $file_id,
					],
					200
				);
			}

			foreach ( $logs as $log ) {
				$existing_content .= wp_json_encode( $log ) . ",\n";
				$params['offset']++;
			}
		}

		$wp_filesystem->put_contents( $file_path, $existing_content, FS_CHMOD_FILE );

		return new WP_REST_Response(
			[
				'status'  => 'continue',
				'offset'  => $params['offset'],
				'file_id' => $file_id,
			],
			200
		);
	}

	/**
	 * Export JSON
	 *
	 * @param string $file_path
	 */
	public static function export_json( $file_path ) {
		global $wp_filesystem;

		if ( ! self::init_filesystem() ) {
			return;
		}

		$filename = 'logs_export.json';
		$filesize = $wp_filesystem->size( $file_path );
		$content  = $wp_filesystem->get_contents( $file_path );

		nocache_headers();
		header( 'X-Robots-Tag: noindex', true );
		header( 'Content-Type: application/json' );
		header( 'Content-Description: File Transfer' );
		header( "Content-Disposition: attachment; filename=\"$filename\";" );
		header( 'Content-Length: ' . $filesize );

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON file content.
		wp_delete_file( $file_path );
		exit;
	}

	/**
	 * Get temp file path
	 *
	 * @param string $file_id
	 *
	 * @return string|WP_Error
	 */
	public static function get_temp_file_path( $file_id ) {
		$temp_dir = QuillSMTP::get_upload_dir() . '/temp';
		if ( ! Security::prepare_upload_dir( $temp_dir ) ) {
			return new WP_Error( 'quillsmtp_cannot_create_dir', 'Cannot create dir' );
		}

		$file_name = sanitize_file_name( "quillsmtp-export-logs-{$file_id}.json" );
		return "{$temp_dir}/{$file_name}";
	}
}
