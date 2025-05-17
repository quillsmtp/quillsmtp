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
use QuillSMTP\Settings;
use QuillSMTP\Mailers\Mailers;

/**
 * Handles log entries by writing to database.
 *
 * @class          Handler_DB
 *
 * @since        1.0.0
 */
class Handler_DB {

	/**
	 * Table name without the prefix
	 *
	 * @var string
	 */
	const TABLE = 'quillsmtp_email_log';

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
	 * @param string $provider       The email provider.
	 * @param string $connection_id       The email connection id.
	 * @param string $account_id       The email account id.
	 * @param array  $response The name of the initiator.
	 * @param int    $resend_count   The number of times the email has been resent.
	 * @return bool                  True if the email log entry is added successfully, false otherwise.
	 */
	public function handle( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $connection_id, $account_id, $response, $resend_count = 0 ) {
		// source.
		$source         = $this->get_log_source();
		$initiator_name = isset( $source['name'] ) ? $source['name'] : '';
		$initiator_slug = isset( $source['slug'] ) ? $source['slug'] : '';
		$initiator_type = isset( $source['type'] ) ? $source['type'] : '';

		// versions.
		$context['versions'] = array();
		// add main plugin version.
		$context['versions']['QuillSMTP'] = QUILLSMTP_PLUGIN_VERSION;

		return $this->add( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $connection_id, $account_id, $response, $initiator_name, $initiator_slug, $initiator_type, $context, $resend_count );
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
	 * @param string $connection_id       The email connection id.
	 * @param string $account_id       The email account id.
	 * @param array  $response The response of the initiator.
	 * @param string $initiator_name The name of the initiator.
	 * @param string $initiator_slug The slug of the initiator.
	 * @param string $initiator_type The type of the initiator.
	 * @param array  $context        The context of the email.
	 * @param int    $resend_count   The number of times the email has been resent.
	 * @return bool                  True if the email log entry is added successfully, false otherwise.
	 */
	public static function add( $subject, $body, $headers, $attachments, $from, $recipients, $status, $provider, $connection_id, $account_id, $response, $initiator_name, $initiator_slug, $initiator_type, $context, $resend_count = 0 ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'quillsmtp_email_log';

		// Serialize arrays before inserting
		$headers     = serialize( $headers );
		$attachments = serialize( $attachments );
		$recipients  = serialize( $recipients );
		$context     = serialize( $context );
		$response    = serialize( $response );

		$data = array(
			'timestamp'      => gmdate( 'Y-m-d H:i:s', time() ),
			'subject'        => $subject,
			'body'           => $body,
			'headers'        => $headers,
			'attachments'    => $attachments,
			'from'           => $from,
			'recipients'     => $recipients,
			'status'         => $status,
			'provider'       => $provider,
			'connection_id'  => $connection_id,
			'account_id'     => $account_id,
			'response'       => $response,
			'initiator_name' => $initiator_name,
			'initiator_slug' => $initiator_slug,
			'initiator_type' => $initiator_type,
			'context'        => $context,
			'resend_count'   => $resend_count,
		);

		$format = array(
			'%s', // timestamp.
			'%s', // subject.
			'%s', // body.
			'%s', // headers.
			'%s', // attachments.
			'%s', // from.
			'%s', // recipients.
			'%s', // status.
			'%s', // provider.
			'%s', // connection_id.
			'%s', // account_id.
			'%s', // response.
			'%s', // initiator_name.
			'%s', // initiator_slug.
			'%s', // initiator_type.
			'%s', // context.
			'%d', // resend_count.
		);

		$columns = array_map(
			function( $column ) {
				return "`$column`";
			},
			array_keys( $data )
		);

		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		$query = $wpdb->prepare(
			"INSERT INTO $table_name (" . implode( ',', $columns ) . ') VALUES (' . implode( ',', $format ) . ')',
			array_values( $data )
		);

		return $wpdb->query( $query );
		// phpcs:enable
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

		return $wpdb->query( $result );
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
	public static function get_all( $status = false, $offset = 0, $count = 0, $start_date = false, $end_date = false, $search = false ) {
		global $wpdb;

		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		$where = '';
		$params = array();

		if ( $status ) {
			$where .= 'WHERE status = %s ';
			$params[] = $status;
		}

		if ( $start_date && $end_date ) {
			if ( ! empty( $where ) ) {
				$where .= ' AND ';
			} else {
				$where .= ' WHERE ';
			}
			$where .= 'timestamp BETWEEN %s AND %s ';
			array_push($params, $start_date, $end_date);
		}

		if ( $search ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			} else {
				$where .= ' AND ';
			}
			$where .= '(subject LIKE %s OR body LIKE %s OR headers LIKE %s OR to LIKE %s OR from LIKE %s OR recipients LIKE %s) ';
			$search_wildcard = '%' . $wpdb->esc_like( $search ) . '%';
			array_push($params, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard);
		}

		array_push($params, $offset, $count);

		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}quillsmtp_email_log $where ORDER BY timestamp DESC LIMIT %d, %d", $params );

		$results = $wpdb->get_results( $query, ARRAY_A );

		$prepared_results = [];

		foreach ( $results as $result ) {
			// Add result to prepared results.
			$prepared_results[] = self::prepare_log( $result );
		}

		return $prepared_results;
		// phpcs:enable
	}

