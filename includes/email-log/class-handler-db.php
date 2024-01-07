<?php
/**
 * Class Log_Handler_DB file.
 *
 * @package QuillSMTP
 * @subpackage email-log
 *
 * @since 1.0.0
 */

namespace QuillSMTP\Email_Log;

use QuillSMTP\Vendor\Automattic\Jetpack\Constants;

/**
 * Handles log entries by writing to database.
 *
 * @class          Handler_DB
 *
 * @since        1.0.0
 */
class Handler_DB {

	/**
	 * Instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @var Handler_DB
	 */
	protected static $instance = null;

	/**
	 * Get instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return Handler_DB
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {}

	/**
	 * Handles the email log entry.
	 *
	 * @param string $subject        The subject of the email.
	 * @param string $body           The body of the email.
	 * @param string $headers        The headers of the email.
	 * @param array  $attachments    The attachments of the email.
	 * @param string $from           The sender of the email.
	 * @param array  $recipients     The recipients of the email.
	 * @param string $status         The status of the email.
	 * @param array  $provider       The email provider.
	 * @param int    $resend_count   The number of times the email has been resent.
	 * @return bool                  True if the email log entry is added successfully, false otherwise.
	 */
	public function handle( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $resend_count = 0 ) {
		// source.
		$source = $this->get_log_source();

		// versions.
		$context['versions'] = array();
		// add main plugin version.
		$context['versions']['QuillSMTP'] = QUILLSMTP_PLUGIN_VERSION;

		return $this->add( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $resend_count, $source, $context );
	}

	/**
	 * Adds the email log entry to the database.
	 *
	 * @param string $subject        The subject of the email.
	 * @param string $body           The body of the email.
	 * @param array  $headers        The headers of the email.
	 * @param array  $attachments    The attachments of the email.
	 * @param string $from           The sender of the email.
	 * @param array  $recipients     The recipients of the email.
	 * @param string $status         The status of the email.
	 * @param array  $provider       The email provider.
	 * @param int    $resend_count   The number of times the email has been resent.
	 * @param array  $source         The source of the email.
	 * @param array  $context        The context of the email.
	 * @return bool                  True if the email log entry is added successfully, false otherwise.
	 */
	protected function add( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $resend_count, $source, $context ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'quillsmtp_email_log';

		// Serialize arrays before inserting
		$headers     = serialize( $headers );
		$attachments = serialize( $attachments );
		$recipients  = serialize( $recipients );
		$provider    = serialize( $provider );
		$source      = serialize( $source );
		$context     = serialize( $context );

		$data = array(
			'timestamp'    => gmdate( 'Y-m-d H:i:s', time() ),
			'subject'      => $subject,
			'body'         => $body,
			'headers'      => $headers,
			'attachments'  => $attachments,
			'from'         => $from,
			'recipients'   => $recipients,
			'status'       => $status,
			'provider'     => $provider,
			'resend_count' => $resend_count,
			'source'       => $source,
			'context'      => $context,
		);

		$format = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
		);

