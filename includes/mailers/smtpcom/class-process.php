<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTPcom;

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

		$this->body['custom_headers'][ $name ] = $value;
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

		$this->body['originator']['from'] = [
			'address' => $email,
			'name'    => sanitize_text_field( $name ),
		];
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

				switch ( $type ) {
					case 'to':
						$this->body['recipients']['to'][] = $user_data;
						break;
					case 'cc':
						$this->body['recipients']['cc'][] = $user_data;
						break;
					case 'bcc':
						$this->body['recipients']['bcc'][] = $user_data;
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

		$default_part = [
			'type'    => 'plain/text',
			'charset' => $this->phpmailer->CharSet,
			'content' => '',
		];

		$parts = [];

		if ( is_array( $content ) ) {

			if ( ! empty( $content['text'] ) ) {
				$parts[] = array_merge(
					$default_part,
					[
						'content' => $content['text'],
					]
				);
			}

			if ( ! empty( $content['html'] ) ) {
				$parts[] = array_merge(
					$default_part,
					[
						'type'    => 'text/html',
						'content' => $content['html'],
					]
				);
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$parts[] = array_merge(
					$default_part,
					[
						'content' => $content,
					]
				);
			} else {
				$parts[] = array_merge(
					$default_part,
					[
						'type'    => 'text/html',
						'content' => $content,
					]
				);
			}
		}

		$this->body['body']['parts'] = $parts;
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
			'address' => $email,
		];

		if ( ! empty( $name ) ) {
			$user_data['name'] = sanitize_text_field( $name );
		}

		$this->body['originator']['reply_to'] = $user_data;
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

			$this->body['body']['attachments'][] = array(
				'content'     => base64_encode( $this->filesystem->get_contents( $filepath ) ),
				'filename'    => $filename,
				'type'        => mime_content_type( $filepath ),
				'disposition' => in_array( $attachment[6], [ 'inline', 'attachment' ], true ) ? $attachment[6] : 'attachment',
				'cid'         => empty( $attachment[7] ) ? '' : trim( (string) $attachment[7] ),
				'encoding'    => $ext === 'pdf' ? 'base64' : 'quoted-printable',
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
		 * Filters SMTPcom email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_smtpcom_mailer_get_headers', $this->body['custom_headers'] );

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
		 * Filters SMTPcom email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_smtpcom_mailer_get_body', $this->body );

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
			$channel         = $account_api->get_sender_name();
			$body            = $this->get_body();
			$body['channel'] = $channel;
			$send_email      = $account_api->send( $body );

			if ( is_wp_error( $send_email ) ) {
				throw new Exception( $send_email->get_error_message() );
			}

			if ( 'success' === $send_email['status'] ) {
				$this->log_result(
					[
						'status'   => self::SUCCEEDED,
						'response' => $send_email,
					]
				);
				return true;
			} else {
				throw new Exception( $send_email['message'] );
			}
		} catch ( Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'SMTPcom API Error', 'quill-smtp' ),
				array(
					'code'  => 'quillsmtp_smtpcom_send_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					],
				)
			);
			$this->log_result(
				[
					'status'   => self::FAILED,
					'response' => $e->getMessage(),
				]
			);
			return false;
		}
	}
}
