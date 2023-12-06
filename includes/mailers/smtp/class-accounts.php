<?php
/**
 * Accounts class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\SMTP;

use QuillSMTP\Mailer\Provider\Accounts as Abstract_Accounts;

/**
 * Accounts class.
 *
 * @since 1.0.0
 */
class Accounts extends Abstract_Accounts {

	/**
	 * Initialize new account api
	 *
	 * @param string $account_id Account id.
	 * @param array  $account_data Account data.
	 * @return Account_API
	 */
	protected function init_account_api( $account_id, $account_data ) {
		return new Account_API( $account_data['credentials']['smtp_host'], $account_data['credentials']['smtp_port'], $account_data['credentials']['encryption'], $account_data['credentials']['auto_tls'], $account_data['credentials']['authentication'], $account_data['credentials']['username'], $account_data['credentials']['password'] );
	}

}
