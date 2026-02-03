<?php
/**
 * Email_Test class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Email_Test;


use QuillSMTP\Settings;

/**
 * Email_Test class.
 *
 * @since 1.0.0
 */
class Email_Test {

	/**
	 * Content type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $content_type = 'html';

	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Email_Test
	 */
	private static $instance;

	/**
	 * Email_Test Instance.
	 *
	 * Instantiates or reuses an instance of Email_Test.
	 *
	 * @since  1.0.0
	 * @static
	 *
	 * @return self - Single instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'wp_ajax_quillsmtp_send_test_email', array( $this, 'test_email' ) );
	}

	/**
	 * Test email.
	 *
	 * @since 1.0.0
	 */
	public function test_email() {

		// Check nonce.
		check_ajax_referer( 'quillsmtp-admin', 'nonce' );
		$connection_id      = sanitize_text_field( $_POST['connection'] );
		$email              = empty( $_POST['email'] ) ? get_option( 'admin_email' ) : sanitize_email( $_POST['email'] );
		$content_type       = sanitize_text_field( $_POST['content_type'] ) ?? 'html';
		$this->content_type = $content_type;

		// Check connection.
		if ( ! $connection_id ) {
			wp_send_json_error( __( 'Please select a connection.', 'quillsmtp' ) );
		}

		// check if email is valid.
		if ( ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please enter a valid email address.', 'quillsmtp' ) );
		}

		$connections = Settings::get( 'connections' );
		$connection  = $connections[ $connection_id ] ?? null;

		if ( ! $connection ) {
			wp_send_json_error( __( 'Connection not found.', 'quillsmtp' ) );
		}

		// Use explicit connection filter to bypass auto-routing
		add_filter(
			'quillsmtp_explicit_connection',
			function() use ( $connection_id ) {
				return $connection_id;
			}
		);

		$message = $this->build_email();
		$result  = wp_mail(
			$email,
			__( 'QuillSMTP Test Email', 'quillsmtp' ),
			$message,
			$this->get_headers()
		);

		if ( $result ) {
			wp_send_json_success( __( 'Email sent successfully.', 'quillsmtp' ) );
		} else {
			wp_send_json_error( __( 'Failed to send email.', 'quillsmtp' ) );
		}
	}

	/**
	 * Get the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return string The email headers.
	 */
	public function get_headers() {
		$admin_name  = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );

		$headers  = "From: {$admin_name} <{$admin_email}>\r\n";
		$headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";

		return apply_filters( 'quillsmtp_email_test_headers', $headers, $this );
	}

	/**
	 * Get the email content type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The email content type.
	 */
	public function get_content_type() {

		if ( 'html' === $this->content_type ) {
			$content_type = apply_filters( 'quillsmtp_email_test_content_type', 'text/html', $this );
		} else {
			$content_type = 'text/plain';
		}

		return apply_filters( 'quillsmtp_email_content_type', $content_type, $this );
	}

	/**
	 * Build the email.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function build_email() {
		/*
		 * Generate an HTML email.
		 */

		ob_start();

		$this->get_template_part( $this->content_type, false );

		$message = ob_get_clean();

		return apply_filters( 'quillsmtp_email_test_message', $message, $this );
	}

	/**
	 * Retrieve a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Optional. Default null.
	 * @param bool   $load Maybe load.
	 *
	 * @return string
	 */
	public function get_template_part( $name = null, $load = true ) {
		$template = QUILLSMTP_PLUGIN_DIR . 'includes/email-test/templates/';
		if ( isset( $name ) ) {
			$template .= $name . '.php';
		}
		$template = apply_filters( 'quillsmtp_get_template_part', $template, $name, $load );

		// Return the part that is found.
		return load_template( $template, $load );
	}
}
