<?php
/**
 * Mailers Class.
 *
 *  @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers;

use QuillSMTP\Mailers\SendLayer\SendLayer;
use QuillSMTP\Mailers\SendInBlue\SendInBlue;
use QuillSMTP\Mailers\PostMark\PostMark;
use QuillSMTP\Mailers\Mailgun\Mailgun;
use QuillSMTP\Mailers\SendGrid\SendGrid;
use QuillSMTP\Mailers\SMTPcom\SMTPcom;
use QuillSMTP\Mailers\Gmail\Gmail;

/**
 * Mailers Class.
 *
 * @since 1.0.0
 */
final class Mailers {

	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var QuillSMTP
	 */
	private static $instance;

	/**
	 * QuillSMTP Instance.
	 *
	 * Instantiates or reuses an instance of QuillSMTP.
	 *
	 * @since  1.0.0
	 * @static
	 *
	 * @return self - Single instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->load_mailers();
	}

	/**
	 * Load Mailers.
	 *
	 * @since 1.0.0
	 */
	private function load_mailers() {
		$mailers = self::get_mailers();

		foreach ( $mailers as $key => $mailer ) {
			$mailer::instance();
		}
	}

	/**
	 * Get mailer provider.
	 *
	 * @since 1.0.0
	 *
	 * @return \QuillSMTP\Mailer\Mailer[]
	 */
	public static function get_mailers() {
		$mailers = [
			'sendinblue' => SendInBlue::class,
			'sendlayer'  => SendLayer::class,
			'postmark'   => PostMark::class,
			'mailgun'    => Mailgun::class,
			'sendgrid'   => SendGrid::class,
			'smtpcom'    => SMTPcom::class,
			'gmail'      => Gmail::class,
		];

		return apply_filters( 'quillsmtp_mailers', $mailers );
	}

	/**
	 * Get mailer provider.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 *
	 * @return \QuillSMTP\Mailer\Provider\Provider
	 */
	public static function get_mailer( $key ) {
		$mailers = self::get_mailers();

		if ( isset( $mailers[ $key ] ) ) {
			$mailer = $mailers[ $key ];
			return $mailer::instance();
		}

		return false;
	}
}
