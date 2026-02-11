<?php
/**
 * Class Settings
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP;

defined( 'ABSPATH' ) || exit;

/**
 * Settings Class
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Option name where to store all settings
	 *
	 * @since 1.0.0
	 */
	const OPTION_NAME = 'quillsmtp_settings';

	/**
	 * Get a setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get( $key, $default = false ) {
		$settings = self::get_all();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Update a setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param mixed  $value Value.
	 * @return boolean
	 */
	public static function update( $key, $value ) {
		return self::update_many( array( $key => $value ) );
	}

	/**
	 * Delete a setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @return boolean
	 */
	public static function delete( $key ) {
		$settings = self::get_all();
		unset( $settings[ $key ] );
		return self::update_all( $settings );
	}

	/**
	 * Get all settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_all() {
		do_action( 'quillsmtp_before_get_settings' );
		$settings = get_option( self::OPTION_NAME, array() );
		do_action( 'quillsmtp_after_get_settings' );

		return $settings;
	}

	/**
	 * Update many settings
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_settings New settings.
	 * @return boolean
	 */
	public static function update_many( $new_settings ) {
		$old_settings = self::get_all();
		$settings     = array_replace( $old_settings, $new_settings );
		return self::update_all( $settings );
	}

	/**
	 * Update all settings
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Settings.
	 * @return boolean
	 */
	public static function update_all( $settings ) {
		return update_option( self::OPTION_NAME, $settings );
	}

	/**
	 * Delete all settings
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public static function delete_all() {
		return delete_option( self::OPTION_NAME );
	}

	/**
	 * Get the default connection ID
	 *
	 * Returns the configured default connection, or falls back to the first
	 * available connection if no default is set.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null Connection ID if found, null otherwise.
	 */
	public static function get_default_connection() {
		$default_connection = self::get( 'default_connection' );

		// If default connection is set and not empty, return it
		if ( ! empty( $default_connection ) ) {
			return $default_connection;
		}

		// Fall back to the first available connection
		$connections = self::get( 'connections', array() );
		if ( is_array( $connections ) && ! empty( $connections ) ) {
			return array_key_first( $connections );
		}

		return null;
	}

	/**
	 * Get smart route for email sending
	 *
	 * Returns the connection routing information based on smart routing rules:
	 * 1. Explicit connection set via filter (highest priority)
	 * 2. Auto-routing based on from email address
	 * 3. Filtered/default connection
	 * 4. First available connection (fallback)
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $from_email Optional from email address for auto-routing.
	 * @return array {
	 *     Smart route information.
	 *
	 *     @type string|null $default_connection_id    The primary connection ID to use.
	 *     @type array|null  $default_connection       The primary connection configuration.
	 *     @type string|null $fallback_connection_id   The fallback connection ID.
	 *     @type array|null  $fallback_connection      The fallback connection configuration.
	 *     @type array       $connections              All available connections.
	 * }
	 */
	public static function get_smart_route( $from_email = null ) {
		$connections           = self::get( 'connections', array() ) ?? array();
		$default_connection_id = null;

		/**
		 * Filter to enable/disable from email routing
		 *
		 * @param bool $enable Enable from email routing. Default true.
		 */
		$enable_from_email_routing = apply_filters( 'quillsmtp_enable_from_email_routing', true );

		// Get the default connection (returns configured default or first available connection)
		$settings_default_connection = self::get_default_connection();

		// Track if a filter has modified the connection
		$filter_modified_connection = false;
		$filtered_connection_id     = apply_filters(
			'quillsmtp_default_connection',
			$settings_default_connection,
			$filter_modified_connection
		);

		// Use a separate filter to detect if connection was explicitly set
		$explicit_connection = apply_filters( 'quillsmtp_explicit_connection', null );

		// If explicit connection is set via filter, use it and skip auto-routing
		if ( $explicit_connection ) {
			$default_connection_id = $explicit_connection;
		} else {
			// Try auto-routing if no explicit connection selected
			if ( $enable_from_email_routing && ! empty( $from_email ) ) {
				$matched_connection_id = self::get_connection_by_from_email( $from_email );
				if ( $matched_connection_id ) {
					$default_connection_id = $matched_connection_id;
				}
			}

			// Fall back to filtered/default connection if auto-routing didn't match
			if ( ! $default_connection_id ) {
				$default_connection_id = $filtered_connection_id;
			}
		}

		// Final fallback to first connection
		$first_connection_id   = is_array( $connections ) ? array_key_first( $connections ) : null;
		$default_connection_id = $default_connection_id ?: $first_connection_id;

		// Get connection configurations
		$default_connection     = $connections[ $default_connection_id ] ?? null;
		$fallback_connection_id = self::get( 'fallback_connection' );
		$fallback_connection    = $connections[ $fallback_connection_id ] ?? null;

		return array(
			'default_connection_id'  => $default_connection_id,
			'default_connection'     => $default_connection,
			'fallback_connection_id' => $fallback_connection_id,
			'fallback_connection'    => $fallback_connection,
			'connections'            => $connections,
		);
	}

	/**
	 * Get connection by from email address
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_email From email address.
	 * @return string|null Connection ID if found, null otherwise.
	 */
	public static function get_connection_by_from_email( $from_email ) {
		if ( empty( $from_email ) || ! is_email( $from_email ) ) {
			return null;
		}

		$connections = self::get( 'connections', array() );
		if ( ! is_array( $connections ) || empty( $connections ) ) {
			return null;
		}

		// Normalize email for comparison
		$from_email = strtolower( trim( $from_email ) );

		foreach ( $connections as $connection_id => $connection ) {
			$connection_from_email = $connection['from_email'] ?? '';
			if ( empty( $connection_from_email ) ) {
				continue;
			}

			// Normalize connection email for comparison
			$connection_from_email = strtolower( trim( $connection_from_email ) );

			// Exact match
			if ( $from_email === $connection_from_email ) {
				/**
				 * Filter the matched connection ID
				 *
				 * @param string $connection_id Connection ID that matched.
				 * @param string $from_email From email address.
				 * @param array  $connection Connection configuration.
				 */
				return apply_filters( 'quillsmtp_matched_connection', $connection_id, $from_email, $connection );
			}
		}

		return null;
	}

}
