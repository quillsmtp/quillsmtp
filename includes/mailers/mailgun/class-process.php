<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Mailgun;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use QuillSMTP\Vendor\Postmark\Models\PostmarkAttachment;
use WP_Error;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

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

		$name = sanitize_text_field( $name );

		$this->body[ 'h:' . $name ] = $value;
		$this->headers[ $name ]     = $value;
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

		$this->body['from'] = $this->phpmailer->addrFormat( [ $email, $name ] );
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

			$this->body[ $type ] = $this->addrs_format( $emails );
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
		$this->body['subject'] = sanitize_text_field( $subject );
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
				$this->body['text'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['html'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['text'] = $content;
			} else {
				$this->body['html'] = $content;
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

		$this->body['h:Reply-To'] = $this->addrs_format( $emails );
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
		$headers = apply_filters( 'quillsmtp_mailgun_mailer_get_headers', $this->headers );

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
		$body = apply_filters( 'quillsmtp_mailgun_mailer_get_body', $this->body );

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
		$body   = $this->get_body();
		$result = $account_api->send( $body, $this->content_type );

		if ( is_wp_error( $result ) ) {
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $result->get_error_message(),
				)
			);
			return $result;
		}

		if ( ! empty( $result['id'] ) ) {
			$this->log_result(
				array(
					'status'   => self::SUCCEEDED,
					'response' => $result,
				)
			);
		} else {
			$this->log_result(
				array(
					'status'   => self::FAILED,
					'response' => $result,
				)
			);

			return false;
		}

		return true;
	}
}
