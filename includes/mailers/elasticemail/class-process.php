<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\ElasticEmail;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

	/**
	 * Attachment boundary.
	 *
	 * @var string
	 */
	protected $boundary = '';

	/**
	 * Attachment payload.
	 *
	 * @var string
	 */
	protected $payload = '';

	/**
	 * Content type
	 *
	 * @var string
	 */
	protected $content_type = 'application/x-www-form-urlencoded';

	/**
	 * Set email header.
	 *
	 * @since 1.0.0
	 */
	public function set_header( $name, $value ) {

		$name                            = sanitize_text_field( $name );
		$this->body[ "headers_{$name}" ] = "{$name}: {$value}";
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

		$this->body['from'] = $email;
		if ( ! empty( $name ) ) {
			$this->body['fromName'] = sanitize_text_field( $name );
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

			$type_address = [];
			foreach ( $emails as $user ) {
				$email_address = isset( $user[0] ) ? $user[0] : false;
				$name          = isset( $user[1] ) ? $user[1] : false;

				if ( ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}

				$type_address[] = $this->phpmailer->addrFormat( [ $email_address, $name ] );
			}

			if ( ! empty( $type_address ) ) {
				switch ( $type ) {
					case 'to':
						$this->body['msgTo'] = implode( ';', $type_address );
						break;
					case 'cc':
						$this->body['msgCC'] = implode( ';', $type_address );
						break;
					case 'bcc':
						$this->body['msgBcc'] = implode( ';', $type_address );
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

		if ( is_array( $content ) ) {

			if ( ! empty( $content['text'] ) ) {
				$this->body['bodyText'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['bodyHtml'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['bodyText'] = $content;
			} else {
				$this->body['bodyHtml'] = $content;
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

		$this->body['replyTo'] = $email;

		if ( ! empty( $name ) ) {
			$this->body['replyToName'] = sanitize_text_field( $name );
		}
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

		$payload = '';
		$data    = [];

		foreach ( $attachments as $attachment ) {
			$filepath = isset( $attachment[0] ) ? $attachment[0] : false;
			$filename = isset( $attachment[2] ) ? $attachment[2] : false;
			$file     = $this->filesystem->get_contents( $filepath );

			if ( $file === false ) {
				continue;
			}

			$data[] = [
				'content' => $file,
				'name'    => $filename,
			];
		}

		if ( ! empty( $data ) ) {

			$boundary = md5( time() );

			foreach ( $this->body as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $child_value ) {
						$payload .= '--' . $boundary;
						$payload .= "\r\n";
						$payload .= 'Content-Disposition: form-data; name="' . $key . "\"\r\n\r\n";
						$payload .= $child_value;
						$payload .= "\r\n";
					}
				} else {
					$payload .= '--' . $boundary;
					$payload .= "\r\n";
					$payload .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
					$payload .= $value;
					$payload .= "\r\n";
				}
			}

			foreach ( $data as $key => $attachment ) {
				$payload .= '--' . $boundary;
				$payload .= "\r\n";
				$payload .= 'Content-Disposition: form-data; name="attachment[' . $key . ']"; filename="' . $attachment['name'] . '"' . "\r\n\r\n";
				$payload .= $attachment['content'];
				$payload .= "\r\n";
			}

			$payload .= '--' . $boundary . '--';

			$this->body = $payload;

			$this->content_type = 'multipart/form-data; boundary=' . $boundary;
		}
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
		 * Filters ElasticEmail email body.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body Email body.
		 */
		$body = apply_filters( 'quillsmtp_elasticemail_mailer_get_body', $this->body );

		return $body;
	}

	/**
	 * Parse body.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $body
	 *
	 * @return array
	 */
	public function parse_body( $body ) {

		if ( is_string( $body ) ) {
			return $body;
		}

		$payload  = '';
		$boundary = md5( time() );

		foreach ( $body as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $child_value ) {
					$payload .= '--' . $boundary;
					$payload .= "\r\n";
					$payload .= 'Content-Disposition: form-data; name="' . $key . "\"\r\n\r\n";
					$payload .= $child_value;
					$payload .= "\r\n";
				}
			} else {
				$payload .= '--' . $boundary;
				$payload .= "\r\n";
				$payload .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
				$payload .= $value;
				$payload .= "\r\n";
			}
		}

		$payload           .= '--' . $boundary . '--';
		$this->content_type = 'multipart/form-data; boundary=' . $boundary;

		return $payload;
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

			$body       = $this->get_body();
			$body       = $this->parse_body( $body );
			$send_email = $account_api->send( $body, $this->content_type );

			if ( is_wp_error( $send_email ) ) {
				throw new Exception( $send_email->get_error_message() );
			}

			$this->log_result(
				[
					'status'   => self::SUCCEEDED,
					'response' => $send_email,
				]
			);
			return true;
		} catch ( Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'ElasticEmail API Error', 'quillsmtp' ),
				[
					'code'  => 'quillsmtp_elasticemail_send_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
					],
				]
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
