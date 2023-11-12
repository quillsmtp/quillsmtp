<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendInBlue;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use QuillSMTP\Vendor\Brevo\Client\Model\SendSmtpEmail;
use WP_Error;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

	/**
	 * The list of allowed attachment files extensions.
	 *
	 * @see   https://developers.sendinblue.com/reference#sendTransacEmail_attachment__title
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	// @formatter:off
	protected $allowed_attach_ext = array( 'xlsx', 'xls', 'ods', 'docx', 'docm', 'doc', 'csv', 'pdf', 'txt', 'gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff', 'rtf', 'bmp', 'cgm', 'css', 'shtml', 'html', 'htm', 'zip', 'xml', 'ppt', 'pptx', 'tar', 'ez', 'ics', 'mobi', 'msg', 'pub', 'eps', 'odt', 'mp3', 'm4a', 'm4v', 'wma', 'ogg', 'flac', 'wav', 'aif', 'aifc', 'aiff', 'mp4', 'mov', 'avi', 'mkv', 'mpeg', 'mpg', 'wmv' );
	// @formatter:on

	/**
	 * Set email header.
	 *
	 * @since 1.0.0
	 */
	public function set_header( $name, $value ) {

		$name = sanitize_text_field( $name );

		$this->body['headers'][ $name ] = $value;
	}

	/**
	 * Set the From information for an email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function set_from( $email, $name ) {

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return;
		}

		$this->body['sender'] = array(
			'email' => $email,
			'name'  => ! empty( $name ) ? sanitize_text_field( $name ) : '',
		);
	}

	/**
	 * Set email recipients: to, cc, bcc.
	 *
	 * @since 1.0.0
	 *
	 * @param array $recipients
	 */
	public function set_recipients( $recipients ) {

		if ( empty( $recipients ) ) {
			return;
		}

		foreach ( $recipients as $type => $emails ) {

			if ( empty( $emails ) || ! is_array( $emails ) ) {
				continue;
			}

			foreach ( $emails as $user ) {
				$email_address = isset( $user[0] ) ? $user[0] : false;
				$name          = isset( $user[1] ) ? $user[1] : false;

				if ( ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}

				$user_data = array(
					'email' => $email_address,
				);

				if ( ! empty( $name ) ) {
					$user_data['name'] = sanitize_text_field( $name );
				}

				$this->body[ $type ][] = $user_data;
			}
		}
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function set_subject( $subject ) {

		$this->body['subject'] = $subject;
	}

	/**
	 * Set email content.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $content
	 */
	public function set_content( $content ) {

		if ( empty( $content ) ) {
			return;
		}

		if ( is_array( $content ) ) {

			if ( ! empty( $content['text'] ) ) {
				$this->body['textContent'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['htmlContent'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['textContent'] = $content;
			} else {
				$this->body['htmlContent'] = $content;
			}
		}
	}

	/**
	 * Set the Reply To headers if not set already.
	 *
	 * @since 1.0.0
	 *
	 * @param array $emails
	 */
	public function set_reply_to( $emails ) {

		if ( empty( $emails ) ) {
			return;
		}

		// Get the first email address in the array.
		$user  = reset( $emails );
		$email = isset( $user[0] ) ? $user[0] : false;
		$name  = isset( $user[1] ) ? $user[1] : false;

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return;
		}

		$this->body['replyTo'] = array(
			'email' => $email,
			'name'  => ! empty( $name ) ? sanitize_text_field( $name ) : '',
		);
	}

	/**
	 * Set attachments for an email.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attachments The array of attachments data.
	 */
	public function set_attachments( $attachments ) {

		if ( empty( $attachments ) ) {
			return;
		}

		foreach ( $attachments as $attachment ) {

			$filepath = isset( $attachment[0] ) ? $attachment[0] : false;
			$filename = isset( $attachment[2] ) ? $attachment[2] : false;

			if ( empty( $filename ) || empty( $filepath ) ) {
				continue;
			}

			$ext = pathinfo( $filename, PATHINFO_EXTENSION );

			if ( ! in_array( $ext, $this->allowed_attach_ext, true ) ) {
				continue;
			}

			$this->body['attachment'][] = array(
				'name'    => $filename,
				'content' => base64_encode( $this->filesystem->get_contents( $filepath ) ),
			);
		}
	}

	/**
	 * Set the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_headers() {

		/**
		 * Filters Sendinblue email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_sendinblue_mailer_get_headers', $this->body['headers'] );

		return $headers;
	}

	/**
	 * Get the email body.
	 *
	 * @since 1.0.0
	 *
	 * @return SendSmtpEmail
	 */
	public function get_body() {

		/**
		 * Filters Sendinblue email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_sendinblue_mailer_get_body', $this->body );

		return new SendSmtpEmail( $body );
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function send() {
		$account_id = $this->connection['account_id'];
		/** @var Account_API|WP_Error */ // phpcs:ignore
		$account_api = $this->provider->accounts->connect( $account_id );
		if ( is_wp_error( $account_api ) ) {
			return $account_api;
		}
		$api_instance = $account_api->get_api_instance();
		error_log( wp_json_encode( $this->get_body() ) );
		try {
			$result = $api_instance->sendTransacEmail( $this->get_body() );
			if ( $result->getMessageId() ) {
				$this->log_result(
					[
						'status'   => self::SUCCEEDED,
						'response' => [
							'message_id' => $result->getMessageId(),
						],
					]
				);
				return true;
			} else {
				$this->log_result(
					[
						'status'   => self::FAILED,
						'response' => [
							'message' => $result->getMessage(),
						],
					]
				);
				return new WP_Error( 'quillsmtp_sendinblue_error', $result->getMessage() );
			}
		} catch ( Exception $e ) {
			$this->log_result(
				[
					'status'   => self::FAILED,
					'response' => [
						'message' => $e->getMessage(),
					],
				]
			);
			return new WP_Error( 'quillsmtp_sendinblue_error', $e->getMessage() );
		}
	}
}
