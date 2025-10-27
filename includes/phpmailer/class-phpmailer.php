<?php
/**
 * Class PHPMailer
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage phpmailer
 */

namespace QuillSMTP\PHPMailer;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Settings;
use QuillSMTP\Mailers\Mailers;

/**
 * PHPMailer class.
 * Override the default PHPMailer class to catch emails.
 *
 * @since 1.0.0
 */
class PHPMailer extends \PHPMailer\PHPMailer\PHPMailer {

	/**
	 * Modify the default send method to catch emails.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send() {
		do_action( 'quillsmtp_before_get_settings' );
		$connections            = Settings::get( 'connections', [] ) ?? [];
		$default_connection_id  = null;

		/**
		 * Filter to enable/disable from email routing
		 *
		 * @param bool $enable Enable from email routing. Default true.
		 */
		$enable_from_email_routing = apply_filters( 'quillsmtp_enable_from_email_routing', true );

		// Try to find connection by from email first
		if ( $enable_from_email_routing && ! empty( $this->From ) ) {
			$matched_connection_id = Settings::get_connection_by_from_email( $this->From );
			if ( $matched_connection_id ) {
				$default_connection_id = $matched_connection_id;
			}
		}

		// Fall back to default connection selection if no match found
		if ( ! $default_connection_id ) {
			$default_connection_id = apply_filters( 'quillsmtp_default_connection', Settings::get( 'default_connection' ) );
		}

		$fallback_connection_id = Settings::get( 'fallback_connection' );
		$first_connection_id    = is_array( $connections ) ? array_key_first( $connections ) : null;
		$default_connection_id  = $default_connection_id ?: $first_connection_id;
		$default_connection     = $connections[ $default_connection_id ] ?? null;
		$fallback_connection    = $connections[ $fallback_connection_id ] ?? null;
		do_action( 'quillsmtp_after_get_settings' );

		if ( ! $default_connection ) {
			return parent::send();
		}

		// Store original values before any modifications (for fallback)
		$original_from      = $this->From;
		$original_from_name = $this->FromName;

		// Apply force from email and name BEFORE provider processing
		$force_from_email      = $default_connection['force_from_email'] ?? false;
		$connection_from_email = $default_connection['from_email'] ?? '';

		if ( $force_from_email && ! empty( $connection_from_email ) && is_email( $connection_from_email ) ) {
			$this->From = $connection_from_email;

			/**
			 * Fires when force from email is applied.
			 *
			 * @since 1.0.0
			 *
			 * @param string $forced_email The forced from email address.
			 * @param string $original_email The original from email address.
			 * @param string $connection_id The connection ID.
			 */
			do_action( 'quillsmtp_force_from_email_applied', $connection_from_email, $original_from, $default_connection_id );
		}

		$force_from_name      = $default_connection['force_from_name'] ?? false;
		$connection_from_name = $default_connection['from_name'] ?? '';

		if ( $force_from_name && ! empty( $connection_from_name ) ) {
			$this->FromName = $connection_from_name;

			/**
			 * Fires when force from name is applied.
			 *
			 * @since 1.0.0
			 *
			 * @param string $forced_name The forced from name.
			 * @param string $original_name The original from name.
			 * @param string $connection_id The connection ID.
			 */
			do_action( 'quillsmtp_force_from_name_applied', $connection_from_name, $original_from_name, $default_connection_id );
		}

		$mailer = Mailers::get_mailer( $default_connection['mailer'] );
		if ( ! $mailer ) {
			return false;
		}
		$result = $mailer->process( $this, $default_connection_id, $default_connection )->send();

		if ( ! $result && $fallback_connection ) {
			// Apply force from email for fallback connection too
			$force_from_email      = $fallback_connection['force_from_email'] ?? false;
			$connection_from_email = $fallback_connection['from_email'] ?? '';

			if ( $force_from_email && ! empty( $connection_from_email ) && is_email( $connection_from_email ) ) {
				$this->From = $connection_from_email;

				do_action( 'quillsmtp_force_from_email_applied', $connection_from_email, $original_from, $fallback_connection_id );
			}

			$force_from_name      = $fallback_connection['force_from_name'] ?? false;
			$connection_from_name = $fallback_connection['from_name'] ?? '';

			if ( $force_from_name && ! empty( $connection_from_name ) ) {
				$this->FromName = $connection_from_name;

				do_action( 'quillsmtp_force_from_name_applied', $connection_from_name, $original_from_name, $fallback_connection_id );
			}

			$mailer = Mailers::get_mailer( $fallback_connection['mailer'] );
			if ( ! $mailer ) {
				return false;
			}
			$result = $mailer->process( $this, $fallback_connection_id, $fallback_connection )->send();
		}

		return $result;
	}
}
