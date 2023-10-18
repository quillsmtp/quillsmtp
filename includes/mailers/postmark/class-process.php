<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\PostMark;

use Exception;
use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use QuillSMTP\Vendor\Postmark\Models\PostmarkAttachment;

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

		$this->body['From'] = $this->phpmailer->addrFormat( [ $email, $name ] );
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
						$this->body['To'][] = ! empty( $name ) ? sanitize_text_field( $name ) . ' <' . $email_address . '>' : $email_address;
						break;
					case 'cc':
						$this->body['Cc'][] = ! empty( $name ) ? sanitize_text_field( $name ) . ' <' . $email_address . '>' : $email_address;
						break;
					case 'bcc':
						$this->body['Bcc'][] = ! empty( $name ) ? sanitize_text_field( $name ) . ' <' . $email_address . '>' : $email_address;
						break;
				}
			}
		}

		$this->body['To'] = implode( ',', $this->body['To'] );

		if ( ! empty( $this->body['Cc'] ) ) {
			$this->body['Cc'] = implode( ',', $this->body['Cc'] );
		}

		if ( ! empty( $this->body['Bcc'] ) ) {
			$this->body['Bcc'] = implode( ',', $this->body['Bcc'] );
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
		$this->body['Subject'] = sanitize_text_field( $subject );
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
				$this->body['TextBody'] = $content['text'];
			}

			if ( ! empty( $content['html'] ) ) {
				$this->body['HtmlBody'] = $content['html'];
			}
		} else {
			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$this->body['TextBody'] = $content;
			} else {
				$this->body['HtmlBody'] = $content;
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

		foreach ( $emails as $user ) {
			$email_address = isset( $user[0] ) ? $user[0] : false;
			$name          = isset( $user[1] ) ? $user[1] : false;

			if ( ! filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
				continue;
			}

			$this->body['ReplyTo'][] = ! empty( $name ) ? sanitize_text_field( $name ) . ' <' . $email_address . '>' : $email_address;
		}

		$this->body['ReplyTo'] = implode( ',', $this->body['ReplyTo'] );
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

			$attachment = PostmarkAttachment::fromRawData(
				$this->filesystem->get_contents( $filepath ),
				$filename,
				mime_content_type( $filepath )
			);

			$this->body['Attachments'][] = $attachment;
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
		return apply_filters( 'quillsmtp_postmark_mailer_get_body', $this->body );
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
		$account_api       = $this->provider->accounts->connect( $account_id );
		$client            = $account_api->get_client();
		$message_stream_id = $account_api->get_message_stream_id();
		$body              = $this->get_body();
		if ( ! empty( $message_stream_id ) ) {
			$body['MessageStream'] = $message_stream_id;
		}

		try {
			$results = $client->sendEmailBatch( [ $body ] );
			if ( 'OK' === $results[0]->__get( 'Message' ) ) {
				return true;
			} else {
				return new \WP_Error( 'quillsmtp_postmark_mailer_send_error', $results[0]->__get( 'Message' ) );
			}
		} catch ( Exception $e ) {
			return new \WP_Error( 'quillsmtp_postmark_mailer_send_error', $e->getMessage() );
		}
	}
}
