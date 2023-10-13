<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendInBlue;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\Brevo\Client\Configuration;

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
	 * Sending domain.
	 *
	 * @var string
	 */
	protected $sending_domain;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_key API key.
	 * @param array $sending_domain Sending domain.
	 */
	public function __construct( $api_key, $sending_domain ) {
		$this->api_key        = $api_key;
		$this->sending_domain = $sending_domain;
	}

	/**
	 * Get Brevo client
	 *
	 * @since 1.0.0
	 *
	 * @return \QuillSMTP\Vendor\Brevo\Client\Api\AccountApi
	 */
	protected function get_client() {
		return Configuration::getDefaultConfiguration()->setApiKey( 'api-key', $this->api_key );
	}
}
