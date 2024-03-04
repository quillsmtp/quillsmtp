<?php
/**
 * Class Store
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP;

defined( 'ABSPATH' ) || exit;

/**
 * Store Class
 *
 * @since 1.0.0
 */
class Store {

	/**
	 * Mailers
	 *
	 * @var array
	 */
	private $mailers;

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
		$this->define_mailers();
	}

	/**
	 * Get all mailers
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all_mailers() {
		return $this->mailers;
	}

	/**
	 * Get all mailers
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function define_mailers() {
		$this->mailers = array(
			'phpmailer'    => array(
				'name'        => __( 'Default', 'quillsmtp' ),
				'description' => __( 'Use the default WordPress mailer', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/php/icon.svg',
				),
			),
			'sendlayer'    => array(
				'name'        => __( 'SendLayer', 'quillsmtp' ),
				'description' => __( 'Send emails using SendLayer SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sendlayer/icon.png',
				),
			),
			'sendgrid'     => array(
				'name'        => __( 'SendGrid', 'quillsmtp' ),
				'description' => __( 'Send emails using SendGrid SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sendgrid/icon.svg',
				),
			),
			'mailgun'      => array(
				'name'        => __( 'Mailgun', 'quillsmtp' ),
				'description' => __( 'Send emails using Mailgun SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/mailgun/icon.svg',
				),
			),
			'sendinblue'   => array(
				'name'        => __( 'Sendinblue', 'quillsmtp' ),
				'description' => __( 'Send emails using Sendinblue SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sendinblue/icon.svg',
				),
			),
			'postmark'     => array(
				'name'        => __( 'Postmark', 'quillsmtp' ),
				'description' => __( 'Send emails using Postmark SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/postmark/icon.svg',
				),
			),
			'smtpcom'      => array(
				'name'        => __( 'SMTP.com', 'quillsmtp' ),
				'description' => __( 'Send emails using SMTP.com SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/smtpcom/icon.svg',
				),
			),
			'sparkpost'    => array(
				'name'        => __( 'SparkPost', 'quillsmtp' ),
				'description' => __( 'Send emails using SparkPost SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sparkpost/icon.svg',
				),
			),
			'gmail'        => array(
				'name'        => __( 'Gmail', 'quillsmtp' ),
				'description' => __( 'Send emails using Gmail SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/gmail/icon.svg',
				),
			),
			'smtp'         => array(
				'name'        => __( 'Other SMTP', 'quillsmtp' ),
				'description' => __( 'Send emails using Other SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/smtp/icon.svg',
				),
			),
			'aws'          => array(
				'name'        => __( 'Amazon SES', 'quillsmtp' ),
				'description' => __( 'Send emails using Amazon SES SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/aws/icon.svg',
				),
			),
			'elasticemail' => array(
				'name'        => __( 'Elastic Email', 'quillsmtp' ),
				'description' => __( 'Send emails using Elastic Email SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/elasticemail/icon.svg',
				),
				'is_pro'      => true,
			),
			'outlook'      => array(
				'name'        => __( 'Outlook', 'quillsmtp' ),
				'description' => __( 'Send emails using Outlook SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/outlook/icon.svg',
				),
				'is_pro'      => true,
			),
			'zoho'         => array(
				'name'        => __( 'Zoho', 'quillsmtp' ),
				'description' => __( 'Send emails using Zoho SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/zoho/icon.svg',
				),
				'is_pro'      => true,
			),
			'smtp2go'      => array(
				'name'        => __( 'SMTP2GO', 'quillsmtp' ),
				'description' => __( 'Send emails using SMTP2GO SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/smtp2go/icon.png',
				),
				'is_pro'      => true,
			),
		);
	}
}
