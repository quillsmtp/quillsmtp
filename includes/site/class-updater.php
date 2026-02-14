<?php
/**
 * Class: Updater
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Site;

/**
 * Updater Class
 *
 * @since 1.0.0
 */
class Updater {

	/**
	 * Class instance
	 *
	 * @var self instance
	 */
	private static $instance = null;

	/**
	 * Get class instance
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
		// Note: Custom update checks have been commented out to comply with WordPress.org guidelines.
		// WordPress.org provides update hosting, so plugins must not phone home to other servers
		// for updates or interfere with the built-in updater.

		// // set pro updates data.
		// add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'init_pro_updates' ) );

		// // filter pro plugins info.
		// add_filter( 'plugins_api', array( $this, 'filter_plugins_api' ), 10, 3 );

		// // add additional messages to pro plugin row.
		// add_action(
		// 	'in_plugin_update_message-quillsmtp-pro/quillsmtp-pro.php',
		// 	array( $this, 'add_in_plugin_update_message' ),
		// 	10
		// );

		// // clear pro updates cache on upgrader process complete.
		// add_action(
		// 	'upgrader_process_complete',
		// 	function() {
		// 		update_option( 'quillsmtp_pro_update_cache_needs_clear', true );
		// 	}
		// );
		// if ( get_option( 'quillsmtp_pro_update_cache_needs_clear' ) ) {
		// 	update_option( 'quillsmtp_pro_update_cache_needs_clear', false );
		// 	$this->clear_pro_update_cache();
		// }
	}

	/**
	 * Get Plugin Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plugin_data() {
		if ( ! function_exists( 'is_plugin_active' ) || ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// base dir of plugins (with trailing slash) instead of WP_PLUGIN_DIR.
		$plugins_dir      = trailingslashit( dirname( dirname( QUILLSMTP_PLUGIN_FILE ) ) );
		$plugin_file      = 'quillsmtp-pro/quillsmtp-pro.php';
		$full_plugin_file = $plugins_dir . 'quillsmtp-pro/quillsmtp-pro.php';
		$plugin_exists    = file_exists( $full_plugin_file );
		$plugin_data      = $plugin_exists ? get_plugin_data( $full_plugin_file ) : array();
		$plugin_slug      = 'quillsmtp-pro';

		$data = array(
			'is_installed'     => $plugin_exists,
			'is_active'        => is_plugin_active( $full_plugin_file ),
			'full_plugin_file' => $full_plugin_file,
			'plugin_file'      => $plugin_file,
			'version'          => $plugin_data['Version'] ?? '',
			'slug'             => $plugin_slug,
		);

		return $data;
	}

	/**
	 * Init pro updates
	 *
	 * @since 1.21.0
	 *
	 * @param object $transient Transient to filter.
	 * @return object
	 */
	public function init_pro_updates( $transient ) {
		$updates_data = $this->get_pro_update();
		$plugin       = $this->get_plugin_data();

		if ( $plugin['is_installed'] ) {
				$plugin_basename = plugin_basename( $plugin['full_plugin_file'] );

				$new_version = $updates_data[ $plugin['slug'] ]->new_version ?? null;
			if ( $new_version && version_compare( $plugin['version'], $new_version, '<' ) ) {
				$transient->response[ $plugin_basename ] = $updates_data[ $plugin['slug'] ];
				unset( $transient->no_update[ $plugin_basename ] );
			} else {
				$transient->no_update[ $plugin_basename ] = $updates_data[ $plugin['slug'] ];
				unset( $transient->response[ $plugin_basename ] );
			}
		}

		return $transient;
	}

	/**
	 * Filter plugins_api
	 *
	 * @since 1.21.0
	 *
	 * @param false|object|array $result Result.
	 * @param string             $action Action.
	 * @param object             $args Args.
	 * @return false|object|array
	 */
	public function filter_plugins_api( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) ) {
			return $result;
		}

		$updates_data       = $this->get_pro_update();
		$plugin_update_data = quillsmtp_objects_find( $updates_data, 'slug', $args->slug );
		if ( $plugin_update_data ) {
			return $plugin_update_data;
		}

		return $result;
	}

	/**
	 * Get pro updates data
	 *
	 * @since 1.21.0
	 *
	 * @return array
	 */
	private function get_pro_update() {
		// get updates payload.
		$payload = array(
			'edd_action' => 'get_version',
			'products'   => array(),
			'versions'   => array(
				'php'       => phpversion(),
				'wp'        => get_bloginfo( 'version' ),
				'quill-smtp' => QUILLSMTP_PLUGIN_VERSION,
			),
		);

		$license     = get_option( 'quillsmtp_license' );
		$license_key = ! empty( $license ) ? $license['key'] : '';
		$plugin      = $this->get_plugin_data();

		if ( $plugin['is_installed'] ) {
			$payload['products'][ $plugin['slug'] ] = array(
				'action'  => 'get_version',
				'license' => $license_key,
				'item_id' => "{$plugin['slug']}",
				'version' => $plugin['version'],
				'slug'    => basename( $plugin['full_plugin_file'], '.php' ),
				'author'  => 'quillsmtp.com',
				'url'     => home_url(),
				'beta'    => false,
			);
		}

		// check transient cache.
		$hash      = md5( wp_json_encode( $payload ) );
		$cache_key = 'quillsmtp_pro_updates';
		$transient = get_transient( $cache_key );
		if ( $transient && hash_equals( $hash, $transient['hash'] ) ) {
			return $transient['data'];
		}

		// get updates from the site api.
		$response = Site::instance()->api_request( $payload );
		if ( ! $response['success'] || ! $response['data'] ) {
			return array();
		}

		// prepare data.
		$data = array();
		foreach ( $response['data'] as $plugin_slug => $item ) {
			if ( 'quillsmtp-pro' === $plugin_slug ) {
				$data[ $plugin_slug ] = (object) array();
				foreach ( $item as $key => $value ) {
					$data[ $plugin_slug ]->{$key} = maybe_unserialize( $value );
				}
			}
		}

		// set transient cache.
		$transient = array(
			'hash' => $hash,
			'data' => $data,
			'time' => time(),
		);
		set_transient( $cache_key, $transient, 4 * HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Add pro update message.
	 *
	 * @since 1.21.0
	 *
	 * @return void
	 */
	public function add_in_plugin_update_message() {
		$license_info = License::instance()->get_license_info();
		$license_page = esc_url( admin_url( 'admin.php?page=quillsmtp&path=license' ) );

		// invalid license.
		if ( ! $license_info || 'valid' !== $license_info['status'] ) {
			echo '&nbsp;<strong><a href="' . esc_attr($license_page) . '">' . esc_html__( 'Enter valid license key for automatic updates.', 'quill-smtp' ) . '</a></strong>';
			return;
		}
	}

	/**
	 * Clear pro update cache
	 *
	 * @since 1.21.0
	 *
	 * @return void
	 */
	public function clear_pro_update_cache() {
		// Note: This method is now a no-op as custom update checks have been commented out
		// to comply with WordPress.org guidelines.

		// // delete updates transient.
		// delete_transient( 'quillsmtp_pro_updates' );

		// // clear wp plugins cache.
		// if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
		// 	require_once ABSPATH . 'wp-admin/includes/plugin.php';
		// }
		// wp_clean_plugins_cache();
	}

}