		$result = $wpdb->insert( $table_name, $data, $format );
		return $result;
	}

	/**
	 * Update log.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $log_id         The log ID.
	 * @param array $data           The data to update.
	 *
	 * @return bool                  True if the email log entry is updated successfully, false otherwise.
	 */
	public static function update( $log_id, $data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'quillsmtp_email_log';

		$result = $wpdb->update( $table_name, $data, array( 'log_id' => $log_id ) );

		return $result;
	}

	/**
	 * Retrieves all email logs based on specified parameters.
	 *
	 * @param string|bool $status     The status of the email logs to retrieve. Default is false.
	 * @param int         $offset     The offset for pagination. Default is 0.
	 * @param int         $count      The number of email logs to retrieve. Default is 0.
	 * @param string|bool $start_date The start date for filtering email logs. Default is false.
	 * @param string|bool $end_date   The end date for filtering email logs. Default is false.
	 * @param string|bool $search     The search term for filtering email logs. Default is false.
	 *
	 * @return array An array of prepared email log results.
	 */
	public function get_all( $status = false, $offset = 0, $count = 0, $start_date = false, $end_date = false, $search = false ) {
		global $wpdb;

		$where = '';

		if ( $status ) {
			$where .= 'WHERE status = "' . $status . '" ';
		}

		if ( $start_date ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND timestamp >= "' . $start_date . '" ';
		}

		if ( $end_date ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND timestamp <= "' . $end_date . '" ';
		}

		if ( $search ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND (subject LIKE "%' . $search . '%" OR body LIKE "%' . $search . '%" OR headers LIKE "%' . $search . '%" OR to LIKE "%' . $search . '%" OR from LIKE "%' . $search . '%" OR recipients LIKE "%' . $search . '%") ';
		}

		$results = $wpdb->get_results(
			// @codingStandardsIgnoreStart
			$wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}quillsmtp_email_log $where ORDER BY timestamp DESC LIMIT %d, %d",
                $offset,
                $count
            ),
			// @codingStandardsIgnoreEnd
			ARRAY_A
		);

		$prepared_results = [];

		foreach ( $results as $result ) {
			// local datetime.
			$local_datetime = get_date_from_gmt( $result['timestamp'] );

			// Remove timestamp from array and add local datetime.
			unset( $result['timestamp'] );
			$result['local_datetime'] = $local_datetime;

			// Add result to prepared results.
			$prepared_results[] = $result;
		}

		return $prepared_results;
	}

	/**
	 * Get selected logs from DB.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|array $log_ids Log ID or array of Log IDs to be deleted.
	 *
	 * @return array
	 */
	public static function get( $log_ids ) {
		global $wpdb;

		if ( ! is_array( $log_ids ) ) {
			$log_ids = array( $log_ids );
		}

		$format   = array_fill( 0, count( $log_ids ), '%d' );
		$query_in = '(' . implode( ',', $format ) . ')';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}quillsmtp_email_log WHERE log_id IN {$query_in}", $log_ids ), ARRAY_A ); // @codingStandardsIgnoreLine.

		$prepared_results = [];

		foreach ( $results as $result ) {
			// local datetime.
			$local_datetime = get_date_from_gmt( $result['timestamp'] );

			// Remove timestamp from array and add local datetime.
			unset( $result['timestamp'] );
			$result['local_datetime'] = $local_datetime;

			// Add result to prepared results.
			$prepared_results[] = $result;
		}

		return $prepared_results;
	}

	/**
	 * Retrieves the count of email logs based on the specified parameters.
	 *
	 * @param string|bool $status     Optional. The status of the email logs to filter by.
	 * @param string|bool $start_date Optional. The start date to filter the logs from.
	 * @param string|bool $end_date   Optional. The end date to filter the logs until.
	 * @param string|bool $search     Optional. The search term to filter the logs by.
	 *
	 * @return int The count of email logs.
	 */
	public static function get_count( $status = false, $start_date = false, $end_date = false, $search = false ) {
		global $wpdb;

		$where = '';

		if ( $status ) {
			$where .= 'WHERE status = "' . $status . '" ';
		}

		if ( $start_date ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND timestamp >= "' . $start_date . '" ';
		}

		if ( $end_date ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND timestamp <= "' . $end_date . '" ';
		}

		if ( $search ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			}
			$where .= 'AND (subject LIKE "%' . $search . '%" OR body LIKE "%' . $search . '%" OR headers LIKE "%' . $search . '%" OR to LIKE "%' . $search . '%" OR from LIKE "%' . $search . '%" OR recipients LIKE "%' . $search . '%") ';
		}

		return (int) $wpdb->get_var(
            // @codingStandardsIgnoreStart
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}quillsmtp_email_log $where"
            )
            // @codingStandardsIgnoreEnd
		);
	}

	/**
	 * Clear all logs from the DB.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if flush was successful.
	 */
	public static function flush() {
		global $wpdb;

		return $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}quillsmtp_email_log" );
	}

	/**
	 * Clear entries for a chosen handle/source.
	 *
	 * @since 1.0.0
	 *
	 * @param string $source Log source.
	 * @return bool
	 */
	public function clear( $source ) {
		global $wpdb;

		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE source = %s",
				$source
			)
		);
	}

	/**
	 * Delete selected logs from DB.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|array $log_ids Log ID or array of Log IDs to be deleted.
	 *
	 * @return bool
	 */
	public static function delete( $log_ids ) {
		global $wpdb;

		if ( ! is_array( $log_ids ) ) {
			$log_ids = array( $log_ids );
		}

		$format   = array_fill( 0, count( $log_ids ), '%d' );
		$query_in = '(' . implode( ',', $format ) . ')';
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE log_id IN {$query_in}", $log_ids ) ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Delete all logs older than a defined timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $timestamp Timestamp to delete logs before.
	 */
	public static function delete_logs_before_timestamp( $timestamp = 0 ) {
		if ( ! $timestamp ) {
			return;
		}

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE timestamp < %s",
				gmdate( 'Y-m-d H:i:s', $timestamp )
			)
		);
	}

	/**
	 * Get appropriate source based on file name.
	 *
	 * Try to provide an appropriate source in case none is provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string Text to use as log source. "" (empty string) if none is found.
	 */
	protected static function get_log_source() {
		/**
		 * PHP < 5.3.6 correct behavior
		 *
		 * @see http://php.net/manual/en/function.debug-backtrace.php#refsect1-function.debug-backtrace-parameters
		 */
		if ( Constants::is_defined( 'DEBUG_BACKTRACE_IGNORE_ARGS' ) ) {
			$debug_backtrace_arg = DEBUG_BACKTRACE_IGNORE_ARGS; // phpcs:ignore PHPCompatibility.Constants.NewConstants.debug_backtrace_ignore_argsFound
		} else {
			$debug_backtrace_arg = false;
		}

		$trace = debug_backtrace( $debug_backtrace_arg ); // @codingStandardsIgnoreLine.
		foreach ( $trace as $t ) {
			if ( isset( $t['function'] ) ) {
				if ( 'wp_mail' === $t['function'] ) {
					return static::get_initiator( $t['file'] );
				}
			}
		}

		return '';
	}

	/**
	 * Get initiator plugin/theme name, slug and version.
	 *
	 * @param string $file_path File path.
	 * @return string
	 */
	protected static function get_initiator( $file_path ) {
		$initiator = self::get_initiator_plugin( $file_path );

		if ( ! $initiator ) {
			$initiator = self::get_initiator_theme( $file_path );
		}

		if ( ! $initiator ) {
			$initiator = self::get_initiator_wp_core( $file_path );
		}

		if ( ! $initiator ) {
			$initiator = array(
				'name' => esc_html__( 'Unknown', 'quillsmtp' ),
				'slug' => 'unknown',
				'type' => 'unknown',
			);
		}

		return $initiator;
	}

	/**
	 * Get the initiator's data, if it's a plugin (or mu plugin).
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path       The absolute path of a file.
	 * @param bool   $check_mu_plugin Whether to check for mu plugins or not.
	 *
	 * @return false|array
	 */
	private static function get_initiator_plugin( $file_path, $check_mu_plugin = false ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh, Generic.Metrics.CyclomaticComplexity.MaxExceeded

		$constant = empty( $check_mu_plugin ) ? 'WP_PLUGIN_DIR' : 'WPMU_PLUGIN_DIR';

		if ( ! defined( $constant ) ) {
			return false;
		}

		$root      = basename( constant( $constant ) );
		$separator = defined( 'DIRECTORY_SEPARATOR' ) ? '\\' . DIRECTORY_SEPARATOR : '\/';

		preg_match( "/$separator$root$separator(.[^$separator]+)($separator|\.php)/", $file_path, $result );

		if ( ! empty( $result[1] ) ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$all_plugins = empty( $check_mu_plugin ) ? get_plugins() : get_mu_plugins();
			$plugin_slug = $result[1];

			foreach ( $all_plugins as $plugin => $plugin_data ) {
				if (
					1 === preg_match( "/^$plugin_slug(\/|\.php)/", $plugin ) &&
					isset( $plugin_data['Name'] )
				) {
					return [
						'name' => $plugin_data['Name'],
						'slug' => $plugin,
						'type' => $check_mu_plugin ? 'mu-plugin' : 'plugin',
					];
				}
			}

			return [
				'name' => $result[1],
				'slug' => '',
				'type' => $check_mu_plugin ? 'mu-plugin' : 'plugin',
			];
		}

		return false;
	}

	/**
	 * Get the initiator's data, if it's a theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path The absolute path of a file.
	 *
	 * @return false|array
	 */
	private static function get_initiator_theme( $file_path ) {

		if ( ! defined( 'WP_CONTENT_DIR' ) ) {
			return false;
		}

		$root      = basename( WP_CONTENT_DIR );
		$separator = defined( 'DIRECTORY_SEPARATOR' ) ? '\\' . DIRECTORY_SEPARATOR : '\/';

		preg_match( "/$separator$root{$separator}themes{$separator}(.[^$separator]+)/", $file_path, $result );

		if ( ! empty( $result[1] ) ) {
			$theme = wp_get_theme( $result[1] );

			return [
				'name' => method_exists( $theme, 'get' ) ? $theme->get( 'Name' ) : $result[1],
				'slug' => $result[1],
				'type' => 'theme',
			];
		}

		return false;
	}

	/**
	 * Return WP Core if the file path is from WP Core (wp-admin or wp-includes folders).
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path The absolute path of a file.
	 *
	 * @return false|array
	 */
	private static function get_initiator_wp_core( $file_path ) {

		if ( ! defined( 'ABSPATH' ) ) {
			return false;
		}

		$wp_includes = defined( 'WPINC' ) ? trailingslashit( ABSPATH . WPINC ) : false;
		$wp_admin    = trailingslashit( ABSPATH . 'wp-admin' );

		if (
			strpos( $file_path, $wp_includes ) === 0 ||
			strpos( $file_path, $wp_admin ) === 0
		) {
			return [
				'name' => esc_html__( 'WP Core', 'quillsmtp' ),
				'slug' => 'wp-core',
				'type' => 'wp-core',
			];
		}

		return false;
	}

	/**
	 * Clean filename
	 *
	 * @param string $filename Full path of file.
	 * @return string
	 */
	protected static function clean_filename( $filename ) {
		if ( substr( $filename, 0, strlen( ABSPATH ) ) === ABSPATH ) {
			$filename = substr( $filename, strlen( ABSPATH ) );
		}
		return $filename;
	}
}
