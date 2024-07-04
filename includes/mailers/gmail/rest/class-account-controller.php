<?php
/**
 * Account_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail\REST;

use QuillSMTP\Mailer\Provider\REST\Account_Controller as Abstract_Account_Controller;
use QuillSMTP\Mailer\Provider\REST\Traits\Account_Controller_Gettable;

/**
 * Account_Controller class.
 *
 * @since 1.3.0
 */
class Account_Controller extends Abstract_Account_Controller {
	use Account_Controller_Gettable;

	/**
	 * Register controller routes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		parent::register_routes();

		$this->register_gettable_route();
	}

}
