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
