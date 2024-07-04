<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendGrid;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use WP_Error;
use QuillSMTP\Vendor\SendGrid\Mail\Mail;

/**
 * Process class.
 *
 * @since 1.0.0
 */
class Process extends Abstract_Process {

	/**
	 * Mail.
	 *
	 * @since 1.0.0
	 *
	 * @var Mail
	 */
	protected $email;

	/**
	 * Set mail from phpmailer.
	 *
	 * @since 1.0.0
	 */
	public function set_phpmailer() {
		$this->email = new Mail();
		parent::set_phpmailer();
	}

	/**
	 * Set email header.
	 *
	 * @since 1.0.0
	 */
	public function set_header( $name, $value ) {

		$name = sanitize_text_field( $name );

		$this->email->addHeader( $name, $value );
		$this->headers[ $name ] = $value;
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

		$this->email->setFrom( $email, $name );
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

				switch ( $type ) {
					case 'to':
						$this->email->addTo( $email_address, $name );
						break;
					case 'cc':
						$this->email->addCc( $email_address, $name );
						break;
					case 'bcc':
						$this->email->addBcc( $email_address, $name );
						break;
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
		$this->email->setSubject( $subject );
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
				$this->email->addContent( 'text/plain', $content['text'] );
			}

			if ( ! empty( $content['html'] ) ) {
				$this->email->addContent( 'text/html', $content['html'] );
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->email->addContent( 'text/plain', $content );
			} else {
				$this->email->addContent( 'text/html', $content );
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

		foreach ( $emails as $email ) {
			$email_address = isset( $email[0] ) ? $email[0] : false;
			$name          = isset( $email[1] ) ? $email[1] : false;

			if ( ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
				continue;
			}

			$this->email->setReplyTo( $email_address, $name );
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

		foreach ( $attachments as $attachment ) {
			$filepath = isset( $attachment[0] ) ? $attachment[0] : false;
			$filename = isset( $attachment[2] ) ? $attachment[2] : false;

			if ( empty( $filename ) || empty( $filepath ) ) {
				continue;
			}

			$this->email->addAttachment(
				base64_encode( $this->filesystem->get_contents( $filepath ) ),
				mime_content_type( $filepath ),
				$filename
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
		 * Filters Sendgrid email headers.
		 *
		 * @since 1.0.0
		 *
		 * @param array $headers Email headers.
		 */
		$headers = apply_filters( 'quillsmtp_sendgrid_mailer_get_headers', $this->headers );

		return $headers;
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
			$client         = $account_api->get_client();
			$sending_domain = $account_api->get_sending_domain();

			if ( ! empty( $sending_domain ) ) {
				$this->email->setSpamCheck( true, 1, $sending_domain );
			}
			$results       = $client->send( $this->email );
			$response_code = $results->statusCode();

			if ( 202 === $response_code ) {
				$this->log_result(
					[
						'status'   => self::SUCCEEDED,
						'response' => [
							'message' => $results->body(),
						],
					]
				);
				return true;
			} else {
				throw new Exception( $results->body() );
			}
		} catch ( Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'SendGrid API Error', 'quillsmtp' ),
				array(
					'code'  => 'quillsmtp_sendgrid_send_error',
					'error' => [
						'message' => $e->getMessage(),
						'code'    => $e->getCode(),
						'data'    => $e->getTraceAsString(),
					],
				)
			);
			$this->log_result(
				[
					'status'   => self::FAILED,
					'response' => [
						'message' => $e->getMessage(),
					],
				]
			);
			return false;
		}
	}
}
