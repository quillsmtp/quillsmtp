<?php
/**
 * SendLayer Mailer.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SendLayer;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Mailer\Provider\Provider;

/**
 * SendLayer Mailer Class.
 *
 * @since 1.0.0
 */
class SendLayer extends Provider {

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug = 'sendlayer';

	/**
	 * Mailer name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'SendLayer';

}