	/**
	 * Prepare the log data for display or storage.
	 *
	 * @param array $log The log data to be prepared.
	 * @return array The prepared log data.
	 */
	public static function prepare_log( $log ) {
		// local datetime.
		$local_datetime = get_date_from_gmt( $log['timestamp'] );
		$connections    = Settings::get( 'connections' ) ?? [];
		$connection     = $connections[ $log['connection_id'] ] ?? [];
		$mailer         = [];

		if ( ! empty( $connection['mailer'] ) ) {
			$mailer = Mailers::get_mailer( $connection['mailer'] );
		}

		// Add result to prepared results.
		return [
			'log_id'          => $log['log_id'],
			'datetime'        => $log['timestamp'],
			'local_datetime'  => $local_datetime,
			'subject'         => $log['subject'],
			'body'            => $log['body'],
			'headers'         => maybe_unserialize( $log['headers'] ),
			'attachments'     => maybe_unserialize( $log['attachments'] ),
			'from'            => $log['from'],
			'recipients'      => maybe_unserialize( $log['recipients'] ),
			'status'          => $log['status'],
			'provider'        => $log['provider'],
			'provider_name'   => $mailer->name ?? '',
			'connection_id'   => $log['connection_id'],
			'connection_name' => $connection['name'] ?? '',
			'account_id'      => $log['account_id'],
			'account_name'    => isset( $mailer->accounts ) ? $mailer->accounts->get_account_data( $log['account_id'], 'name' ) ?? '' : '',
			'response'        => maybe_unserialize( $log['response'] ),
			'initiator_name'  => $log['initiator_name'],
			'initiator_slug'  => $log['initiator_slug'],
			'initiator_type'  => $log['initiator_type'],
			'context'         => maybe_unserialize( $log['context'] ),
			'resend_count'    => $log['resend_count'] > 0 ? $log['resend_count'] : '',
		];
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
			// Add result to prepared results.
			$prepared_results[] = self::prepare_log( $result );
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

		$where  = '';
		$params = array();
		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		if ( $status ) {
			$where .= 'WHERE status = %s ';
			$params[] = $status;
		}

		if ( $start_date && $end_date ) {
			if ( ! empty( $where ) ) {
				$where .= ' AND ';
			} else {
				$where .= ' WHERE ';
			}
			$where .= 'timestamp BETWEEN %s AND %s ';
			$params[] = $start_date;
			$params[] = $end_date;
		}

		if ( $search ) {
			if ( ! $where ) {
				$where .= 'WHERE ';
			} else {
				$where .= ' AND ';
			}
			$where .= '(subject LIKE %s OR body LIKE %s OR headers LIKE %s OR to LIKE %s OR from LIKE %s OR recipients LIKE %s) ';
			$search_wildcard = '%' . $wpdb->esc_like( $search ) . '%';
			array_push($params, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard, $search_wildcard);
		}

		$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}quillsmtp_email_log $where", $params );
		
