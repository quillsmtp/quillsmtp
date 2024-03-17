<?php
/**
 * Admin: Class Admin Loader
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage admin
 */

namespace QuillSMTP\Admin;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Core;

/**
 * Admin Loader Class.
 *
 * @since 1.0.0
 */
class Admin_Loader {

	/**
	 * Class Instance.
	 *
	 * @var Admin_Loader
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Admin Instance.
	 *
	 * Instantiates or reuses an instance of Admin_Loader.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @see Admin_Loader()
	 *
	 * @return self - Single instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Returns true if we are on a JS powered admin page.
	 */
	public static function is_admin_page() : bool {
		$current_screen = get_current_screen();
		if ( false === strpos( $current_screen->id, 'quillsmtp' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Constructor.
	 * Since this is a singleton class, it is better to have its constructor as a private.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_inline_scripts' ), 14 );

		// Remove notices.
		add_action( 'admin_notices', array( $this, 'remove_notices' ), 1 );
		add_action( 'admin_notices', array( __CLASS__, 'inject_before_notices' ), -9999 );
		add_action( 'admin_notices', array( __CLASS__, 'inject_after_notices' ), PHP_INT_MAX );

	}


	/**
	 * Runs before admin notices action and hides them.
	 *
	 * @since 1.0.0
	 */
	public static function inject_before_notices() {
		if ( ! self::is_admin_page() ) {
			return;
		}

		// Wrap the notices in a hidden div to prevent flickering before
		// they are moved elsewhere in the page by WordPress Core.
		echo '<div class="quillsmtp-layout__notice-list-hide" style="display: none;" id="wp__notice-list">';

		if ( self::is_admin_page() ) {
			// Capture all notices and hide them. WordPress Core looks for
			// `.wp-header-end` and appends notices after it if found.
			// https://github.com/WordPress/WordPress/blob/f6a37e7d39e2534d05b9e542045174498edfe536/wp-admin/js/common.js#L737 .
			echo '<div class="wp-header-end" id="quillsmtp-layout__notice-catcher"></div>';
		}
	}

	/**
	 * Runs after admin notices and closes div.
	 *
	 * @since 1.0.0
	 */
	public static function inject_after_notices() {
		if ( ! self::is_admin_page() ) {
				return;
		}
		// Close the hidden div used to prevent notices from flickering before
		// they are inserted elsewhere in the page.
		echo '</div>';
	}

	/**
	 * Remove Notices.
	 *
	 * @since 1.0.0
	 */
	public function remove_notices() {

		if ( ! self::is_admin_page() ) {
			return;
		}

		// Hello Dolly.
		if ( function_exists( 'hello_dolly' ) ) {
			remove_action( 'admin_notices', 'hello_dolly' );
		}
	}


	/**
	 * Add inline scripts.
	 *
	 * @since 1.0.0
	 */
	public static function add_inline_scripts() {
		Core::set_admin_config();
	}

	/**
	 * Enqueue Scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		global $submenu;
		$user = wp_get_current_user();

		$asset_file   = QUILLSMTP_PLUGIN_DIR . 'build/client/index.asset.php';
		$asset        = file_exists( $asset_file ) ? require $asset_file : null;
		$dependencies = isset( $asset['dependencies'] ) ? $asset['dependencies'] : array();
		$version      = isset( $asset['version'] ) ? $asset['version'] : QUILLSMTP_PLUGIN_VERSION;
		$config_file  = QUILLSMTP_PLUGIN_DIR . 'build/config/index.asset.php';
		$config       = file_exists( $config_file ) ? require $config_file : null;
		$config_deps  = isset( $config['dependencies'] ) ? $config['dependencies'] : array();
		$config_ver   = isset( $config['version'] ) ? $config['version'] : QUILLSMTP_PLUGIN_VERSION;

		// Register scripts.
		wp_register_script(
			'qsmtp-config',
			QUILLSMTP_PLUGIN_URL . 'build/config/index.js',
			$config_deps,
			$config_ver,
			true
		);

		wp_register_script(
			'qsmtp-admin',
			QUILLSMTP_PLUGIN_URL . 'build/client/index.js',
			array_merge( $dependencies, array( 'qsmtp-config' ) ),
			$version,
			true
		);

		wp_localize_script(
			'qsmtp-admin',
			'qsmtpAdmin',
			array(
				'adminUrl'       => admin_url(),
				'assetsBuildUrl' => QUILLSMTP_PLUGIN_URL,
				'submenuPages'   => $submenu['quillsmtp'] ?? [],
				'license_nonce'  => wp_create_nonce( 'quillsmtp_license' ),
				'adminUrl'       => admin_url(),
			)
		);

		// Register styles.
		wp_register_style(
			'qsmtp-admin',
			QUILLSMTP_PLUGIN_URL . 'build/client/style.css',
			array(),
			$version
		);

		// RTL styles.
		wp_style_add_data( 'qsmtp-admin', 'rtl', 'replace' );
	}

	/**
	 * Page Wrapper.
	 *
	 * @since 1.0.0
	 */
	public static function page_wrapper() {
		// Important to check for authentication.
		wp_auth_check_load();

		do_action( 'qsmtp_admin_enqueue_scripts' );

		// Enqueue scripts.
		wp_enqueue_script( 'qsmtp-config' );
		wp_enqueue_script( 'qsmtp-admin' );
		wp_enqueue_style( 'qsmtp-admin' );
		wp_enqueue_script( 'jquery' );

		?>
		<div class="quillsmtp-wrap">
			<div id="qsmtp-admin-root">
				<div id="qsmtp-admin-root__loader-container" style="
					display: flex;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 600px;
				">
					<svg xmlns="http://www.w3.org/2000/svg" version="1.2" viewBox="0 0 1452 1590" width="80px" height="80px">
						<defs>
							<linearGradient id="g1" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1060.488,119.755,-112.706,998.068,339.802,917.005)">
								<stop offset="0" stop-color="#00afef"/>
								<stop offset="1" stop-color="#2a5584"/>
							</linearGradient>
							<linearGradient id="g2" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(564.565,172.59,-92.844,303.706,611.993,598.052)">
								<stop offset="0" stop-color="#8c3a8a"/>
								<stop offset="1" stop-color="#ec268f"/>
							</linearGradient>
							<linearGradient id="g3" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(828.95,731.717,-349.551,396.001,571.805,86.425)">
								<stop offset="0" stop-color="#2a5584"/>
								<stop offset="1" stop-color="#00afef"/>
							</linearGradient>
							<linearGradient id="g4" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(-414.223,-844.185,532.169,-261.123,427.705,1290.275)">
								<stop offset="0" stop-color="#8c3a8a"/>
								<stop offset="1" stop-color="#ec268f"/>
							</linearGradient>
							<linearGradient id="g5" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(346.275,585.77,-485.217,286.834,763.435,-57.335)">
								<stop offset="0" stop-color="#2a5584"/>
								<stop offset="1" stop-color="#00afef"/>
							</linearGradient>
							<linearGradient id="g6" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(-320.503,-563.18,689.613,-392.455,310.553,915.047)">
								<stop offset="0" stop-color="#8c3a8a"/>
								<stop offset="1" stop-color="#ec268f"/>
							</linearGradient>
							<linearGradient id="g7" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(.857,241.372,-666.488,2.368,357.41,1690.743)">
								<stop offset="0" stop-color="#ec268f"/>
								<stop offset="1" stop-color="#8c3a8a"/>
							</linearGradient>
							<clipPath clipPathUnits="userSpaceOnUse" id="cp1">
								<path d="m99.77 1906.26q-13.07-2.61-25.7-10.02-12.64-7.41-23.67-19.31-11.04-11.91-17.87-30.07-6.82-18.15-6.82-39.35 0-46.77 24.54-76.25 24.54-29.48 72.18-29.48 36.3 0 62.88 27.16 26.58 27.16 26.58 72.76 0 35.72-17.57 61.14-17.58 25.41-38.78 29.77 23.24 7.55 63.32 7.55v12.78q0 19.17-21.49 19.17-21.79 0-50.4-7.7-28.61-7.69-47.19-18.15zm71.16-99.63q-0.01-69.71-53.45-69.71-23.52 0-37.17 17.72-13.66 17.72-13.66 47.05 0 35.73 14.53 53.74 14.52 18 41.24 18 22.07 0.01 35.29-19.02 13.22-19.03 13.22-47.78zm212.61 27.3q0 73.78-68.55 73.78-29.92 0-49.52-19.03-19.61-19.02-19.61-52.13v-89.17h7.26q32.24 0 32.24 27.88v57.51q0 20.63 8.72 30.5 8.71 9.88 22.94 9.88 12.49 0 19.9-9.3 7.4-9.29 7.4-28.46v-88.01h6.97q32.24 0 32.24 27.88v58.68zm112.7 48.21q0 12.49-5.38 18.01-5.37 5.52-17.86 5.52-22.65 0-34.71-12.49-12.05-12.49-12.05-36.31v-106.01h11.04q27.88 0 27.88 29.04v73.2q0 11.61 5.37 16.12 5.38 4.5 18.44 4.5h7.27zm-34.71-184.73q6.82 6.68 6.83 15.98 0 9.29-6.83 16.12-6.83 6.82-16.12 6.82-9.3 0-15.98-6.82-6.68-6.83-6.68-16.12 0-9.3 6.68-15.98 6.68-6.68 15.98-6.68 9.29 0 16.12 6.68zm133.75 184.73q0 23.53-26.14 23.53-24.11 0-36.89-12.49-12.78-12.49-12.78-36.31v-161.78h11.04q13.94 0 20.91 7.12 6.98 7.11 6.97 19.02v131.87q0 11.61 6.1 16.12 6.1 4.5 20.33 4.5h10.46zm94.69 0q0 23.53-26.14 23.53-24.11 0-36.89-12.49-12.78-12.49-12.78-36.31v-161.78h11.04q13.94 0 20.91 7.11 6.97 7.12 6.97 19.03v131.86q0 11.62 6.1 16.12 6.1 4.5 20.33 4.51h10.46z"/>
							</clipPath>
							<linearGradient id="g8" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(-2.825,206.138,-714.156,-9.787,1121.565,1701.68)">
								<stop offset="0" stop-color="#00afef"/>
								<stop offset="1" stop-color="#285989"/>
							</linearGradient>
							<clipPath clipPathUnits="userSpaceOnUse" id="cp2">
								<path d="m892.58 1842.65q0 29.92-19.61 47.49-19.6 17.57-55.33 17.57-29.33 0-45.01-10.6-15.69-10.6-15.69-28.61v-17.71h0.87q24.11 21.49 55.48 21.49 21.49 0 30.5-6.68 9-6.68 9-19.46 0-11.04-8.57-16.7-8.57-5.66-38.77-12.06-52.87-11.32-52.87-51.12 0-26.14 20.48-45.31 20.48-19.17 54.17-19.17 31.66 0 45.75 10.75 14.09 10.74 14.09 25.56v18.3h-1.17q-18.29-19.46-51.7-19.46-42.11 0-42.11 25.56 0 9.29 9.73 15.25 9.73 5.95 36.74 11.76 54.03 11.33 54.03 53.15zm243.98 61.58q-11.91-0.01-18.74-6.68-6.82-6.68-6.82-19.75 0-19.75-2.62-61.73-2.61-41.97-4.94-56.2-8.13 19.75-9.29 23.53l-27.88 85.39q-7.56 22.95-16.27 35.44h-10.75q-21.2-0.01-31.95-31.66l-28.46-83.94q-4.94-14.53-11.04-29.05-3.49 19.75-6.1 66.66-2.61 46.91-2.61 77.99h-13.94q-11.33-0.01-18.16-6.98-6.82-6.97-6.82-19.46 0-32.53 5.23-87.28 5.22-54.75 10.74-85.25h8.43q20.62 0 34.7 8.86 14.09 8.86 19.9 24.84l27.59 77.84q8.71 23.81 11.33 37.76 1.16-6.1 5.52-20.33l22.94-70.58q6.97-22.08 15.54-37.47 8.57-15.4 15.54-20.91h4.65q16.85 0 27.16 10.45 10.31 10.46 12.63 30.79 2.91 24.98 6.25 77.12 3.33 52.13 3.34 80.6zm189.95-186.47q0 7.55-7.26 14.95-7.26 7.41-16.56 7.41h-36.59v164.11h-15.98q-10.74-0.01-17.42-6.98-6.68-6.97-6.69-19.75v-137.38h-57.21v-13.07q0-8.14 6.39-14.96 6.39-6.83 17.72-6.83h133.6v12.49zm161.2 47.93q0 31.08-19.02 49.52-19.03 18.44-48.07 18.45-20.92-0.01-33.41-6.68v77.26h-15.68q-9.58-0.01-16.85-6.98-7.26-6.97-7.26-21.49v-162.65q8.72-3.2 25.13-6.25 16.41-3.05 26.57-3.05h22.37q29.62 0 47.92 16.27 18.3 16.26 18.3 45.6zm-40.95 3.48q0-16.85-7.27-24.25-7.26-7.41-22.94-7.41h-12.78q-2.61 0-16.56 2.62v56.34q11.91 3.49 28.18 3.49 15.1 0 23.23-8.13 8.14-8.14 8.14-22.66z"/>
							</clipPath>
						</defs>
						<style>
							.s0 { fill: url(#g1) } 
							.s1 { fill: url(#g2) } 
							.s2 { fill: #246193 } 
							.s3 { fill: #1e93cf } 
							.s4 { fill: url(#g3) } 
							.s5 { fill: url(#g4) } 
							.s6 { fill: url(#g5) } 
							.s7 { fill: url(#g6) } 
							.s8 { fill: url(#g7) } 
							.s9 { opacity: .3;fill: #373435 } 
							.s10 { fill: none } 
							.s11 { fill: url(#g8) } 
						</style>
						<g id="Layer_x0020_1">
							<g id="_3181323266288">
								<g id="Layer">
									<path id="Layer" fill-rule="evenodd" class="s0" d="m625.8 483.1c-3.7 4.1 3.5 25.6 4.4 30.2l40.2 193.2c4 29 8.5 48.2 14.5 77.5 7.7 38.4 32.6 151.6 36.5 180.6 9.2 1.2 44.1-4.7 53.6-6.1 17-2.6 34.9-4.4 52.4-7.8l208.3-29.5c19.5-3.1 88.8-14.4 104.9-12.7-10.3 13.3-90.8 106.6-95.1 115.8 11.1 6.4 214.7 184.8 226.2 194.4 22.9 19.2 0.5 40.4-18 26-27.4-21.2-237.7-206.4-265.1-228.1-13.7-11-26.1-21.8-39.8-33.3-5.1-4.3-14.4-13.1-20-16.6-9.7-6.2-20.5-1.5-23.3 7.3-3.8 12.1 4.1 16.5 11.3 22.5l324.9 277.9c13.9 13.6-1.1 40.6-26.8 18.3l-245.4-211.3c-7.3-6.1-15.3-11.5-24.2-4.3-18.5 15.1 8.7 31.5 19.4 40.2 27.4 22.2 239.3 206.9 266.2 228.7 10.3 8.3 37.8 26.4 17.7 40.6-13.9 9.8-34.9-14.2-44.7-22.3l-344.7-294.6c-7.1-5.9-33.7-30.7-42.4-31.3-8.8-0.5-17.8 7.6-15.6 18.4 1.5 7.7 11.4 13.9 17.9 19.3 53.3 43.9 105.8 88.2 159.2 133.1 42.7 36 284.5 242.5 324.8 278.1 20.2 17.7 0.4 40.3-20.6 23.6l-444.3-377.9c-5.6-4.7-13.8-13.7-22-13.9-8.6-0.2-18.1 7.4-15 19.6 1.9 7.4 252.7 221 263.2 229.4 27.8 22.5 5.9 37.8-8 32.9-5.3-1.8-366-312.8-385.5-328.7-79.7-64.9-159.1-135.1-238.7-199.9-13.6-11.2-26.3-22-39.8-33.4-15-12.7-28.3-20.4-30.1-41.1-1.9-21.4 14.3-35.3 24.8-47.9l166.4-199.2c3.7-4.3 62-76.7 68-79.4l4.3 11.7z"/>
									<path id="Layer" fill-rule="evenodd" class="s1" d="m1156.5 887.4l-401.9-48.4c-0.3-6.7-24.6-84.4-28.6-96.6-10.2-31.7-20.9-64.5-29.5-96.3-4.5-16.8-9.9-31-14.6-47.5-8.4-29.6-40.4-126.2-44.3-146 7.2 3.8 82.8 69.1 97.9 81.2 10.8 8.6 21.9 18.1 32.5 27.2 21.5 18.3 42.9 36.6 64.3 53.7l129.2 108.4c22 18.2 43.2 36 65.1 54.4l97.9 81.7c4.5 3.6 29.4 23.8 32 28.2z"/>
									<path id="Layer" fill-rule="evenodd" class="s2" d="m1140.6 908.5c-16.1-1.7-85.4 9.6-105 12.7l-208.2 29.5c-17.5 3.4-35.4 5.2-52.4 7.8-9.5 1.4-44.4 7.3-53.6 6.1-3.9-29-28.8-142.2-36.5-180.6-6-29.3-10.5-48.5-14.5-77.5l-40.2-193.2c-0.9-4.6-8.1-26.1-4.4-30.2 7.6 30.3 19.2 62.1 28.5 93.2 9.1 30.2 18.5 63.5 28.7 94.2 7.2 21.5 52.3 174.7 57.8 186.6 13.1 3.2 35.1 4.9 49.3 6.4l344.7 41.5c9.4 2.7 3.8 0.2 5.8 3.5z"/>
									<path id="Layer" fill-rule="evenodd" class="s3" d="m1336.8 1350.4c7.1 3.9 26.7 20.9 33.7 27.1 13 11.7 2.6 35.1-17.3 25.9-3.4-1.5-29.8-24-33.2-27.2-14.7-14.4 1.5-34.2 16.8-25.8z"/>
									<path id="Layer" fill-rule="evenodd" class="s3" d="m1348 1520.8c8.4 6.3 18.2 11.7 16.2 23.4-1.8 11.1-13.5 16.6-24.2 9.8-34.5-21.6-8.6-45.6 8-33.2z"/>
									<path id="Layer" fill-rule="evenodd" class="s3" d="m1111.5 1404c7.3 3.4 17.4 13.1 17.1 21-0.5 12.1-12 19-23.6 13.1-28.8-15-12-42.7 6.5-34.1z"/>
									<path id="Layer" fill-rule="evenodd" class="s3" d="m1284.1 1305.6c26.3 18.7 5 41.1-13 29.8-26.2-16.4-5.3-42.8 13-29.8z"/>
									<path id="Layer" fill-rule="evenodd" class="s4" d="m1384.7 713.9c0-371.8-301.4-673.1-673.1-673.1-45.6 0-90.2 4.6-133.3 13.3-67 91.8-104.4 182-124.9 247.7 81.2-51.5 177.3-81.6 279.9-81.6 289.6 0 524.3 234.7 524.3 524.3 0 92.8-24.4 179.7-66.6 255.3 95.1 89 165.7 270.4 165.7 270.4-13.3-85.4-35.1-155.1-58.8-220.2 60.7-113.9 86.8-219.9 86.8-336.1z"/>
									<path id="Layer" fill-rule="evenodd" class="s5" d="m38.5 713.9c0 371.7 301.3 673.1 673.1 673.1 72 0 141.3-11.5 206.3-32.4-162.6-2.6-260.7-35.3-334.3-66.8-121.8-54.8-370.7-197.6-374.6-543.3-2-184.2 96.5-349 244.4-442.7 20.5-65.7 57.9-155.9 124.9-247.7-307.9 61.8-539.8 333.7-539.8 659.8z"/>
									<path id="Layer" fill-rule="evenodd" class="s6" d="m1297.6 382.7c-115.6-204.1-334.7-341.9-586-341.9-45.6 0-90.2 4.6-133.3 13.3-67 91.8-104.4 182-124.9 247.7 81.2-51.5 177.3-81.6 279.9-81.6 247.6 0 455 171.6 510 402.3 33.1-74.3 52.2-155.2 54.3-239.8z"/>
									<path id="Layer" fill-rule="evenodd" class="s7" d="m38.5 713.9c0 61.8 8.4 121.6 23.9 178.4 61.2 46.1 130.4 83.6 205.6 110.4-34.9-70-57.8-155.1-59-258.2-2-184.2 96.5-349 244.4-442.7 20.5-65.7 57.9-155.9 124.9-247.7-307.9 61.8-539.8 333.7-539.8 659.8z"/>
								</g>
								<path id="Layer" fill-rule="evenodd" class="s8" d="m99.8 1906.3q-13.1-2.7-25.7-10.1-12.7-7.4-23.7-19.3-11-11.9-17.9-30-6.8-18.2-6.8-39.4 0-46.8 24.6-76.2 24.5-29.5 72.1-29.5 36.3 0 62.9 27.1 26.6 27.2 26.6 72.8 0 35.7-17.6 61.1-17.6 25.4-38.8 29.8 23.3 7.6 63.4 7.6v12.7q0 19.2-21.5 19.2-21.8 0-50.4-7.7-28.6-7.7-47.2-18.1zm71.1-99.7q0-69.7-53.4-69.7-23.5 0-37.2 17.7-13.6 17.8-13.6 47.1 0 35.7 14.5 53.7 14.5 18 41.2 18 22.1 0 35.3-19 13.2-19 13.2-47.8zm212.6 27.3q0 73.8-68.5 73.8-29.9 0-49.5-19-19.6-19-19.6-52.2v-89.1h7.2q32.3 0 32.3 27.9v57.5q0 20.6 8.7 30.5 8.7 9.8 22.9 9.8 12.5 0 19.9-9.2 7.4-9.3 7.4-28.5v-88h7q32.2 0 32.2 27.9v58.6zm112.7 48.3q0 12.4-5.3 18-5.4 5.5-17.9 5.5-22.7 0-34.7-12.5-12.1-12.5-12.1-36.3v-106h11.1q27.9 0 27.9 29v73.2q0 11.6 5.3 16.1 5.4 4.5 18.5 4.5h7.2zm-34.7-184.7q6.9 6.7 6.9 16 0 9.3-6.9 16.1-6.8 6.8-16.1 6.8-9.3 0-16-6.8-6.6-6.8-6.6-16.1 0-9.3 6.6-16 6.7-6.7 16-6.7 9.3 0 16.1 6.7zm133.8 184.7q0 23.6-26.2 23.6-24.1 0-36.8-12.5-12.8-12.5-12.8-36.3v-161.8h11q14 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5zm94.7 0q0 23.5-26.2 23.5-24.1 0-36.9-12.5-12.7-12.4-12.7-36.3v-161.7h11q13.9 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5z"/>
								<g id="Clip-Path" clip-path="url(#cp1)">
									<g id="Layer">
										<g id="Layer">
											<path id="_1" fill-rule="evenodd" class="s9" d="m756.7 1531.7c454.2 0 822.4 63.9 822.4 142.9 0 78.9-368.2 142.9-822.4 142.9-454.2 0-822.4-64-822.4-142.9 0-79 368.2-142.9 822.4-142.9z"/>
										</g>
									</g>
								</g>
								<path id="Layer" fill-rule="evenodd" class="s10" d="m99.8 1906.3q-13.1-2.7-25.7-10.1-12.7-7.4-23.7-19.3-11-11.9-17.9-30-6.8-18.2-6.8-39.4 0-46.8 24.6-76.2 24.5-29.5 72.1-29.5 36.3 0 62.9 27.1 26.6 27.2 26.6 72.8 0 35.7-17.6 61.1-17.6 25.4-38.8 29.8 23.3 7.6 63.4 7.6v12.7q0 19.2-21.5 19.2-21.8 0-50.4-7.7-28.6-7.7-47.2-18.1zm71.1-99.7q0-69.7-53.4-69.7-23.5 0-37.2 17.7-13.6 17.8-13.6 47.1 0 35.7 14.5 53.7 14.5 18 41.2 18 22.1 0 35.3-19 13.2-19 13.2-47.8zm212.6 27.3q0 73.8-68.5 73.8-29.9 0-49.5-19-19.6-19-19.6-52.2v-89.1h7.2q32.3 0 32.3 27.9v57.5q0 20.6 8.7 30.5 8.7 9.8 22.9 9.8 12.5 0 19.9-9.2 7.4-9.3 7.4-28.5v-88h7q32.2 0 32.2 27.9v58.6zm112.7 48.3q0 12.4-5.3 18-5.4 5.5-17.9 5.5-22.7 0-34.7-12.5-12.1-12.5-12.1-36.3v-106h11.1q27.9 0 27.9 29v73.2q0 11.6 5.3 16.1 5.4 4.5 18.5 4.5h7.2zm-34.7-184.7q6.9 6.7 6.9 16 0 9.3-6.9 16.1-6.8 6.8-16.1 6.8-9.3 0-16-6.8-6.6-6.8-6.6-16.1 0-9.3 6.6-16 6.7-6.7 16-6.7 9.3 0 16.1 6.7zm133.8 184.7q0 23.6-26.2 23.6-24.1 0-36.8-12.5-12.8-12.5-12.8-36.3v-161.8h11q14 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5zm94.7 0q0 23.5-26.2 23.5-24.1 0-36.9-12.5-12.7-12.4-12.7-36.3v-161.7h11q13.9 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5z"/>
								<path id="Layer" fill-rule="evenodd" class="s11" d="m892.6 1842.7q0 29.9-19.6 47.4-19.6 17.6-55.4 17.6-29.3 0-45-10.6-15.7-10.6-15.7-28.6v-17.7h0.9q24.1 21.5 55.5 21.5 21.5 0 30.5-6.7 9-6.7 9-19.5 0-11-8.6-16.7-8.5-5.6-38.8-12-52.8-11.3-52.8-51.1 0-26.2 20.5-45.3 20.4-19.2 54.1-19.2 31.7 0 45.8 10.7 14.1 10.8 14.1 25.6v18.3h-1.2q-18.3-19.5-51.7-19.5-42.1 0-42.1 25.6 0 9.3 9.7 15.2 9.7 6 36.8 11.8 54 11.3 54 53.2zm244 61.5q-11.9 0-18.8-6.7-6.8-6.6-6.8-19.7 0-19.8-2.6-61.7-2.6-42-4.9-56.2-8.2 19.7-9.3 23.5l-27.9 85.4q-7.6 22.9-16.3 35.4h-10.7q-21.2 0-32-31.6l-28.5-84q-4.9-14.5-11-29-3.5 19.7-6.1 66.6-2.6 46.9-2.6 78h-13.9q-11.4 0-18.2-6.9-6.8-7-6.8-19.5 0-32.5 5.2-87.3 5.2-54.7 10.8-85.2h8.4q20.6 0 34.7 8.8 14.1 8.9 19.9 24.9l27.6 77.8q8.7 23.8 11.3 37.8 1.2-6.1 5.5-20.4l23-70.6q6.9-22 15.5-37.4 8.6-15.4 15.5-20.9h4.7q16.8 0 27.1 10.4 10.4 10.5 12.7 30.8 2.9 25 6.2 77.1 3.4 52.2 3.4 80.6zm189.9-186.4q0 7.5-7.2 14.9-7.3 7.4-16.6 7.4h-36.6v164.1h-16q-10.7 0-17.4-6.9-6.7-7-6.7-19.8v-137.4h-57.2v-13.1q0-8.1 6.4-14.9 6.4-6.8 17.7-6.8h133.6v12.5zm161.2 47.9q0 31.1-19 49.5-19 18.4-48.1 18.4-20.9 0-33.4-6.6v77.2h-15.7q-9.5 0-16.8-6.9-7.3-7-7.3-21.5v-162.7q8.7-3.2 25.2-6.2 16.4-3.1 26.5-3.1h22.4q29.6 0 47.9 16.3 18.3 16.2 18.3 45.6zm-40.9 3.5q0-16.9-7.3-24.3-7.3-7.4-22.9-7.4h-12.8q-2.6 0-16.6 2.6v56.4q11.9 3.4 28.2 3.4 15.1 0 23.2-8.1 8.2-8.1 8.2-22.6z"/>
								<g id="Clip-Path" clip-path="url(#cp2)">
									<g id="Layer">
										<g id="Layer">
											<path id="_1_0" fill-rule="evenodd" class="s9" d="m756.7 1531.7c454.2 0 822.4 63.9 822.4 142.9 0 78.9-368.2 142.9-822.4 142.9-454.2 0-822.4-64-822.4-142.9 0-79 368.2-142.9 822.4-142.9z"/>
										</g>
									</g>
								</g>
								<path id="Layer" fill-rule="evenodd" class="s10" d="m892.6 1842.7q0 29.9-19.6 47.4-19.6 17.6-55.4 17.6-29.3 0-45-10.6-15.7-10.6-15.7-28.6v-17.7h0.9q24.1 21.5 55.5 21.5 21.5 0 30.5-6.7 9-6.7 9-19.5 0-11-8.6-16.7-8.5-5.6-38.8-12-52.8-11.3-52.8-51.1 0-26.2 20.5-45.3 20.4-19.2 54.1-19.2 31.7 0 45.8 10.7 14.1 10.8 14.1 25.6v18.3h-1.2q-18.3-19.5-51.7-19.5-42.1 0-42.1 25.6 0 9.3 9.7 15.2 9.7 6 36.8 11.8 54 11.3 54 53.2zm244 61.5q-11.9 0-18.8-6.7-6.8-6.6-6.8-19.7 0-19.8-2.6-61.7-2.6-42-4.9-56.2-8.2 19.7-9.3 23.5l-27.9 85.4q-7.6 22.9-16.3 35.4h-10.7q-21.2 0-32-31.6l-28.5-84q-4.9-14.5-11-29-3.5 19.7-6.1 66.6-2.6 46.9-2.6 78h-13.9q-11.4 0-18.2-6.9-6.8-7-6.8-19.5 0-32.5 5.2-87.3 5.2-54.7 10.8-85.2h8.4q20.6 0 34.7 8.8 14.1 8.9 19.9 24.9l27.6 77.8q8.7 23.8 11.3 37.8 1.2-6.1 5.5-20.4l23-70.6q6.9-22 15.5-37.4 8.6-15.4 15.5-20.9h4.7q16.8 0 27.1 10.4 10.4 10.5 12.7 30.8 2.9 25 6.2 77.1 3.4 52.2 3.4 80.6zm189.9-186.4q0 7.5-7.2 14.9-7.3 7.4-16.6 7.4h-36.6v164.1h-16q-10.7 0-17.4-6.9-6.7-7-6.7-19.8v-137.4h-57.2v-13.1q0-8.1 6.4-14.9 6.4-6.8 17.7-6.8h133.6v12.5zm161.2 47.9q0 31.1-19 49.5-19 18.4-48.1 18.4-20.9 0-33.4-6.6v77.2h-15.7q-9.5 0-16.8-6.9-7.3-7-7.3-21.5v-162.7q8.7-3.2 25.2-6.2 16.4-3.1 26.5-3.1h22.4q29.6 0 47.9 16.3 18.3 16.2 18.3 45.6zm-40.9 3.5q0-16.9-7.3-24.3-7.3-7.4-22.9-7.4h-12.8q-2.6 0-16.6 2.6v56.4q11.9 3.4 28.2 3.4 15.1 0 23.2-8.1 8.2-8.1 8.2-22.6z"/>
							</g>
						</g>
					</svg>
				</div>
			</div>
		</div>
		<?php
	}
}
