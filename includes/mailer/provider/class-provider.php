<?php
/**
 * Mailer Provider class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailer
 */

namespace QuillSMTP\Mailer\Provider;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Mailer;

/**
 * Mailer Provider Class.
 *
 * @since 1.0.0
 */
class Provider extends Mailer {

	/**
	 * Account.
	 *
	 * @since 1.0.0
	 *
	 * @var Accounts
	 */
	public $accounts;

	/**
	 * Provider instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Provider
	 */
	private static $instance;

	/**
	 * Provider Instance.
	 *
	 * Instantiates or reuses an instance of Provider.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @see Provider()
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
	 * Since this is a singleton class, it is better to have its constructor as a private.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}
}
