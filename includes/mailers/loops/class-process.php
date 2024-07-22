<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Loops;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use WP_Error;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

	/**
	 * Set email header.
	 *
	 * @since 1.0.0
	 */
	public function set_header( $name, $value ) {}

	/**
	 * Set the From information for an email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function set_from( $email, $name ) {}

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

				if ( 'to' === $type ) {
					$this->body['email'] = $email_address;
				}
			}
		}

	}

	/**
	 * Set email subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subject
	 */
	public function set_subject( $subject ) {
		$this->body['dataVariables']['subject'] = sanitize_text_field( $subject );
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
				$this->body['dataVariables']['body'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['dataVariables']['body'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['dataVariables']['body'] = $content;
			} else {
				$this->body['dataVariables']['body'] = $content;
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
	public function set_reply_to( $emails ) {}

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

			$this->body['attachments'][] = array(
				'filename'    => $filename,
				'data'        => base64_encode( $this->filesystem->get_contents( $filepath ) ),
				'contentType' => mime_content_type( $filepath ),
			);
		}
	}

	/**
	 * Get the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_headers() {
		/**
		 * Filters Postmark email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_loops_mailer_get_headers', $this->headers );

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
		 * Filters Postmark email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_loops_mailer_get_body', $this->body );

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
			$account_id = $this->connection['account_id'];
			/** @var Account_API|WP_Error */ // phpcs:ignore
			$account_api = $this->provider->accounts->connect( $account_id );
			if ( is_wp_error( $account_api ) ) {
				throw new Exception( $account_api->get_error_message() );
			}

			$body   = $this->get_body();
			$result = $account_api->send( $body );
			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			if ( isset( $result['success'] ) && $result['success'] === true ) {
				$this->log_result(
					array(
						'status'   => self::SUCCEEDED,
						'response' => $result,
					)
				);

				return true;
			} else {
				$this->log_result(
					array(
						'status'   => self::FAILED,
						'response' => $result,
					)
				);

				return false;
			}
		} catch ( Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'Loops Send Email Error', 'quillsmtp-pro' ),
				array(
					'code'  => 'quillsmtp_loops_send_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					],
				)
			);
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $e->getMessage(),
				)
			);
			return false;
		}

	}
}
