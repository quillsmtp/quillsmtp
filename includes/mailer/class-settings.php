<?php
/**
 * Mailer Settings.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailer
 */

namespace QuillSMTP\Mailer;

defined( 'ABSPATH' ) || exit;

/**
 * Mailer Settings Class.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Mailer
	 *
	 * @var Mailer
	 */
	protected $mailer;

	/**
	 * Option key
	 *
	 * @var string
	 */
	protected $option_key;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Addon $mailer Addon.
	 */
	public function __construct( $mailer ) {
		$this->mailer     = $mailer;
		$this->option_key = "quillsmtp_{$this->mailer->slug}_settings";
	}

	/**
	 * Get settings
	 *
	 * @since 1.0.0
	 *
	 * @param false|string $property Property.
	 * @return mixed
	 */
	public function get( $property = false ) {
		$settings = get_option( $this->option_key, array() );
		if ( $property ) {
			return $settings[ $property ] ?? null;
		}
		return $settings;
	}

	/**
	 * Update settings
	 *
	 * @since 1.0.0
	 *
	 * @param array   $new_settings New settings.
	 * @param boolean $partial Partial update or complete.
	 * @return boolean
	 */
	public function update( $new_settings, $partial = true ) {
		$previous_settings = $this->get();
		if ( $partial ) {
			$new_settings = array_replace( $previous_settings, $new_settings );
		}
		if ( $new_settings === $previous_settings ) {
			return true;
		}
		return update_option( $this->option_key, $new_settings );
	}

	/**
	 * Delete settings
	 *
	 * @since 1.0.0
	 *
	 * @param false|string $property Property.
	 * @return boolean
	 */
	public function delete( $property = false ) {
		if ( $property ) {
			return $this->update( array( $property => null ) );
		} else {
			return delete_option( $this->option_key );
		}
	}
}
