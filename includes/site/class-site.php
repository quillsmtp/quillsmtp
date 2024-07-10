<?php
/**
 * Class: Site
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Site;

/**
 * Site class
 *
 * @since 1.0.0
 */
class Site {

	/**
	 * Class instance
	 *
	 * @var self instance
	 */
	private static $instance = null;

	/**
	 * Get class instance
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		License::instance();
		Updater::instance();
	}

	/**
	 * API Request
	 *
	 * @param array   $body Body.
	 * @param integer $success_code Success code.
	 * @return array
	 */
	public function api_request( $body, $success_code = 200 ) {
		$body = array_merge(
			array(
				'url'         => home_url(),
				'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
			),
			$body
		);

		$response = wp_remote_post(
			'https://quillsmtp.com',
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'code'    => $response->get_error_code(),
				'message' => $response->get_error_message(),
			);
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		return array(
			'success' => $response_code === $success_code,
			'code'    => $response_code,
			'data'    => $response_body,
		);
	}

}
