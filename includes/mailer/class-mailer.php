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
	private $name;

	/**
	 * Mailer slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Mailer settings.
	 *
	 * @since 1.0.0
	 *
	 * @var Settings|null
	 */
	private $settings;
}
