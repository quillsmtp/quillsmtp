<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SparkPost;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;

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
	public function set_header( $name, $value ) {
		$name = sanitize_text_field( $name );

		$this->body['content']['headers'][ $name ] = $value;
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

		$this->body['content']['from']['email'] = $email;

		if ( ! empty( $name ) ) {
			$this->body['content']['from']['name'] = sanitize_text_field( $name );
		}
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

				$user_data = [
					'address' => $email_address,
				];

				if ( ! empty( $name ) ) {
					$user_data['name'] = sanitize_text_field( $name );
				}

				$this->body['recipients'][] = $user_data;
			}

			if ( 'cc' === $type ) {
				$this->body['content']['headers']['CC'] = $this->addrs_format( $emails );
			}
		}
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function set_subject( $subject ) {

		$this->body['content']['subject'] = $subject;
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
				$this->body['content']['text'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['content']['html'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['content']['text'] = $content;
			} else {
				$this->body['content']['html'] = $content;
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

		$this->body['content']['reply_to'] = $this->addrs_format( $emails );
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

			$this->body['content']['attachments'][] = array(
				'data' => base64_encode( $this->filesystem->get_contents( $filepath ) ),
				'name' => $filename,
				'type' => mime_content_type( $filepath ),
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
		 * Filters SparkPost email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_sparkpost_mailer_get_headers', $this->body['content']['headers'] ?? [] );

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
		 * Filters SparkPost email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_sparkpost_mailer_get_body', $this->body );

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
		$account_id = $this->connection['account_id'];
		 /** @var Account_API|WP_Error */ // phpcs:ignore
		$account_api = $this->provider->accounts->connect( $account_id );
		if ( is_wp_error( $account_api ) ) {
			return $account_api;
		}
		$body       = $this->get_body();
		$send_email = $account_api->send( $body );

		if ( is_wp_error( $send_email ) ) {
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $send_email->get_error_message(),
				)
			);
			return false;
		}

		if ( isset( $send_email['results'] ) && 1 === $send_email['results']['total_accepted_recipients'] ) {
			$this->log_result(
				[
					'status'   => self::SUCCEEDED,
					'response' => $send_email,
				]
			);
		} else {
			$this->log_result(
				[
					'status'   => self::FAILED,
					'response' => $send_email,
				]
			);

			return false;
		}

		return true;
	}
}
