<?php
/**
 * Class Core
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

namespace QuillSMTP;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Store;
use QuillSMTP\Site\License;

/**
 * Core Class
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * Set admin config
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public static function set_admin_config() {
		// Admin email address.
		$admin_email = get_option( 'admin_email' );
		$ajax_url    = admin_url( 'admin-ajax.php' );
		$nonce       = wp_create_nonce( 'quillsmtp-admin' );

		wp_add_inline_script(
			'qsmtp-config',
			'qsmtp.config.setAdminUrl("' . admin_url() . '");' .
			'qsmtp.config.setPluginDirUrl("' . QUILLSMTP_PLUGIN_URL . '");' .
			'qsmtp.config.setStoreMailers(' . json_encode( Store::instance()->get_all_mailers() ) . ');' .
			'qsmtp.config.setAdminEmail("' . $admin_email . '");' .
			'qsmtp.config.setAjaxUrl("' . $ajax_url . '");' .
			'qsmtp.config.setNonce("' . $nonce . '");' .
			'qsmtp.config.setIsMultisite("' . ( is_multisite() ? '1' : '0' ) . '");' .
			'qsmtp.config.setIsMainSite("' . ( is_main_site() ? '1' : '0' ) . '");' .
			'qsmtp.config.setLicense(' . json_encode( License::instance()->get_license_info() ) . ');'
		);
	}
}
