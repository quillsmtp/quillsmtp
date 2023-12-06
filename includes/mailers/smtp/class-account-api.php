<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTP;

defined( 'ABSPATH' ) || exit;

use WP_Error;

/**
 * Account_API class.
 *
 * @since 1.0.0
 */
class Account_API {

	/**
	 * SMTP host
	 *
	 * @var string
	 */
	protected $smtp_host;

	/**
	 * SMTP port
	 *
	 * @var string
	 */
	protected $smtp_port;

	/**
	 * Encryption
	 *
	 * @var string
	 */
	protected $encryption;

	/**
	 * Auto tls
	 *
	 * @var bool
	 */
	protected $auto_tls;

	/**
	 * Authentication
	 *
	 * @var bool
	 */
	protected $authentication;

	/**
	 * Username
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 *
	 * @var string
	 */

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $smtp_host SMTP host.
	 * @param string $smtp_port SMTP port.
	 * @param string $encryption Encryption.
	 * @param bool   $auto_tls Auto TLS.
	 * @param bool   $authentication Authentication.
	 * @param string $username Username.
	 * @param string $password Password.
	 */
	public function __construct( $smtp_host, $smtp_port, $encryption, $auto_tls, $authentication, $username, $password ) {
		$this->smtp_host      = $smtp_host;
		$this->smtp_port      = $smtp_port;
		$this->encryption     = $encryption;
		$this->auto_tls       = $auto_tls;
		$this->authentication = $authentication;
		$this->username       = $username;
		$this->password       = $password;
	}

	/**
	 * Get SMTP host.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_smtp_host() {
		return $this->smtp_host;
	}

	/**
	 * Get SMTP port.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_smtp_port() {
		return $this->smtp_port;
	}

	/**
	 * Get encryption.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_encryption() {
		return $this->encryption;
	}

	/**
	 * Get auto tls.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function get_auto_tls() {
		return $this->auto_tls;
	}

	/**
	 * Get authentication.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function get_authentication() {
		return $this->authentication;
	}

	/**
	 * Get username.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_username() {
		return $this->username;
	}

	/**
	 * Get password.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_password() {
		return $this->password;
	}

}