		return (int) $wpdb->get_var( $query );
		// phpcs:enable
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

		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE source = %s",
				$source
			)
		);
		// phpcs:enable
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

		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		$format   = array_fill( 0, count( $log_ids ), '%d' );
		$query_in = '(' . implode( ',', $format ) . ')';
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE log_id IN {$query_in}", $log_ids ) );
		// phpcs:enable
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

		// phpcs:disable -- Ignoring this as it's a prepared query and caching is not needed.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}quillsmtp_email_log WHERE timestamp < %s",
				gmdate( 'Y-m-d H:i:s', $timestamp )
			)
		);
		// phpcs:enable
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

	/**
	 * Get top senders efficiently
	 *
	 * @param int         $limit Number of top senders to return
	 * @param string|bool $start_date Start date in Y-m-d H:i:s format
	 * @param string|bool $end_date End date in Y-m-d H:i:s format
	 * @return array Array of top senders
	 */
	public static function get_top_senders( $limit = 4, $start_date = false, $end_date = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE;

		$where_clauses = [];
		$where_values  = [];

		if ( $start_date ) {
			$where_clauses[] = 'timestamp >= %s';
			$where_values[]  = $start_date;
		}

		if ( $end_date ) {
			$where_clauses[] = 'timestamp <= %s';
			$where_values[]  = $end_date;
		}

		$where_sql = '';
		if ( ! empty( $where_clauses ) ) {
			$where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
		}

		try {
			$prepared_values = array_merge( $where_values, [ $limit ] );
			$sql             = $wpdb->prepare(
				"SELECT `from`, COUNT(*) as count 
				FROM $table_name 
				$where_sql
				GROUP BY `from` 
				ORDER BY count DESC 
				LIMIT %d",
				$prepared_values
			);

			$results = $wpdb->get_results( $sql, ARRAY_A );

			if ( ! is_array( $results ) ) {
				// Return empty array if query failed
				return [];
			}

			return array_map(
				function( $row ) {
					return [
						'from'  => $row['from'],
						'count' => (int) $row['count'],
					];
				},
				$results
			);
		} catch ( \Exception $e ) {
			return [];
		}
	}

	/**
	 * Get data for a specific period
	 *
	 * @param string|bool $start_date Start date in Y-m-d H:i:s format
	 * @param string|bool $end_date End date in Y-m-d H:i:s format
	 * @return array
	 */
	public static function get_period_data( $start_date = false, $end_date = false ) {
		$logs_for_each_day = [];

		// If we have valid dates, get data for each day
		if ( $start_date && $end_date ) {
			$start = new \DateTime( $start_date );
			$end   = new \DateTime( $end_date );

			$interval = new \DateInterval( 'P1D' );
			$period   = new \DatePeriod( $start, $interval, $end );

			foreach ( $period as $date ) {
				$date_str                       = $date->format( 'Y-m-d' );
				$logs_for_each_day[ $date_str ] = self::get_count(
					false,
					$date_str . ' 00:00:00',
					$date_str . ' 23:59:59'
				);
			}
		}

		// Get counts with the date range
		$success_logs = self::get_count( 'succeeded', $start_date, $end_date );
		$error_logs   = self::get_count( 'failed', $start_date, $end_date );
		$total_logs   = self::get_count( false, $start_date, $end_date );

		return [
			'total'   => $total_logs,
			'success' => $success_logs,
			'failed'  => $error_logs,
			'days'    => $logs_for_each_day,
		];
	}

	/**
	 * Calculate percentage change between two values
	 *
	 * @param int $current Current value
	 * @param int $previous Previous value
	 * @return int
	 */
	public static function calculate_percentage_change( $current, $previous ) {
		if ( $previous == 0 ) {
			return $current > 0 ? 100 : 0;
		}

		return round( ( ( $current - $previous ) / $previous ) * 100 );
	}

	/**
	 * Get all dashboard data for the home page
	 *
	 * @param string|bool $from_date Start date in MM/DD/YYYY format
	 * @param string|bool $to_date End date in MM/DD/YYYY format
	 * @return array All dashboard data
	 */
	public static function get_dashboard_data( $from_date = false, $to_date = false ) {
		if ( ! $from_date && ! $to_date ) {
			$now       = new \DateTime();
			$last_week = clone $now;
			$last_week->modify( '-7 days' );

			$from_date = $last_week->format( 'm/d/Y' );
			$to_date   = $now->format( 'm/d/Y' );
		}
		$formatted_from_date = $from_date ? self::format_date( $from_date ) : null;
		$formatted_to_date   = $to_date ? self::format_date( $to_date, '23:59:59' ) : null;
		$current_period_data = self::get_period_data(
			$formatted_from_date,
			$formatted_to_date
		);
		$periods             = self::get_periods_data();

		$metrics = [];
		foreach ( $periods as $period_key => $period ) {
			$current_data  = self::get_period_data( $period['start'], $period['end'] );
			$previous_data = self::get_period_data( $period['previous_start'], $period['previous_end'] );

			$metrics[ $period_key ] = [
				'current'           => $current_data,
				'previous'          => $previous_data,
				'percentage_change' => [
					'total'   => self::calculate_percentage_change(
						$current_data['total'],
						$previous_data['total']
					),
					'success' => self::calculate_percentage_change(
						$current_data['success'],
						$previous_data['success']
					),
					'failed'  => self::calculate_percentage_change(
						$current_data['failed'],
						$previous_data['failed']
					),
				],
			];
		}

		$top_senders = self::get_top_senders( 4, $formatted_from_date, $formatted_to_date );
		$recent_logs = self::get_all( false, 0, 4, $formatted_from_date, $formatted_to_date );

		return [
			'chart_data'  => $current_period_data,
			'metrics'     => $metrics,
			'top_senders' => $top_senders,
			'recent_logs' => $recent_logs,
		];
	}

	/**
	 * Get metrics data
	 *
	 * @param string $total 'today', 'yesterday', 'thisWeek', 'lastMonth'
	 * @param string $success 'today', 'yesterday', 'thisWeek', 'lastMonth'
	 * @param string $failed 'today', 'yesterday', 'thisWeek', 'lastMonth'
	 * @return array Metrics data
	 */
	public static function get_metrics_data( $total = '', $success = '', $failed = '' ) {
		$periods = self::get_periods_data();

		if ( ! isset( $periods[ $total ] ) || ! isset( $periods[ $success ] ) || ! isset( $periods[ $failed ] ) ) {
			return [
				'metrics' => [],
				'current' => [
					'total'   => 0,
					'success' => 0,
					'failed'  => 0,
				],
			];
		}

		$total_period   = $periods[ $total ];
		$success_period = $periods[ $success ];
		$failed_period  = $periods[ $failed ];

		$total_current   = self::get_count( false, $total_period['start'], $total_period['end'] );
		$success_current = self::get_count( 'succeeded', $success_period['start'], $success_period['end'] );
		$failed_current  = self::get_count( 'failed', $failed_period['start'], $failed_period['end'] );

		$total_previous   = self::get_count( false, $total_period['previous_start'], $total_period['previous_end'] );
		$success_previous = self::get_count( 'succeeded', $success_period['previous_start'], $success_period['previous_end'] );
		$failed_previous  = self::get_count( 'failed', $failed_period['previous_start'], $failed_period['previous_end'] );

		$metrics = [];

		// Create metrics structure that matches dashboard_data format
		$metrics[ $total ] = [
			'current'           => [
				'total' => $total_current,
			],
			'previous'          => [
				'total' => $total_previous,
			],
			'percentage_change' => [
				'total' => self::calculate_percentage_change( $total_current, $total_previous ),
			],
		];

		$metrics[ $success ] = [
			'current'           => [
				'success' => $success_current,
			],
			'previous'          => [
				'success' => $success_previous,
			],
			'percentage_change' => [
				'success' => self::calculate_percentage_change( $success_current, $success_previous ),
			],
		];

		$metrics[ $failed ] = [
			'current'           => [
				'failed' => $failed_current,
			],
			'previous'          => [
				'failed' => $failed_previous,
			],
			'percentage_change' => [
				'failed' => self::calculate_percentage_change( $failed_current, $failed_previous ),
			],
		];

		return [
			'metrics' => $metrics,
			'current' => [
				'total'   => $total_current,
				'success' => $success_current,
				'failed'  => $failed_current,
			],
		];
	}

	/**
	 * Get periods data
	 *
	 * @return array
	 */
	public static function get_periods_data() {
		return [
			'today'     => [
				'start'          => date( 'Y-m-d 00:00:00' ),
				'end'            => date( 'Y-m-d 23:59:59' ),
				'previous_start' => date( 'Y-m-d 00:00:00', strtotime( '-1 day' ) ),
				'previous_end'   => date( 'Y-m-d 23:59:59', strtotime( '-1 day' ) ),
			],
			'yesterday' => [
				'start'          => date( 'Y-m-d 00:00:00', strtotime( '-1 day' ) ),
				'end'            => date( 'Y-m-d 23:59:59', strtotime( '-1 day' ) ),
				'previous_start' => date( 'Y-m-d 00:00:00', strtotime( '-2 days' ) ),
				'previous_end'   => date( 'Y-m-d 23:59:59', strtotime( '-2 days' ) ),
			],
			'thisWeek'  => [
				'start'          => date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) ),
				'end'            => date( 'Y-m-d 23:59:59' ),
				'previous_start' => date( 'Y-m-d 00:00:00', strtotime( '-14 days' ) ),
				'previous_end'   => date( 'Y-m-d 23:59:59', strtotime( '-7 days' ) ),
			],
			'lastMonth' => [
				'start'          => date( 'Y-m-d 00:00:00', strtotime( '-1 month' ) ),
				'end'            => date( 'Y-m-d 23:59:59' ),
				'previous_start' => date( 'Y-m-d 00:00:00', strtotime( '-2 months' ) ),
				'previous_end'   => date( 'Y-m-d 23:59:59', strtotime( '-1 month' ) ),
			],
		];
	}

	/**
	 * Format date from MM/DD/YYYY to Y-m-d H:i:s
	 *
	 * @param string $date Date in MM/DD/YYYY format
	 * @param string $time Time to append (default 00:00:00)
	 * @return string Formatted date in Y-m-d H:i:s format
	 */
	public static function format_date( $date, $time = '00:00:00' ) {
		list( $month, $day, $year ) = explode( '/', $date );
		$value                      = "$year-$month-$day";
		if ( $time ) {
			$value .= " $time";
		}

		return $value;
	}
}
