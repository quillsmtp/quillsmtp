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
		$connections = Settings::get( 'connections' );
		$connection  = $connections['default'];
		$mailer      = Mailers::get_mailer( $connection['mailer'] );
		$mailer->process( $this, $connection )->send();
		$result = null;

		return $result;
	}
}
