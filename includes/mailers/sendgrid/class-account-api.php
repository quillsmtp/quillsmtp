<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendGrid;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\SendGrid;

/**
 * Account_API class.
 *
 * @since 1.0.0
 */
class Account_API {

	/**
	 * API
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * Sending Domain
	 *
	 * @var string
	 */
	protected $sending_domain;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 * @param string $sending_domain Sending domain.
	 */
	public function __construct( $api_key, $sending_domain ) {
		$this->api_key        = $api_key;
		$this->sending_domain = $sending_domain;
	}

	/**
	 * Get Client.
	 *
	 * @since 1.0.0
	 *
	 * @return SendGrid
	 */
	public function get_client() {
		return new SendGrid( $this->api_key );
	}

	/**
	 * Get the sending domain.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_sending_domain() {
		return $this->sending_domain;
	}
}
