<?php
/**
 * PHPMailer Mailer.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\PHPMailer;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Provider\Provider;
use QuillSMTP\Mailer\Settings;

/**
 * PHPMailer Mailer Class.
 *
 * @since 1.0.0
 */
class PHPMailer extends Provider {

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug = 'phpmailer';

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'PHPMailer';

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
		'settings' => Settings::class,
		'process'  => Process::class,
	);

}
