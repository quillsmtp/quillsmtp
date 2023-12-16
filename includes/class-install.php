<?php

/**
 * Install: class Install
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP;

/**
 * Class Install is responsible for main set up.
 * create needed database tables.
 * assign capabilities to user roles.
 *
 * @since 1.0.0
 */
class Install {

	/**
	 * Install QuillSMTP
	 *
	 * @since 1.0.0
	 * @static
	 */
	public static function install() {
		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'quillsmtp_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'quillsmtp_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();
		self::create_cron_jobs();

		delete_transient( 'quillsmtp_installing' );
	}

	/**
	 * Create DB Tables
	 *
	 * @since 1.0.0
	 */
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}quillsmtp_log (
				log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				timestamp datetime NOT NULL,
				level smallint(4) NOT NULL,
				source varchar(200) NOT NULL,
				message longtext NOT NULL,
				context longtext NULL,
				PRIMARY KEY (log_id),
				KEY level (level)
			) $charset_collate;
			CREATE TABLE {$wpdb->prefix}quillsmtp_task_meta (
				ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				action_id BIGINT UNSIGNED,
				hook varchar(255) NOT NULL,
				group_slug varchar(255) NOT NULL,
				value longtext NOT NULL,
				date_created datetime NOT NULL,
				PRIMARY KEY  (ID),
				KEY action_id (action_id)
			) $charset_collate;";

		dbDelta( $sql );
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		wp_clear_scheduled_hook( 'quillsmtp_cleanup_logs' );

		wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'daily', 'quillsmtp_cleanup_logs' );
	}
}
