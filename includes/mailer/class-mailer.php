<?php
/**
 * Mailer Class.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailer
 */

namespace QuillSMTP\Mailer;

defined( 'ABSPATH' ) || exit;

/**
 * Mailer Class.
 *
 * @since 1.0.0
 */
class Mailer {

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Mailer settings.
	 *
	 * @since 1.0.0
	 *
	 * @var Settings|null
	 */
	public $settings;

	/**
	 * Class names
	 *
	 * @var array
	 */
	protected static $classes = array(
		// 'settings'  => Settings::class,
		// 'rest'      => REST\REST::class,
	);

	/**
	 * Subclasses instances.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private static $instances = array();

	/**
	 * Addon Instances.
	 *
	 * Instantiates or reuses an instances of Addon.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @return static - Single instance
	 */
	public static function instance() {
		if ( ! isset( self::$instances[ static::class ] ) ) {
			self::$instances[ static::class ] = new static();
		}
		return self::$instances[ static::class ];
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	protected function init() {
		if ( ! empty( static::$classes['settings'] ) ) {
			$this->settings = new static::$classes['settings']( $this );
		}

		if ( ! empty( static::$classes['rest'] ) ) {
			new static::$classes['rest']( $this );
		}
	}

}
