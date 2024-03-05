<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTP;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

	/**
	 * Set the From information for an email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function set_from( $email, $name ) {
		$this->phpmailer->setFrom( $email, $name );
	}

	/**
	 * Set email recipients: to, cc, bcc.
	 *
	 * @since 1.0.0
	 *
	 * @param array $recipients
	 */
	public function set_recipients( $recipients ) {}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function set_subject( $subject ) {}

	/**
	 * Set email content.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $content
	 */
	public function set_content( $content ) {}

	/**
	 * Set the Reply To headers if not set already.
	 *
	 * @since 1.0.0
	 *
	 * @param array $emails
	 */
	public function set_reply_to( $emails ) {}

	/**
	 * Set attachments for an email.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attachments The array of attachments data.
	 */
	public function set_attachments( $attachments ) {   }

	/**
	 * Get the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_headers() {

		/**
		 * Filters SMTP email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_smtp_mailer_get_headers', $this->headers );

		return $headers;
	}

	/**
	 * Get the email body.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_body() {

		/**
		 * Filters SMTP email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_smtp_mailer_get_body', $this->body );

		return $body;
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send() {
		global $phpmailer;

		try {
			$account_id = $this->connection['account_id'];
        	/** @var Account_API|WP_Error */ // phpcs:ignore
			$account_api = $this->provider->accounts->connect( $account_id );
			if ( is_wp_error( $account_api ) ) {
				throw new Exception( $account_api->get_error_message() );
			}
			$smtp_host      = $account_api->get_smtp_host();
			$smtp_port      = $account_api->get_smtp_port();
			$encryption     = $account_api->get_encryption();
			$auto_tls       = $account_api->get_auto_tls();
			$authentication = $account_api->get_authentication();
			$username       = $account_api->get_username();
			$password       = $account_api->get_password();

			$phpmailer->Mailer      = 'smtp';
			$phpmailer->Host        = $smtp_host;
			$phpmailer->Port        = $smtp_port;
			$phpmailer->SMTPSecure  = $encryption;
			$phpmailer->SMTPAutoTLS = $auto_tls;

			if ( $authentication ) {
				$phpmailer->SMTPAuth = true;
				$phpmailer->Username = $username;
				$phpmailer->Password = $password;
			}

			if ( ! $this->phpmailer->preSend() ) {
				throw new Exception( $this->phpmailer->ErrorInfo );
			}

			$send_email = $this->phpmailer->postSend();
			if ( ! $send_email ) {
				throw new Exception( $this->phpmailer->ErrorInfo );
			} else {
				$this->log_result(
					array(
						'status'   => self::SUCCEEDED,
						'response' => $send_email,
					)
				);
				return true;
			}
		} catch ( Exception $exc ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'PHPMailer Error', 'quillsmtp' ),
				array(
					'code'  => 'quillsmtp_phpmailer_send_error',
					'error' => [
						'code'  => $exc->getCode(),
						'error' => $exc->getMessage(),
						'data'  => $exc->getTraceAsString(),
					],
				)
			);
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $this->phpmailer->ErrorInfo,
				)
			);
			return false;
		}
	}
}
