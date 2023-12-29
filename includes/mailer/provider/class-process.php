<?php
/**
 * Process class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailer
 */

namespace QuillSMTP\Mailer\Provider;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Abstracts\Log_Levels;

/**
 * Process class.
 *
 * @since 1.0.0
 */
abstract class Process {

	const SUCCEEDED = 'succeeded';
	const FAILED    = 'failed';

	/**
	 * Provider
	 *
	 * @since 1.0.0
	 *
	 * @var Provider
	 */
	protected $provider;

	/**
	 * PHPMailer.
	 *
	 * @since 1.0.0
	 *
	 * @var \PHPMailer\PHPMailer\PHPMailer
	 */
	protected $phpmailer;

	/**
	 * Connection id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $connection_id;

	/**
	 * connection.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $connection;

	/**
	 * Wp filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @var \WP_Filesystem_Base
	 */
	protected $filesystem;

	/**
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $body = array();

	/**
	 * Return path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $return_path;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Provider                       $provider Provider.
	 * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer.
	 * @param string                         $connection_id Connection id.
	 * @param array                          $connection Connection.
	 *
	 * @param array                          $connection Connection.
	 */
	public function __construct( $provider, $phpmailer, $connection_id, $connection ) {
		$this->provider      = $provider;
		$this->phpmailer     = $phpmailer;
		$this->connection    = $connection;
		$this->connection_id = $connection_id;

		// Set the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php'; // We will probably need to load this file.
		global $wp_filesystem;
		WP_Filesystem(); // Initial WP file system.
		$this->filesystem = $wp_filesystem;

		$this->set_phpmailer();
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	abstract public function send();

	/**
	 * Set mail from phpmailer.
	 *
	 * @since 1.0.0
	 */
	public function set_phpmailer() {
		$this->phpmailer->XMailer = "QuillSMTP {$this->provider->name}";
		$this->set_headers( $this->phpmailer->getCustomHeaders() );
		$this->set_from( $this->get_from_email(), $this->get_from_name() );
		$this->set_recipients(
			array(
				'to'  => $this->phpmailer->getToAddresses(),
				'cc'  => $this->phpmailer->getCcAddresses(),
				'bcc' => $this->phpmailer->getBccAddresses(),
			)
		);
		$this->set_subject( $this->phpmailer->Subject );
		if ( $this->phpmailer->ContentType === 'text/plain' ) {
			$this->set_content( $this->phpmailer->Body );
		} else {
			$this->set_content(
				array(
					'text' => $this->phpmailer->AltBody,
					'html' => $this->phpmailer->Body,
				)
			);
		}
		$this->set_return_path( $this->phpmailer->From );
		$this->set_reply_to( $this->phpmailer->getReplyToAddresses() );
		$this->set_attachments( $this->phpmailer->getAttachments() );
	}

	/**
	 * Set the email custom headers.
	 *
	 * @since 1.0.0
	 *
	 * @param array $headers Custom headers.
	 */
	public function set_headers( $headers ) {
		foreach ( $headers as $header ) {
			$name  = isset( $header[0] ) ? $header[0] : false;
			$value = isset( $header[1] ) ? $header[1] : false;

			if ( empty( $name ) || empty( $value ) ) {
				continue;
			}

			$this->set_header( $name, $value );
		}
	}

	/**
	 * Set custom email header.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function set_header( $name, $value ) {
		if ( 'Return-Path' === $name ) {
			$this->set_return_path( $value );
			return;
		}

		$name = sanitize_text_field( $name );

		$this->headers[ $name ] = $value;
	}

	/**
	 * Set the email from address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param string $name
	 */
	abstract function set_from( $email, $name );

	/**
	 * Set the email recipients.
	 *
	 * @since 1.0.0
	 *
	 * @param array $recipients
	 */
	abstract function set_recipients( $recipients );

	/**
	 * Set the email subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subject
	 */
	abstract function set_subject( $subject );

	/**
	 * Set the email content.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $content
	 */
	abstract function set_content( $content );

	/**
	 * Set the email return path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 */
	protected function set_return_path( $email ) {
		$this->return_path = $email;
	}

	/**
	 * Set the email reply to.
	 *
	 * @since 1.0.0
	 *
	 * @param array $emails
	 */
	abstract function set_reply_to( $emails );

	/**
	 * Set the email attachments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attachments
	 */
	abstract function set_attachments( $attachments );

	/**
	 * Get the email body.
	 *
	 * @since 1.0.0
	 *
	 * @return string|array
	 */
	public function get_body() {

		return apply_filters( 'quillsmtp_mailer_get_body', $this->body, $this->provider );
	}

	/**
	 * Get the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_headers() {

		return apply_filters( 'quillsmtp_mailer_get_headers', $this->headers, $this->provider );
	}

	/**
	 * Get From email.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_from_email() {
		$connection_from_email = $this->connection['from_email'] ?? '';
		$force_from_email      = $this->connection['force_from_email'] ?? false;
		$from_email            = $this->phpmailer->From ?? $connection_from_email;

		if ( $force_from_email && ! empty( $connection_from_email ) ) {
			$from_email = $connection_from_email;
		}

		return apply_filters( 'quillsmtp_mailer_get_from_email', $from_email, $this->provider );
	}

	/**
	 * Get From name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_from_name() {
		$connection_from_name = $this->connection['from_name'] ?? '';
		$force_from_name      = $this->connection['force_from_name'] ?? false;
		$from_name            = $this->phpmailer->FromName ?? $connection_from_name;

		if ( $force_from_name && ! empty( $connection_from_name ) ) {
			$from_name = $connection_from_name;
		}

		return apply_filters( 'quillsmtp_mailer_get_from_name', $from_name, $this->provider );
	}

	/**
	 * Get email details.
	 *
	 * @since 1.0.0
	 */
	public function get_email_details() {
		$email_details = [
			'from'        => $this->phpmailer->addrFormat( [ $this->get_from_email(), $this->get_from_name() ] ),
			'to'          => $this->addrs_format( $this->phpmailer->getToAddresses() ),
			'cc'          => $this->addrs_format( $this->phpmailer->getCcAddresses() ),
			'bcc'         => $this->addrs_format( $this->phpmailer->getBccAddresses() ),
			'reply_to'    => $this->addrs_format( $this->phpmailer->getReplyToAddresses() ),
			'subject'     => $this->phpmailer->Subject,
			'headers'     => $this->get_headers(),
			'plain'       => $this->phpmailer->AltBody,
			'html'        => $this->phpmailer->Body,
			'attachments' => array_map(
				function( $attachment ) {
					return $attachment[1];
				},
				$this->phpmailer->getAttachments()
			),
		];

		return apply_filters( 'quillsmtp_mailer_get_email_details', $email_details, $this->provider );
	}

	/**
	 * Address format.
	 *
	 * @since 1.0.0
	 *
	 * @param array $addresses
	 *
	 * @return string
	 */
	public function addrs_format( $addresses ) {
		$addrs = [];

		foreach ( $addresses as $user ) {
			$email = isset( $user[0] ) ? $user[0] : false;

			if ( empty( $email ) ) {
				continue;
			}

			$addrs[] = $this->phpmailer->addrFormat( $user );
		}

		return implode( ',', $addrs );
	}

	/**
	 * Log connection process result
	 *
	 * @since 1.0.0
	 *
	 * @param array $result includes 'status' and 'details'.
	 * @return void
	 */
	public function log_result( $result ) {
		switch ( $result['status'] ) {
			case self::SUCCEEDED:
				$level   = Log_Levels::INFO;
				$message = esc_html__( 'Email sent successfully', 'quillsmtp' );
				$code    = 'email_sent';
				break;
			case self::FAILED:
				$level   = Log_Levels::ERROR;
				$message = esc_html__( 'Failed to send email', 'quillsmtp' );
				$code    = 'cannot_send_email';
				break;
		}

		// add basic log context info.
		$context = array(
			'source'          => static::class . '->send',
			'code'            => $code,
			'connection_id'   => $this->connection_id,
			'connection_name' => $this->connection['name'],
			'provider'        => $this->provider->name,
			'email_details'   => $this->get_email_details(),
			'response'        => $result['response'],
		);

		// add additional info for failed and skipped connections.
		if ( in_array( $result['status'], array( self::FAILED ), true ) ) {
			$context = array_merge(
				$context,
				array(
					'connection' => $this->connection,
				)
			);
		}

		if ( apply_filters( 'quillsmtp_mailer_log_result', true, $level, $message, $context ) === false ) {
			return;
		}

		quillsmtp_get_logger()->log( $level, $message, $context );
	}
}
