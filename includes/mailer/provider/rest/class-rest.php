<?php
/**
 * REST class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailer\Provider\REST;

use QuillSMTP\Mailer\Provider\Provider;
use QuillSMTP\Mailer\REST\REST as Abstract_REST;
use QuillSMTP\Settings;

/**
 * REST class.
 *
 * @since 1.0.0
 *
 * @property Provider $mailer
 */
class REST extends Abstract_REST {

	/**
	 * Class names
	 *
	 * @var array
	 */
	protected static $classes = array(
		// + classes from parent.
		// 'account_controller'        => Account_Controller::class,
	);

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Provider $mailer Provider mailer.
	 */
	public function __construct( $mailer ) {
		parent::__construct( $mailer );

		if ( ! empty( static::$classes['account_controller'] ) ) {
			new static::$classes['account_controller']( $this->mailer );
		}
		add_filter( 'quillsmtp_rest_settings', array( $this, 'add_rest_data' ) );
	}

	/**
	 * Add mailer rest data to quillsmtp post type
	 *
	 * @since 1.0.0
	 *
	 * @param Settings $settings Settings.
	 *
	 * @return Settings
	 */
	public function add_rest_data( $settings ) { // phpcs:ignore
		$settings['mailers'][ $this->mailer->slug ] = $this->get_rest_data() ?? array();

		return $settings;
	}

	/**
	 * Get rest data
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	protected function get_rest_data() {
		$data = [];

		if ( $this->mailer->accounts ) {
			$data['accounts'] = $this->mailer->accounts->get_accounts();
		}

		return $data;
	}
}
