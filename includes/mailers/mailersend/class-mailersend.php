<?php
/**
 * MailerSend Mailer.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\MailerSend;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Provider\Provider;
use QuillSMTP\Mailer\Settings;

/**
 * MailerSend Mailer Class.
 *
 * @since 1.0.0
 */
class MailerSend extends Provider {

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug = 'mailersend';

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'MailerSend';

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
