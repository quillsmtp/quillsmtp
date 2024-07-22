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
use QuillSMTP\Mailers\Mailgun\Mailgun;
use QuillSMTP\Mailers\SMTPcom\SMTPcom;
use QuillSMTP\Mailers\SparkPost\SparkPost;
use QuillSMTP\Mailers\SMTP\SMTP;
use QuillSMTP\Mailers\PHPMailer\PHPMailer;
use QuillSMTP\Mailers\ElasticEmail\ElasticEmail;
use QuillSMTP\Mailers\SendGrid\SendGrid;
use QuillSMTP\Mailers\Gmail\Gmail;
use QuillSMTP\Mailers\PostMark\PostMark;
use QuillSMTP\Mailers\SendinBlue\SendinBlue;
use QuillSMTP\Mailers\Loops\Loops;
use QuillSMTP\Mailers\MailerSend\MailerSend;
use QuillSMTP\Mailers\Mailjet\Mailjet;
use QuillSMTP\Mailers\SMTP2GO\SMTP2GO;

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
			'sendlayer'    => SendLayer::class,
			'mailgun'      => Mailgun::class,
			'smtpcom'      => SMTPcom::class,
			'sparkpost'    => SparkPost::class,
			'smtp'         => SMTP::class,
			'phpmailer'    => PHPMailer::class,
			'elasticemail' => ElasticEmail::class,
			'sendgrid'     => SendGrid::class,
			'gmail'        => Gmail::class,
			'postmark'     => PostMark::class,
			'sendinblue'   => SendinBlue::class,
			'loops'        => Loops::class,
			'mailersend'   => MailerSend::class,
			'mailjet'      => Mailjet::class,
			'smtp2go'      => SMTP2GO::class,
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
