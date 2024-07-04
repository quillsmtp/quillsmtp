<?php
/**
 * Gmail Mailer.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Provider\Provider;
use QuillSMTP\Mailer\Settings;

/**
 * Gmail Mailer Class.
 *
 * @since 1.0.0
 */
class Gmail extends Provider {

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug = 'gmail';

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'Gmail';

	/**
	 * App
	 *
	 * @var App
	 */
	public $app;

	/**
	 * Class names
	 *
	 * @var array
	 */
	protected static $classes = array(
		'rest'     => REST\REST::class,
		'accounts' => Accounts::class,
		'settings' => Settings::class,
		'process'  => Process::class,
	);

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init() {
		parent::init();

		$this->app = new App( $this );
	}

}
