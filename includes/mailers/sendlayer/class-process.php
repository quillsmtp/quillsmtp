<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendLayer;

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

		$this->body['Headers'][ $name ] = $value;
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

		$this->body['From'] = array(
			'email' => $email,
			'name'  => sanitize_text_field( $name ),
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
				$user_data = [
					'email' => $email_address,
				];
				if ( ! empty( $name ) ) {
					$user_data['name'] = sanitize_text_field( $name );
				}
				switch ( $type ) {
					case 'to':
						$this->body['To'][] = $user_data;
						break;
					case 'cc':
						$this->body['Cc'][] = $user_data;
						break;
					case 'bcc':
						$this->body['Bcc'][] = $user_data;
						break;
				}
			}
		}
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function set_subject( $subject ) {

		$this->body['Subject'] = $subject;
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
				$this->body['ContentType']  = 'Plain';
				$this->body['PlainContent'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['ContentType'] = 'HTML';
				$this->body['HTMLContent'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['ContentType']  = 'Plain';
				$this->body['PlainContent'] = $content;
			} else {
				$this->body['ContentType'] = 'HTML';
				$this->body['HTMLContent'] = $content;
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

		$user_data = [
			'email' => $email,
		];

		if ( ! empty( $name ) ) {
			$user_data['name'] = sanitize_text_field( $name );
		}

		$this->body['ReplyTo'] = [ $user_data ];
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

			$this->body['Attachments'][] = array(
				'Content'     => base64_encode( $this->filesystem->get_contents( $filepath ) ),
				'Filename'    => $filename,
				'Type'        => mime_content_type( $filepath ),
				'Disposition' => 'attachment',
				'ContentId'   => empty( $attachment[7] ) ? '' : trim( (string) $attachment[7] ),
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
		 * Filters SendLayer email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_sendlayer_mailer_get_headers', $this->body['Headers'] );

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
		 * Filters SendLayer email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_sendlayer_mailer_get_body', $this->body );

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
			return false;
		}
		$send_email = $account_api->send( $this->get_body() );
		if ( is_wp_error( $send_email ) ) {
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $send_email->get_error_message(),
				)
			);
			return false;
		}

		$this->log_result(
			[
				'status'   => self::SUCCEEDED,
				'response' => $send_email,
			]
		);

		return true;
	}
}
