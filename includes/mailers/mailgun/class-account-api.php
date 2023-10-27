<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Mailgun;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\Postmark\PostmarkClient;

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
	 * Message Stream ID
	 *
	 * @var string
	 */
	protected $message_stream_id;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key.
	 * @param string $message_stream_id Message Stream ID.
	 */
	public function __construct( $api_key, $message_stream_id ) {
		$this->api_key           = $api_key;
		$this->message_stream_id = $message_stream_id;
	}

	/**
	 * Get Client.
	 *
	 * @since 1.0.0
	 *
	 * @return PostmarkClient
	 */
	public function get_client() {
		return new PostmarkClient( $this->api_key );
	}

	/**
	 * Get Message Stream ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_message_stream_id() {
		return $this->message_stream_id;
	}
}
