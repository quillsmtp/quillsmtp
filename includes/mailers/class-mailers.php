<?php
/**
 * Mailers Class.
 *
 *  @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers;

use QuillSMTP\Mailers\SendLayer\SendLayer;

/**
 * Mailers Class.
 *
 * @since 1.0.0
 */
class Mailers {

	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var QuillSMTP
	 */
	private static $instance;

	/**
	 * QuillSMTP Instance.
	 *
	 * Instantiates or reuses an instance of QuillSMTP.
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
		$this->load_mailers();
	}

	/**
	 * Load Mailers.
	 *
	 * @since 1.0.0
	 */
	private function load_mailers() {
		$mailers = array(
			'sendlayer' => SendLayer::class,
		);

		foreach ( $mailers as $slug => $class ) {
			$class::instance();
		}
	}
}
