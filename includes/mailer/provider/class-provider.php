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
	 * Class names
	 *
	 * @var array
	 */
	protected static $classes = array(
		// + classes from parent.
		// 'accounts'             => Accounts::class,
		// 'process'              => Process::class,
	);

	protected function init() {
		parent::init();

		if ( ! empty( static::$classes['accounts'] ) ) {
			$this->accounts = new static::$classes['accounts']( $this );
		}
	}

	/**
	 * Process.
	 *
	 * @since 1.0.0
	 *
	 * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer.
	 * @param array                          $connection Connection.
	 *
	 * @return Process
	 */
	public function process( $phpmailer, $connection ) {
		$class = static::$classes['process'];

		return new $class( $this, $phpmailer, $connection );
	}
}
