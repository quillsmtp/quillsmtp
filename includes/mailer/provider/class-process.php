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

use Exception;

/**
 * Process class.
 *
 * @since 1.0.0
 */
abstract class Process {

	/**
	 * Provider
	 *
	 * @since 1.0.0
	 *
	 * @var Provider
	 */
	protected $provider;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Provider $provider Provider.
	 */
	public function __construct( $provider ) {
		$this->provider = $provider;
	}

	/**
	 * Send email.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Email arguments.
	 *
	 * @return bool
	 */
	abstract public function send( $args );
}
