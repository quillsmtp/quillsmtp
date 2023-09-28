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
		wp_add_inline_script(
			'qsmtp-admin',
			'qsmtp.config.setAdminUrl("' . admin_url() . '");' .
			'qsmtp.config.setPluginDirUrl("' . QUILLSMTP_PLUGIN_URL . '");' .
			'qsmtp.config.setStoreMailers(' . json_encode( Store::instance()->get_all_mailers() ) . ');'
		);
	}
}
