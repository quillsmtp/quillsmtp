<?php
/**
 * SMTPcom Mailer.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTPcom;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Provider\Provider;
use QuillSMTP\Mailer\Settings;

/**
 * SMTPcom Mailer Class.
 *
 * @since 1.0.0
 */
class SMTPcom extends Provider {

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug = 'smtpcom';

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'SMTPcom';

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

}
