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
		$result = parent::send();
		error_log( 'PHPMailer send() called' );
		if ( $result ) {
			do_action( 'quillsmtp_email_sent', $this );
		} else {
			do_action( 'quillsmtp_email_failed', $this );
		}

		return $result;
	}
}
