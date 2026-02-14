<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail;

use QuillSMTP\Mailer\Provider\Process as Abstract_Process;
use QuillSMTP\Vendor\Google\Service\Gmail;
use QuillSMTP\Vendor\Google\Service\Gmail\Message;
use WP_Error;

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
		try {
			$account_id = $this->connection['account_id'];
        	/** @var Account_API|WP_Error */ // phpcs:ignore
			$account_api = $this->provider->accounts->connect( $account_id );
			if ( is_wp_error( $account_api ) ) {
				throw new \Exception( $account_api->get_error_message() );
			}

			$user = $account_api->get_profile();
			if ( is_wp_error( $user ) ) {
				throw new \Exception( $user->get_error_message() );
			}
			$email = $user->emailAddress;

			$this->phpmailer->From   = $email;
			$this->phpmailer->Sender = $email;
		} catch ( \Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'Gmail Send Error', 'quill-smtp' ),
				array(
					'code'  => 'quillsmtp_gmail_send_error',
					'error' => [
						'code'  => $e->getCode(),
						'error' => $e->getMessage(),
					],
				)
			);
			return;
		}
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
	public function set_attachments( $attachments ) {}

	/**
	 * Set email subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subject
	 */
	public function set_subject( $subject ) {}

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
				throw new \Exception( $account_api->get_error_message() );
			}
			$this->phpmailer->preSend();

			$client = $account_api->get_client();
			if ( is_wp_error( $client ) ) {
				throw new \Exception( $client->get_error_message() );
			}
			$message = new Message();

			$base64 = str_replace(
				[ '+', '/', '=' ],
				[ '-', '_', '' ],
				base64_encode( $this->phpmailer->getSentMIMEMessage() ) //phpcs:ignore
			);

			$message->setRaw( $base64 );

			$service  = new Gmail( $client );
			$response = $service->users_messages->send( 'me', $message );

			$this->log_result(
				array(
					'status'   => self::SUCCEEDED,
					'response' => $response,
				)
			);
			return true;
		} catch ( \Exception $e ) {
			quillsmtp_get_logger()->error(
				esc_html__( 'Gmail Send Error', 'quill-smtp' ),
				array(
					'code'  => 'quillsmtp_gmail_send_error',
					'error' => [
						'code'  => $e->getCode(),
						'error' => $e->getMessage(),
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
