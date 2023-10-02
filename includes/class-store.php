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
	 * Addons
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
			'default'    => array(
				'name'        => __( 'Default', 'quillsmtp' ),
				'description' => __( 'Use the default WordPress mailer', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/php/icon.svg',
				),
			),
			'sendlayer'  => array(
				'name'        => __( 'SendLayer', 'quillsmtp' ),
				'description' => __( 'Send emails using SendLayer SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sendlayer/icon.png',
				),
			),
			// 'sendgrid'   => array(
			// 'name'        => __( 'SendGrid', 'quillsmtp' ),
			// 'description' => __( 'Send emails using SendGrid SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/sendgrid/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/sendgrid/banner.png',
			// ),
			// ),
			// 'mailgun'    => array(
			// 'name'        => __( 'Mailgun', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Mailgun SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/mailgun/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/mailgun/banner.png',
			// ),
			// ),
			'sendinblue' => array(
				'name'        => __( 'Sendinblue', 'quillsmtp' ),
				'description' => __( 'Send emails using Sendinblue SMTP', 'quillsmtp' ),
				'assets'      => array(
					'icon' => QUILLSMTP_PLUGIN_URL . 'assets/mailers/sendinblue/icon.png',
				),
			),
			// 'pepipost'   => array(
			// 'name'        => __( 'Pepipost', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Pepipost SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/pepipost/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/pepipost/banner.png',
			// ),
			// ),
			// 'sparkpost'  => array(
			// 'name'        => __( 'SparkPost', 'quillsmtp' ),
			// 'description' => __( 'Send emails using SparkPost SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/sparkpost/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/sparkpost/banner.png',
			// ),
			// ),
			// 'gmail'      => array(
			// 'name'        => __( 'Gmail', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Gmail SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/gmail/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/gmail/banner.png',
			// ),
			// ),
			// 'outlook'    => array(
			// 'name'        => __( 'Outlook', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Outlook SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/outlook/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/outlook/banner.png',
			// ),
			// ),
			// 'zoho'       => array(
			// 'name'        => __( 'Zoho', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Zoho SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/zoho/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/zoho/banner.png',
			// ),
			// ),
			// 'amazon'     => array(
			// 'name'        => __( 'Amazon SES', 'quillsmtp' ),
			// 'description' => __( 'Send emails using Amazon SES SMTP', 'quillsmtp' ),
			// 'assets'      => array(
			// 'icon'   => QUILLSMTP_PLUGIN_URL . 'assets/mailer/amazon/icon.png',
			// 'banner' => QUILLSMTP_PLUGIN_URL . 'assets/mailer/amazon/banner.png',
			// ),
			// ),
		);
	}
}
