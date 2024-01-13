<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\PHPMailer;

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
		try {
			if ( ! $this->phpmailer->preSend() ) {
				$this->log_result(
					array(
						'status'   => self::FAILED,
						'response' => $this->phpmailer->ErrorInfo,
					)
				);
				return false;
			}

			$send_email = $this->phpmailer->postSend();
			if ( ! $send_email ) {
				$this->log_result(
					array(
						'status'   => self::FAILED,
						'response' => $this->phpmailer->ErrorInfo,
					)
				);
				return false;
			} else {
				$this->log_result(
					array(
						'status'   => self::SUCCEEDED,
						'response' => $send_email ? __( 'Email sent successfully.', 'quillsmtp' ) : __( 'Email failed to send.', 'quillsmtp' ),
					)
				);

				return true;
			}
		} catch ( Exception $exc ) {
			$this->phpmailer->mailHeader = '';
			$this->phpmailer->setError( $exc->getMessage() );
			if ( $this->phpmailer->exceptions ) {
				throw $exc;
			}

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
