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
	 * Export items with pagination and file creation.
	 *
	 * @param array    $params
	 * @param callable $callback
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public static function export_items( $params, $callback ) {
		$file_id   = ! empty( $params['file_id'] ) ? $params['file_id'] : time();
		$file_path = self::get_temp_file_path( $params['file_prefix'], $file_id );

		if ( is_wp_error( $file_path ) ) {
			return $file_path;
		}

		if ( $params['download'] ) {
			self::export_json( $file_path );
		}

		$fp = file_exists( $file_path ) ? fopen( $file_path, 'a' ) : fopen( $file_path, 'w' );

		if ( ! $fp ) {
			return new WP_Error(
				'quillsmtp_cannot_create_file',
				esc_html__( 'Cannot create export file', 'quillsmtp' ),
				[ 'status' => 500 ]
			);
		}

		if ( $params['offset'] === 0 ) {
			fwrite( $fp, "[\n" );
		}

		$start_time         = microtime( true );
		$max_execution_time = self::get_max_execution_time();

		while ( ( microtime( true ) - $start_time ) < $max_execution_time && ! self::is_memory_limit_reached() ) {
			$logs = call_user_func( $callback, $params['filter'], $params['offset'], $params['limit'] );

			if ( empty( $logs ) ) {
				fseek( $fp, -2, SEEK_END );
				fwrite( $fp, "\n]\n" );
				fclose( $fp );

				return new WP_REST_Response(
					[
						'status'  => 'done',
						'file_id' => $file_id,
					],
					200
				);
			}

			foreach ( $logs as $log ) {
				fwrite( $fp, json_encode( $log ) . ",\n" );
				$params['offset']++;
			}
		}

		fclose( $fp );

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
		$filename = 'logs_export.json';

		nocache_headers();
		header( 'X-Robots-Tag: noindex', true );
		header( 'Content-Type: application/json' );
		header( 'Content-Description: File Transfer' );
		header( "Content-Disposition: attachment; filename=\"$filename\";" );
		header( 'Content-Length: ' . filesize( $file_path ) );

		readfile( $file_path );
		unlink( $file_path );
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
