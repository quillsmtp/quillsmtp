<?php
/**
 * REST class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail\REST;

use QuillSMTP\Mailer\Provider\REST\REST as Abstract_REST;

/**
 * REST class.
 *
 * @since 1.0.0
 */
class REST extends Abstract_REST {

	/**
	 * Class names
	 *
	 * @var array
	 */
	protected static $classes = array(
		'settings_controller' => Settings_Controller::class,
		'account_controller'  => Account_Controller::class,
	);

	/**
	 * Get rest data
	 *
	 * @since 1.0.0
	 *
	 * @param Settings $settings Settings.
	 * @return mixed
	 */
	protected function get_rest_data( $settings ) {
		$rest_data = parent::get_rest_data( $settings );

		$app              = $this->mailer->settings->get( 'app' ) ?? [];
		$rest_data['app'] = $app;
		return $rest_data;
	}
}
