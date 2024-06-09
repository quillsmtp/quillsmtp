<?php
/**
 * Class Core
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

namespace QuillSMTP;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Store;

/**
 * Core Class
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * Set admin config
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public static function set_admin_config() {
		// Admin email address.
		$admin_email = get_option( 'admin_email' );
		$ajax_url    = admin_url( 'admin-ajax.php' );
		$nonce       = wp_create_nonce( 'quillsmtp-admin' );

		wp_add_inline_script(
			'qsmtp-config',
			'qsmtp.config.setAdminUrl("' . admin_url() . '");' .
			'qsmtp.config.setPluginDirUrl("' . QUILLSMTP_PLUGIN_URL . '");' .
			'qsmtp.config.setStoreMailers(' . wp_json_encode( Store::instance()->get_all_mailers() ) . ');' .
			'qsmtp.config.setAdminEmail("' . $admin_email . '");' .
			'qsmtp.config.setAjaxUrl("' . $ajax_url . '");' .
			'qsmtp.config.setNonce("' . $nonce . '");' .
			'qsmtp.config.setIsMultisite("' . ( is_multisite() ? '1' : '0' ) . '");' .
			'qsmtp.config.setIsMainSite("' . ( is_main_site() ? '1' : '0' ) . '");' .
			'qsmtp.config.setWpMailConfig(' . wp_json_encode( self::wp_mail_config() ) . ');' .
			'qsmtp.config.setEasySMTPConfig(' . wp_json_encode( self::easy_smtp_config() ) . ');' .
			'qsmtp.config.setFluentSMTPConfig(' . wp_json_encode( self::fluent_smtp_config() ) . ');'
		);
	}

	/**
	 * Is WP Mail has settings
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function wp_mail_config() {
		$wp_mail_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $wp_mail_settings ) ) {
			return false;
		}

		$settings = [];

		$mail_settings                = $wp_mail_settings['mail'] ?? [];
		$settings['from_email']       = self::get_mail_setting( 'from_email', $mail_settings['from_email'] ?? '' );
		$settings['from_name']        = self::get_mail_setting( 'from_name', $mail_settings['from_name'] ?? '' );
		$settings['mailer']           = self::get_mail_setting( 'mailer', $mail_settings['mailer'] ?? '' );
		$settings['return_path']      = self::get_mail_setting( 'return_path', $mail_settings['return_path'] ?? '' );
		$settings['from_name_force']  = self::get_mail_setting( 'from_name_force', $mail_settings['from_name_force'] ?? '' );
		$settings['from_email_force'] = self::get_mail_setting( 'from_email_force', $mail_settings['from_email_force'] ?? '' );

		switch ( $mail_settings['mailer'] ) {
			case 'smtp':
				$smtp_settings                      = $wp_mail_settings['smtp'] ?? [];
				$settings['smtp']['smtp_host']      = self::get_custom_smtp_setting( 'host', $smtp_settings['host'] ?? '' );
				$settings['smtp']['smtp_port']      = self::get_custom_smtp_setting( 'port', $smtp_settings['port'] ?? '' );
				$settings['smtp']['encryption']     = self::get_custom_smtp_setting( 'encryption', $smtp_settings['encryption'] ?? '' );
				$settings['smtp']['username']       = self::get_custom_smtp_setting( 'user', $smtp_settings['user'] ?? '' );
				$smtp_pass                          = self::get_custom_smtp_setting( 'pass', $smtp_settings['pass'] ?? '' );
				$settings['smtp']['authentication'] = self::get_custom_smtp_setting( 'auth', $smtp_settings['auth'] ?? false );
				$settings['smtp']['auto_tls']       = self::get_custom_smtp_setting( 'autotls', $smtp_settings['autotls'] ?? '' );
				if ( $settings['smtp']['authentication'] && $smtp_pass ) {
					$settings['smtp']['password'] = self::wp_mail_smtp_password_decoder( $smtp_pass );
				}
				break;
			case 'mailgun':
				$mailgun_settings                   = $wp_mail_settings['mailgun'] ?? [];
				$settings['mailgun']['domain_name'] = self::get_mailgun_setting( 'domain', $mailgun_settings['domain'] ?? '' );
				$settings['mailgun']['api_key']     = self::get_mailgun_setting( 'api_key', $mailgun_settings['api_key'] ?? '' );
				$settings['mailgun']['region']      = self::get_mailgun_setting( 'region', $mailgun_settings['region'] ?? '' );
				break;
			case 'sendgrid':
				$sendgrid_settings                      = $wp_mail_settings['sendgrid'] ?? [];
				$settings['sendgrid']['api_key']        = self::get_sendgrid_setting( 'api_key', $sendgrid_settings['api_key'] ?? '' );
				$settings['sendgrid']['sending_domain'] = self::get_sendgrid_setting( 'domain', $sendgrid_settings['domain'] ?? '' );
				break;
			case 'sparkpost':
				$sparkpost_settings               = $wp_mail_settings['sparkpost'] ?? [];
				$settings['sparkpost']['api_key'] = self::get_sparkpost_setting( 'api_key', $sparkpost_settings['api_key'] ?? '' );
				$settings['sparkpost']['region']  = self::get_sparkpost_setting( 'region', $sparkpost_settings['region'] ?? '' );
				break;
			case 'postmark':
				$postmark_settings                         = $wp_mail_settings['postmark'] ?? [];
				$settings['postmark']['api_token']         = self::get_postmark_setting( 'api_token', $postmark_settings['api_token'] ?? '' );
				$settings['postmark']['message_stream_id'] = self::get_postmark_setting( 'message_stream', $postmark_settings['message_stream'] ?? '' );
				break;
			case 'sendinblue':
				$sendinblue_settings                      = $wp_mail_settings['sendinblue'] ?? [];
				$settings['sendinblue']['api_key']        = self::get_sendinblue_setting( 'api_key', $sendinblue_settings['api_key'] ?? '' );
				$settings['sendinblue']['sending_domain'] = self::get_sendinblue_setting( 'domain', $sendinblue_settings['domain'] ?? '' );
				break;
			case 'smtpcom':
				$smtpcom_settings                   = $wp_mail_settings['smtpcom'] ?? [];
				$settings['smtpcom']['api_key']     = self::get_smtpcom_setting( 'api_key', $smtpcom_settings['api_key'] ?? '' );
				$settings['smtpcom']['sender_name'] = self::get_smtpcom_setting( 'channel', $smtpcom_settings['channel'] ?? '' );
				break;
			case 'sendlayer':
				$sendlayer_settings               = $wp_mail_settings['sendlayer'] ?? [];
				$settings['sendlayer']['api_key'] = self::get_sendlayer_setting( 'api_key', $sendlayer_settings['api_key'] ?? '' );
				break;
		}

		return $settings;
	}

	/**
	 * Get mail setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_mail_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'from_email':
				if ( defined( 'WPMS_MAIL_FROM_NAME' ) && WPMS_MAIL_FROM_NAME ) {
					$value = WPMS_MAIL_FROM_NAME;
				}
				break;
			case 'from_name':
				if ( defined( 'WPMS_MAIL_FROM' ) && WPMS_MAIL_FROM ) {
					$value = WPMS_MAIL_FROM;
				}
				break;
			case 'mailer':
				if ( defined( 'WPMS_MAILER' ) && WPMS_MAILER ) {
					$value = WPMS_MAILER;
				}
				break;
			case 'return_path':
				if ( defined( 'WPMS_RETURN_PATH' ) && WPMS_RETURN_PATH ) {
					$value = WPMS_RETURN_PATH;
				}
				break;
			case 'from_name_force':
				if ( defined( 'WPMS_MAIL_FROM_NAME_FORCE' ) && WPMS_MAIL_FROM_NAME_FORCE ) {
					$value = WPMS_MAIL_FROM_NAME_FORCE;
				}
				break;
			case 'from_email_force':
				if ( defined( 'WPMS_MAIL_FROM_FORCE' ) && WPMS_MAIL_FROM_FORCE ) {
					$value = WPMS_MAIL_FROM_FORCE;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get custom smtp settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_custom_smtp_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'host':
				if ( defined( 'WPMS_SMTP_HOST' ) && WPMS_SMTP_HOST ) {
					$value = WPMS_SMTP_HOST;
				}
				break;
			case 'port':
				if ( defined( 'WPMS_SMTP_PORT' ) && WPMS_SMTP_PORT ) {
					$value = WPMS_SMTP_PORT;
				}
				break;
			case 'encryption':
				if ( defined( 'WPMS_SMTP_ENCRYPTION' ) && WPMS_SMTP_ENCRYPTION ) {
					$value = WPMS_SMTP_ENCRYPTION;
				}
				break;
			case 'user':
				if ( defined( 'WPMS_SMTP_USER' ) && WPMS_SMTP_USER ) {
					$value = WPMS_SMTP_USER;
				}
				break;
			case 'pass':
				if ( defined( 'WPMS_SMTP_PASS' ) && WPMS_SMTP_PASS ) {
					$value = WPMS_SMTP_PASS;
				}
				break;
			case 'auth':
				if ( defined( 'WPMS_SMTP_AUTH' ) && WPMS_SMTP_AUTH ) {
					$value = WPMS_SMTP_AUTH;
				}
				break;
			case 'autotls':
				if ( defined( 'WPMS_SMTP_AUTOTLS' ) && WPMS_SMTP_AUTOTLS ) {
					$value = WPMS_SMTP_AUTOTLS;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get mailgun settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_mailgun_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'domain':
				if ( defined( 'WPMS_MAILGUN_DOMAIN' ) && WPMS_MAILGUN_DOMAIN ) {
					$value = WPMS_MAILGUN_DOMAIN;
				}
				break;
			case 'api_key':
				if ( defined( 'WPMS_MAILGUN_API_KEY' ) && WPMS_MAILGUN_API_KEY ) {
					$value = WPMS_MAILGUN_API_KEY;
				}
				break;
			case 'region':
				if ( defined( 'WPMS_MAILGUN_REGION' ) && WPMS_MAILGUN_REGION ) {
					$value = WPMS_MAILGUN_REGION;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get sendgrid settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_sendgrid_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'WPMS_SENDGRID_API_KEY' ) && WPMS_SENDGRID_API_KEY ) {
					$value = WPMS_SENDGRID_API_KEY;
				}
				break;
			case 'domain':
				if ( defined( 'WPMS_SENDGRID_DOMAIN' ) && WPMS_SENDGRID_DOMAIN ) {
					$value = WPMS_SENDGRID_DOMAIN;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get sparkpost settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_sparkpost_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'WPMS_SPARKPOST_API_KEY' ) && WPMS_SPARKPOST_API_KEY ) {
					$value = WPMS_SPARKPOST_API_KEY;
				}
				break;
			case 'region':
				if ( defined( 'WPMS_SPARKPOST_REGION' ) && WPMS_SPARKPOST_REGION ) {
					$value = WPMS_SPARKPOST_REGION;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get postmark settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_postmark_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_token':
				if ( defined( 'WPMS_POSTMARK_API_TOKEN' ) && WPMS_POSTMARK_API_TOKEN ) {
					$value = WPMS_POSTMARK_API_TOKEN;
				}
				break;
			case 'message_stream':
				if ( defined( 'WPMS_POSTMARK_MESSAGE_STREAM' ) && WPMS_POSTMARK_MESSAGE_STREAM ) {
					$value = WPMS_POSTMARK_MESSAGE_STREAM;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get sendinblue settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_sendinblue_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'WPMS_SENDINBLUE_API_KEY' ) && WPMS_SENDINBLUE_API_KEY ) {
					$value = WPMS_SENDINBLUE_API_KEY;
				}
				break;
			case 'domain':
				if ( defined( 'WPMS_SENDINBLUE_DOMAIN' ) && WPMS_SENDINBLUE_DOMAIN ) {
					$value = WPMS_SENDINBLUE_DOMAIN;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get SMTPcom settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_smtpcom_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'WPMS_SMTPCOM_API_KEY' ) && WPMS_SMTPCOM_API_KEY ) {
					$value = WPMS_SMTPCOM_API_KEY;
				}
				break;
			case 'channel':
				if ( defined( 'WPMS_SMTPCOM_CHANNEL' ) && WPMS_SMTPCOM_CHANNEL ) {
					$value = WPMS_SMTPCOM_CHANNEL;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get SendLayer settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_sendlayer_setting( $key, $default ) {
		if ( ! defined( 'WPMS_ON' ) || ! WPMS_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'wp_mail_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'WPMS_SENDLAYER_API_KEY' ) && WPMS_SENDLAYER_API_KEY ) {
					$value = WPMS_SENDLAYER_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * WP Mail SMTP password decoder
	 *
	 * @since 1.0.0
	 *
	 * @param string $encrypted Password.
	 *
	 * @return string
	 */
	public static function wp_mail_smtp_password_decoder( $encrypted ) {
		if ( apply_filters( 'wp_mail_smtp_helpers_crypto_stop', false ) ) {
			return $encrypted;
		}

		// Unpack base64 message.
		$decoded = base64_decode( $encrypted ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( false === $decoded ) {
			return $encrypted;
		}

		// Include polyfill if mbstring PHP extension is not enabled.
		if ( ! function_exists( 'mb_strlen' ) || ! function_exists( 'mb_substr' ) ) {
			return $encrypted;
		}

		// phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound, PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_macbytesFound
		if ( mb_strlen( $decoded, '8bit' ) < ( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES ) ) {
			return $encrypted;
		}

		// Pull nonce and ciphertext out of unpacked message.
		$nonce      = mb_substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' ); // phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound
		$ciphertext = mb_substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' ); // phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound

		$key = self::get_wp_mail_smtp_secret_key();

		if ( empty( $key ) ) {
			return $encrypted;
		}

		// Decrypt it.
		$message = sodium_crypto_secretbox_open( // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_crypto_secretbox_openFound
			$ciphertext,
			$nonce,
			$key
		);

		// Check for decryption failures.
		if ( false === $message ) {
			return $encrypted;
		}

		try {
			sodium_memzero( $ciphertext ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_memzeroFound
			sodium_memzero( $key ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_memzeroFound
		} catch ( \Exception $e ) {
			return $message;
		}

		return $message;
	}

	/**
	 * Get WP Mail SMTP secret key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_wp_mail_smtp_secret_key() {
		if ( defined( 'WPMS_CRYPTO_KEY' ) ) {
			return WPMS_CRYPTO_KEY;
		}

		$secret_key = apply_filters( 'wp_mail_smtp_helpers_crypto_get_secret_key', get_option( 'wp_mail_smtp_mail_key', false ) );

		// If we already have the secret, send it back.
		if ( false !== $secret_key ) {
			return base64_decode( $secret_key ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		}

		return $secret_key;
	}


	/**
	 * Get EasySMTP settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function easy_smtp_config() {
		$easy_smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $easy_smtp_settings ) ) {
			return false;
		}

		$settings                     = [];
		$mail_settings                = $easy_smtp_settings['mail'] ?? [];
		$settings['from_email']       = self::get_mail_setting( 'from_email', $mail_settings['from_email'] ?? '' );
		$settings['from_name']        = self::get_mail_setting( 'from_name', $mail_settings['from_name'] ?? '' );
		$settings['mailer']           = self::get_mail_setting( 'mailer', $mail_settings['mailer'] ?? '' );
		$settings['return_path']      = self::get_mail_setting( 'return_path', $mail_settings['return_path'] ?? '' );
		$settings['from_name_force']  = self::get_mail_setting( 'from_name_force', $mail_settings['from_name_force'] ?? '' );
		$settings['from_email_force'] = self::get_mail_setting( 'from_email_force', $mail_settings['from_email_force'] ?? '' );

		switch ( $mail_settings['mailer'] ) {
			case 'smtp':
				$smtp_settings                      = $easy_smtp_settings['smtp'] ?? [];
				$settings['smtp']['smtp_host']      = self::get_custom_smtp_setting( 'host', $smtp_settings['host'] ?? '' );
				$settings['smtp']['smtp_port']      = self::get_custom_smtp_setting( 'port', $smtp_settings['port'] ?? '' );
				$settings['smtp']['encryption']     = self::get_custom_smtp_setting( 'encryption', $smtp_settings['encryption'] ?? '' );
				$settings['smtp']['username']       = self::get_custom_smtp_setting( 'user', $smtp_settings['user'] ?? '' );
				$smtp_pass                          = self::get_custom_smtp_setting( 'pass', $smtp_settings['pass'] ?? '' );
				$settings['smtp']['authentication'] = self::get_custom_smtp_setting( 'auth', $smtp_settings['auth'] ?? false );
				$settings['smtp']['auto_tls']       = self::get_custom_smtp_setting( 'autotls', $smtp_settings['autotls'] ?? '' );
				if ( $settings['smtp']['authentication'] && $smtp_pass ) {
					$settings['smtp']['password'] = self::easy_smtp_password_decoder( $smtp_pass );
				}
				break;
			case 'mailgun':
				$mailgun_settings                   = $easy_smtp_settings['mailgun'] ?? [];
				$settings['mailgun']['domain_name'] = self::get_easy_mailgun_setting( 'domain', $mailgun_settings['domain'] ?? '' );
				$settings['mailgun']['api_key']     = self::get_easy_mailgun_setting( 'api_key', $mailgun_settings['api_key'] ?? '' );
				$settings['mailgun']['region']      = self::get_easy_mailgun_setting( 'region', $mailgun_settings['region'] ?? '' );
				break;
			case 'sendgrid':
				$sendgrid_settings                      = $easy_smtp_settings['sendgrid'] ?? [];
				$settings['sendgrid']['api_key']        = self::get_easy_sendgrid_setting( 'api_key', $sendgrid_settings['api_key'] ?? '' );
				$settings['sendgrid']['sending_domain'] = self::get_easy_sendgrid_setting( 'domain', $sendgrid_settings['domain'] ?? '' );
				break;
			case 'postmark':
				$postmark_settings                         = $easy_smtp_settings['postmark'] ?? [];
				$settings['postmark']['api_token']         = self::get_easy_postmark_setting( 'api_token', $postmark_settings['api_token'] ?? '' );
				$settings['postmark']['message_stream_id'] = self::get_easy_postmark_setting( 'message_stream', $postmark_settings['message_stream'] ?? '' );
				break;
			case 'sendinblue':
				$sendinblue_settings                      = $easy_smtp_settings['sendinblue'] ?? [];
				$settings['sendinblue']['api_key']        = self::get_easy_sendinblue_setting( 'api_key', $sendinblue_settings['api_key'] ?? '' );
				$settings['sendinblue']['sending_domain'] = self::get_easy_sendinblue_setting( 'domain', $sendinblue_settings['domain'] ?? '' );
				break;
			case 'smtpcom':
				$smtpcom_settings                   = $easy_smtp_settings['smtpcom'] ?? [];
				$settings['smtpcom']['api_key']     = self::get_easy_smtpcom_setting( 'api_key', $smtpcom_settings['api_key'] ?? '' );
				$settings['smtpcom']['sender_name'] = self::get_easy_smtpcom_setting( 'channel', $smtpcom_settings['channel'] ?? '' );
				break;
			case 'sendlayer':
				$sendlayer_settings               = $easy_smtp_settings['sendlayer'] ?? [];
				$settings['sendlayer']['api_key'] = self::get_easy_sendlayer_setting( 'api_key', $sendlayer_settings['api_key'] ?? '' );
				break;
		}

		return $settings;
	}

	/**
	 * Get Easy Mailgun settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_mailgun_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'domain':
				if ( defined( 'EASY_WP_SMTP_MAILGUN_DOMAIN' ) && EASY_WP_SMTP_MAILGUN_DOMAIN ) {
					$value = EASY_WP_SMTP_MAILGUN_DOMAIN;
				}
				break;
			case 'api_key':
				if ( defined( 'EASY_WP_SMTP_MAILGUN_API_KEY' ) && EASY_WP_SMTP_MAILGUN_API_KEY ) {
					$value = EASY_WP_SMTP_MAILGUN_API_KEY;
				}
				break;
			case 'region':
				if ( defined( 'EASY_WP_SMTP_MAILGUN_REGION' ) && EASY_WP_SMTP_MAILGUN_REGION ) {
					$value = EASY_WP_SMTP_MAILGUN_REGION;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get Easy Sendgrid settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_sendgrid_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'EASY_WP_SMTP_SENDGRID_API_KEY' ) && EASY_WP_SMTP_SENDGRID_API_KEY ) {
					$value = EASY_WP_SMTP_SENDGRID_API_KEY;
				}
				break;
			case 'domain':
				if ( defined( 'EASY_WP_SMTP_SENDGRID_DOMAIN' ) && EASY_WP_SMTP_SENDGRID_DOMAIN ) {
					$value = EASY_WP_SMTP_SENDGRID_DOMAIN;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get Easy Postmark settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_postmark_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_token':
				if ( defined( 'EASY_WP_SMTP_POSTMARK_API_TOKEN' ) && EASY_WP_SMTP_POSTMARK_API_TOKEN ) {
					$value = EASY_WP_SMTP_POSTMARK_API_TOKEN;
				}
				break;
			case 'message_stream':
				if ( defined( 'EASY_WP_SMTP_POSTMARK_MESSAGE_STREAM' ) && EASY_WP_SMTP_POSTMARK_MESSAGE_STREAM ) {
					$value = EASY_WP_SMTP_POSTMARK_MESSAGE_STREAM;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get Easy Sendinblue settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_sendinblue_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'EASY_WP_SMTP_SENDINBLUE_API_KEY' ) && EASY_WP_SMTP_SENDINBLUE_API_KEY ) {
					$value = EASY_WP_SMTP_SENDINBLUE_API_KEY;
				}
				break;
			case 'domain':
				if ( defined( 'EASY_WP_SMTP_SENDINBLUE_DOMAIN' ) && EASY_WP_SMTP_SENDINBLUE_DOMAIN ) {
					$value = EASY_WP_SMTP_SENDINBLUE_DOMAIN;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get Easy SMTPcom settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_smtpcom_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'EASY_WP_SMTP_SMTPCOM_API_KEY' ) && EASY_WP_SMTP_SMTPCOM_API_KEY ) {
					$value = EASY_WP_SMTP_SMTPCOM_API_KEY;
				}
				break;
			case 'channel':
				if ( defined( 'EASY_WP_SMTP_SMTPCOM_CHANNEL' ) && EASY_WP_SMTP_SMTPCOM_CHANNEL ) {
					$value = EASY_WP_SMTP_SMTPCOM_CHANNEL;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get Easy Sendlayer settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_easy_sendlayer_setting( $key, $default ) {
		if ( ! defined( 'EASY_WP_SMTP_ON' ) || ! EASY_WP_SMTP_ON ) {
			return $default;
		}
		$smtp_settings = get_option( 'easy_wp_smtp' );
		if ( empty( $smtp_settings ) ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'EASY_WP_SMTP_SENDLAYER_API_KEY' ) && EASY_WP_SMTP_SENDLAYER_API_KEY ) {
					$value = EASY_WP_SMTP_SENDLAYER_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Easy SMTP password decoder
	 *
	 * @since 1.0.0
	 *
	 * @param string $encrypted Password.
	 *
	 * @return string
	 */
	public static function easy_smtp_password_decoder( $encrypted ) {
		if ( apply_filters( 'easy_wp_smtp_helpers_crypto_stop', false ) ) {
			return $encrypted;
		}

		// Unpack base64 message.
		$decoded = base64_decode( $encrypted ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( false === $decoded ) {
			return $encrypted;
		}

		// Include polyfill if mbstring PHP extension is not enabled.
		if ( ! function_exists( 'mb_strlen' ) || ! function_exists( 'mb_substr' ) ) {
			return $encrypted;
		}

		// phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound, PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_macbytesFound
		if ( mb_strlen( $decoded, '8bit' ) < ( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES ) ) {
			return $encrypted;
		}

		// Pull nonce and ciphertext out of unpacked message.
		$nonce      = mb_substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' ); // phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound
		$ciphertext = mb_substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' ); // phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_crypto_secretbox_noncebytesFound

		$key = self::get_easy_smtp_secret_key();

		if ( empty( $key ) ) {
			return $encrypted;
		}

		// Decrypt it.
		$message = sodium_crypto_secretbox_open( // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_crypto_secretbox_openFound
			$ciphertext,
			$nonce,
			$key
		);

		// Check for decryption failures.
		if ( false === $message ) {
			return $encrypted;
		}

		try {
			sodium_memzero( $ciphertext ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_memzeroFound
			sodium_memzero( $key ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.sodium_memzeroFound
		} catch ( \Exception $e ) {
			return $message;
		}

		return $message;
	}

	/**
	 * Get Easy SMTP secret key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_easy_smtp_secret_key() {
		if ( defined( 'EASY_WP_SMTP_CRYPTO_KEY' ) ) {
			return EASY_WP_SMTP_CRYPTO_KEY;
		}

		$secret_key = apply_filters( 'easy_wp_smtp_helpers_crypto_get_secret_key', get_option( 'easy_wp_smtp_mail_key' ) );

		// If we already have the secret, send it back.
		if ( false !== $secret_key ) {
			return base64_decode( $secret_key ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		}

		return $secret_key;
	}

	/**
	 * Get FluentSMTP settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function fluent_smtp_config() {
		$fluent_smtp_settings = get_option( 'fluentmail-settings' );
		if ( empty( $fluent_smtp_settings ) ) {
			return false;
		}

		$misc_settings      = $fluent_smtp_settings['misc'] ?? [];
		$default_connection = $misc_settings['default_connection'] ?? '';
		$connections        = $fluent_smtp_settings['connections'] ?? [];
		$use_encrypt        = $fluent_smtp_settings['use_encrypt'] ?? 'no';

		if ( empty( $default_connection ) || empty( $connections ) ) {
			return false;
		}

		$connection_settings = $connections[ $default_connection ] ?? [];
		if ( empty( $connection_settings ) ) {
			return false;
		}

		$settings                     = [];
		$connection_settings          = $connection_settings['provider_settings'];
		$settings['from_email']       = $connection_settings['sender_email'] ?? '';
		$settings['from_name']        = $connection_settings['sender_name'] ?? '';
		$settings['mailer']           = $connection_settings['provider'] ?? '';
		$settings['from_name_force']  = 'no' === ( $connection_settings['force_from_name'] ?? 'no' ) ? false : true;
		$settings['from_email_force'] = 'no' === ( $connection_settings['force_from_email'] ?? 'no' ) ? false : true;

		switch ( $settings['mailer'] ) {
			case 'smtp':
				$settings['smtp']['smtp_host']      = $connection_settings['host'] ?? '';
				$settings['smtp']['smtp_port']      = $connection_settings['port'] ?? '';
				$settings['smtp']['encryption']     = $connection_settings['encryption'] ?? '';
				$settings['smtp']['username']       = $connection_settings['username'] ?? '';
				$settings['smtp']['authentication'] = 'yes' === $connection_settings['auth'] ?? 'no' ? true : false;
				$settings['smtp']['auto_tls']       = $connection_settings['autotls'] ?? false;
				$password                           = $connection_settings['password'] ?? '';
				$settings['smtp']['password']       = 'yes' === $use_encrypt ? self::fluent_decrypt( $password ) : $password;
				break;
			case 'elasticmail':
				$api_key                             = self::get_fluent_elasticmail_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['elasticemail']['api_key'] = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				break;
			case 'mailgun':
				$settings['mailgun']['domain_name'] = self::get_fluent_mailgun_setting( 'domain', $connection_settings['domain'] ?? '', $connection_settings );
				$api_key                            = self::get_fluent_mailgun_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['mailgun']['api_key']     = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				break;
			case 'sendgrid':
				$api_key                         = self::get_fluent_sendgrid_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['sendgrid']['api_key'] = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				break;
			case 'postmark':
				$api_key                                   = self::get_fluent_postmark_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['postmark']['api_token']         = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				$settings['postmark']['message_stream_id'] = self::get_fluent_postmark_setting( 'message_stream', $connection_settings['message_stream'] ?? '', $connection_settings );
				break;
			case 'sendinblue':
				$api_key                           = self::get_fluent_sendinblue_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['sendinblue']['api_key'] = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				break;
			case 'sparkpost':
				$api_key                          = self::get_fluent_sparkpost_setting( 'api_key', $connection_settings['api_key'] ?? '', $connection_settings );
				$settings['sparkpost']['api_key'] = 'yes' === $use_encrypt ? self::fluent_decrypt( $api_key ) : $api_key;
				break;
		}

		return $settings;
	}

	/**
	 * Get Fluent ElasticEmail settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	public static function get_fluent_elasticmail_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'FLUENTMAIL_ELASTICMAIL_API_KEY' ) && FLUENTMAIL_ELASTICMAIL_API_KEY ) {
					$value = FLUENTMAIL_ELASTICMAIL_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get FluentSMTP mailgun settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_mailgun_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'domain':
				if ( defined( 'FLUENTMAIL_MAILGUN_DOMAIN' ) && FLUENTMAIL_MAILGUN_DOMAIN ) {
					$value = FLUENTMAIL_MAILGUN_DOMAIN;
				}
				break;
			case 'api_key':
				if ( defined( 'FLUENTMAIL_MAILGUN_API_KEY' ) && FLUENTMAIL_MAILGUN_API_KEY ) {
					$value = FLUENTMAIL_MAILGUN_API_KEY;
				}
		}

		return $value;
	}

	/**
	 * Get FluentSMTP sendgrid settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_sendgrid_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'FLUENTMAIL_SENDGRID_API_KEY' ) && FLUENTMAIL_SENDGRID_API_KEY ) {
					$value = FLUENTMAIL_SENDGRID_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get FluentSMTP postmark settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_postmark_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'FLUENTMAIL_POSTMARK_API_KEY' ) && FLUENTMAIL_POSTMARK_API_KEY ) {
					$value = FLUENTMAIL_POSTMARK_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get FluentSMTP sendinblue settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_sendinblue_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'FLUENTMAIL_SENDINBLUE_API_KEY' ) && FLUENTMAIL_SENDINBLUE_API_KEY ) {
					$value = FLUENTMAIL_SENDINBLUE_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get FluentSMTP sparkpost settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_sparkpost_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'api_key':
				if ( defined( 'FLUENTMAIL_SPARKPOST_API_KEY' ) && FLUENTMAIL_SPARKPOST_API_KEY ) {
					$value = FLUENTMAIL_SPARKPOST_API_KEY;
				}
				break;
		}

		return $value;
	}

	/**
	 * Get FluentSMTP smtp settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key.
	 * @param string $default Default value.
	 * @param array  $settings Settings.
	 *
	 * @return string
	 */
	public static function get_fluent_smtp_setting( $key, $default, $settings ) {
		if ( 'wp_config' !== $settings['key_store'] ) {
			return $default;
		}

		$value = $default;
		switch ( $key ) {
			case 'username':
				if ( defined( 'FLUENTMAIL_SMTP_USERNAME' ) && FLUENTMAIL_SMTP_USERNAME ) {
					$value = FLUENTMAIL_SMTP_USERNAME;
				}
				break;
			case 'password':
				if ( defined( 'FLUENTMAIL_SMTP_PASSWORD' ) && FLUENTMAIL_SMTP_PASSWORD ) {
					$value = FLUENTMAIL_SMTP_PASSWORD;
				}
				break;
		}

		return $value;
	}


	/**
	 * Decrypts a value using AES-256 encryption with a provided key and salt.
	 *
	 * @param string $value The value to decrypt.
	 * @return string|false The decrypted value, or false if decryption fails.
	 */
	public static function fluent_decrypt( $value ) {
		if ( ! $value ) {
			return $value;
		}

		if ( ! extension_loaded( 'openssl' ) ) {
			return $value;
		}

		if ( defined( 'FLUENTMAIL_ENCRYPT_SALT' ) ) {
			$salt = FLUENTMAIL_ENCRYPT_SALT;
		} else {
			$salt = ( defined( 'LOGGED_IN_SALT' ) && '' !== LOGGED_IN_SALT ) ? LOGGED_IN_SALT : 'this-is-a-fallback-salt-but-not-secure';
		}

		if ( defined( 'FLUENTMAIL_ENCRYPT_KEY' ) ) {
			$key = FLUENTMAIL_ENCRYPT_KEY;
		} else {
			$key = ( defined( 'LOGGED_IN_KEY' ) && '' !== LOGGED_IN_KEY ) ? LOGGED_IN_KEY : 'this-is-a-fallback-key-but-not-secure';
		}

		$raw_value = base64_decode( $value, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = substr( $raw_value, 0, $ivlen );

		$raw_value = substr( $raw_value, $ivlen );

		$newValue = openssl_decrypt( $raw_value, $method, $key, 0, $iv );
		if ( ! $newValue || substr( $newValue, -strlen( $salt ) ) !== $salt ) {
			return false;
		}

		return substr( $newValue, 0, -strlen( $salt ) );
	}
}
