<?php
/**
 * Admin: Class Admin
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 * @subpackage admin
 */

namespace QuillSMTP\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Class.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Class Instance.
	 *
	 * @var Admin
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Admin Instance.
	 *
	 * Instantiates or reuses an instance of Admin.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @see Admin()
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
	 * Constructor.
	 * Since this is a singleton class, it is better to have its constructor as a private.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->admin_hooks();
	}

	/**
	 * Admin Hooks.
	 *
	 * @since 1.0.0
	 */
	public function admin_hooks() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu_pages' ) );
	}

	/**
	 * Create Admin Menu Pages.
	 *
	 * @since 1.0.0
	 */
	public function create_admin_menu_pages() {
		add_menu_page(
			__( 'Quill SMTP', 'quillsmtp' ),
			__( 'Quill SMTP', 'quillsmtp' ),
			'manage_options',
			'quillsmtp',
			array( Admin_Loader::class, 'page_wrapper' ),
			'data:image/svg+xml;base64,' . base64_encode(
				'<svg xmlns="http://www.w3.org/2000/svg" version="1.2" viewBox="0 0 1350 1539" width="1350" height="1539">
					<style>
						.s0 { fill: #a9abae } 
						.s1 { fill: #a9abae } 
						.s2 { fill: #246193 } 
						.s3 { fill: #1e93cf } 
						.s4 { fill: #a9abae } 
						.s5 { fill: #a9abae } 
						.s6 { fill: #a9abae } 
						.s7 { fill: #a9abae } 
						.s8 { fill: #a9abae } 
						.s9 { opacity: .3;fill: none } 
						.s10 { fill: none } 
						.s11 { fill: #a9abae } 
						path { fill: #a9abae !important; fill-rule="evenodd" !important;}
					</style>
					<g id="Layer_x0020_1">
						<g id="_3181323266288">
							<g id="Layer">
								<path id="Layer" fill-rule="evenodd" class="s0" d="m589.8 457.1c-3.7 4.1 3.5 25.6 4.4 30.2l40.2 193.2c4 29 8.5 48.2 14.5 77.5 7.7 38.4 32.6 151.6 36.5 180.6 9.2 1.2 44.1-4.7 53.6-6.1 17-2.6 34.9-4.4 52.4-7.8l208.3-29.5c19.5-3.1 88.8-14.4 104.9-12.7-10.3 13.3-90.8 106.6-95.1 115.8 11.1 6.4 214.7 184.8 226.2 194.4 22.9 19.2 0.5 40.4-18 26-27.4-21.2-237.7-206.4-265.1-228.1-13.7-11-26.1-21.8-39.8-33.3-5.1-4.3-14.4-13.1-20-16.6-9.7-6.2-20.5-1.5-23.3 7.3-3.8 12.1 4.1 16.5 11.3 22.5l324.9 277.9c13.9 13.6-1.1 40.6-26.8 18.3l-245.4-211.3c-7.3-6.1-15.3-11.5-24.2-4.3-18.5 15.1 8.7 31.5 19.4 40.2 27.4 22.2 239.3 206.9 266.2 228.7 10.3 8.3 37.8 26.4 17.7 40.6-13.9 9.8-34.9-14.2-44.7-22.3l-344.7-294.6c-7.1-5.9-33.7-30.7-42.4-31.3-8.8-0.5-17.8 7.6-15.6 18.4 1.5 7.7 11.4 13.9 17.9 19.3 53.3 43.9 105.8 88.2 159.2 133.1 42.7 36 284.5 242.5 324.8 278.1 20.2 17.7 0.4 40.3-20.6 23.6l-444.3-377.9c-5.6-4.7-13.8-13.7-22-13.9-8.6-0.2-18.1 7.4-15 19.6 1.9 7.4 252.7 221 263.2 229.4 27.8 22.5 5.9 37.8-8 32.9-5.3-1.8-366-312.8-385.5-328.7-79.7-64.9-159.1-135.1-238.7-199.9-13.6-11.2-26.3-22-39.8-33.4-15-12.7-28.3-20.4-30.1-41.1-1.9-21.4 14.3-35.3 24.8-47.9l166.4-199.2c3.7-4.3 62-76.7 68-79.4l4.3 11.7z"/>
								<path id="Layer" fill-rule="evenodd" class="s1" d="m1120.5 861.4l-401.9-48.4c-0.3-6.7-24.6-84.4-28.6-96.6-10.2-31.7-20.9-64.5-29.5-96.3-4.5-16.8-9.9-31-14.6-47.5-8.4-29.6-40.4-126.2-44.3-146 7.2 3.8 82.8 69.1 97.9 81.2 10.8 8.6 21.9 18.1 32.5 27.2 21.5 18.3 42.9 36.6 64.3 53.7l129.2 108.4c22 18.2 43.2 36 65.1 54.4l97.9 81.7c4.5 3.6 29.4 23.8 32 28.2z"/>
								<path id="Layer" fill-rule="evenodd" class="s2" d="m1104.6 882.5c-16.1-1.7-85.4 9.6-105 12.7l-208.2 29.5c-17.5 3.4-35.4 5.2-52.4 7.8-9.5 1.4-44.4 7.3-53.6 6.1-3.9-29-28.8-142.2-36.5-180.6-6-29.3-10.5-48.5-14.5-77.5l-40.2-193.2c-0.9-4.6-8.1-26.1-4.4-30.2 7.6 30.3 19.2 62.1 28.5 93.2 9.1 30.2 18.5 63.5 28.7 94.2 7.2 21.5 52.3 174.7 57.8 186.6 13.1 3.2 35.1 4.9 49.3 6.4l344.7 41.5c9.4 2.7 3.8 0.2 5.8 3.5z"/>
								<path id="Layer" fill-rule="evenodd" class="s3" d="m1300.8 1324.4c7.1 3.9 26.7 20.9 33.7 27.1 13 11.7 2.6 35.1-17.3 25.9-3.4-1.5-29.8-24-33.2-27.2-14.7-14.4 1.5-34.2 16.8-25.8z"/>
								<path id="Layer" fill-rule="evenodd" class="s3" d="m1312 1494.8c8.4 6.3 18.2 11.7 16.2 23.4-1.8 11.1-13.5 16.6-24.2 9.8-34.5-21.6-8.6-45.6 8-33.2z"/>
								<path id="Layer" fill-rule="evenodd" class="s3" d="m1075.5 1378c7.3 3.4 17.4 13.1 17.1 21-0.5 12.1-12 19-23.6 13.1-28.8-15-12-42.7 6.5-34.1z"/>
								<path id="Layer" fill-rule="evenodd" class="s3" d="m1248.1 1279.6c26.3 18.7 5 41.1-13 29.8-26.2-16.4-5.3-42.8 13-29.8z"/>
								<path id="Layer" fill-rule="evenodd" class="s4" d="m1348.7 687.9c0-371.8-301.4-673.1-673.1-673.1-45.6 0-90.2 4.6-133.3 13.3-67 91.8-104.4 182-124.9 247.7 81.2-51.5 177.3-81.6 279.9-81.6 289.6 0 524.3 234.7 524.3 524.3 0 92.8-24.4 179.7-66.6 255.3 95.1 89 165.7 270.4 165.7 270.4-13.3-85.4-35.1-155.1-58.8-220.2 60.7-113.9 86.8-219.9 86.8-336.1z"/>
								<path id="Layer" fill-rule="evenodd" class="s5" d="m2.5 687.9c0 371.7 301.3 673.1 673.1 673.1 72 0 141.3-11.5 206.3-32.4-162.6-2.6-260.7-35.3-334.3-66.8-121.8-54.8-370.7-197.6-374.6-543.3-2-184.2 96.5-349 244.4-442.7 20.5-65.7 57.9-155.9 124.9-247.7-307.9 61.8-539.8 333.7-539.8 659.8z"/>
								<path id="Layer" fill-rule="evenodd" class="s6" d="m1261.6 356.7c-115.6-204.1-334.7-341.9-586-341.9-45.6 0-90.2 4.6-133.3 13.3-67 91.8-104.4 182-124.9 247.7 81.2-51.5 177.3-81.6 279.9-81.6 247.6 0 455 171.6 510 402.3 33.1-74.3 52.2-155.2 54.3-239.8z"/>
								<path id="Layer" fill-rule="evenodd" class="s7" d="m2.5 687.9c0 61.8 8.4 121.6 23.9 178.4 61.2 46.1 130.4 83.6 205.6 110.4-34.9-70-57.8-155.1-59-258.2-2-184.2 96.5-349 244.4-442.7 20.5-65.7 57.9-155.9 124.9-247.7-307.9 61.8-539.8 333.7-539.8 659.8z"/>
							</g>
							<path id="Layer" fill-rule="evenodd" class="s8" d="m63.8 1880.3q-13.1-2.7-25.7-10.1-12.7-7.4-23.7-19.3-11-11.9-17.9-30-6.8-18.2-6.8-39.4 0-46.8 24.6-76.2 24.5-29.5 72.1-29.5 36.3 0 62.9 27.1 26.6 27.2 26.6 72.8 0 35.7-17.6 61.1-17.6 25.4-38.8 29.8 23.3 7.6 63.4 7.6v12.7q0 19.2-21.5 19.2-21.8 0-50.4-7.7-28.6-7.7-47.2-18.1zm71.1-99.7q0-69.7-53.4-69.7-23.5 0-37.2 17.7-13.6 17.8-13.6 47.1 0 35.7 14.5 53.7 14.5 18 41.2 18 22.1 0 35.3-19 13.2-19 13.2-47.8zm212.6 27.3q0 73.8-68.5 73.8-29.9 0-49.5-19-19.6-19-19.6-52.2v-89.1h7.2q32.3 0 32.3 27.9v57.5q0 20.6 8.7 30.5 8.7 9.8 22.9 9.8 12.5 0 19.9-9.2 7.4-9.3 7.4-28.5v-88h7q32.2 0 32.2 27.9v58.6zm112.7 48.3q0 12.4-5.3 18-5.4 5.5-17.9 5.5-22.7 0-34.7-12.5-12.1-12.5-12.1-36.3v-106h11.1q27.9 0 27.9 29v73.2q0 11.6 5.3 16.1 5.4 4.5 18.5 4.5h7.2zm-34.7-184.7q6.9 6.7 6.9 16 0 9.3-6.9 16.1-6.8 6.8-16.1 6.8-9.3 0-16-6.8-6.6-6.8-6.6-16.1 0-9.3 6.6-16 6.7-6.7 16-6.7 9.3 0 16.1 6.7zm133.8 184.7q0 23.6-26.2 23.6-24.1 0-36.8-12.5-12.8-12.5-12.8-36.3v-161.8h11q14 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5zm94.7 0q0 23.5-26.2 23.5-24.1 0-36.9-12.5-12.7-12.4-12.7-36.3v-161.7h11q13.9 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5z"/>
							<g id="Clip-Path" clip-path="url(#cp1)">
								<g id="Layer">
									<g id="Layer">
										<path id="_1" fill-rule="evenodd" class="s9" d="m720.7 1505.7c454.2 0 822.4 63.9 822.4 142.9 0 78.9-368.2 142.9-822.4 142.9-454.2 0-822.4-64-822.4-142.9 0-79 368.2-142.9 822.4-142.9z"/>
									</g>
								</g>
							</g>
							<path id="Layer" fill-rule="evenodd" class="s10" d="m63.8 1880.3q-13.1-2.7-25.7-10.1-12.7-7.4-23.7-19.3-11-11.9-17.9-30-6.8-18.2-6.8-39.4 0-46.8 24.6-76.2 24.5-29.5 72.1-29.5 36.3 0 62.9 27.1 26.6 27.2 26.6 72.8 0 35.7-17.6 61.1-17.6 25.4-38.8 29.8 23.3 7.6 63.4 7.6v12.7q0 19.2-21.5 19.2-21.8 0-50.4-7.7-28.6-7.7-47.2-18.1zm71.1-99.7q0-69.7-53.4-69.7-23.5 0-37.2 17.7-13.6 17.8-13.6 47.1 0 35.7 14.5 53.7 14.5 18 41.2 18 22.1 0 35.3-19 13.2-19 13.2-47.8zm212.6 27.3q0 73.8-68.5 73.8-29.9 0-49.5-19-19.6-19-19.6-52.2v-89.1h7.2q32.3 0 32.3 27.9v57.5q0 20.6 8.7 30.5 8.7 9.8 22.9 9.8 12.5 0 19.9-9.2 7.4-9.3 7.4-28.5v-88h7q32.2 0 32.2 27.9v58.6zm112.7 48.3q0 12.4-5.3 18-5.4 5.5-17.9 5.5-22.7 0-34.7-12.5-12.1-12.5-12.1-36.3v-106h11.1q27.9 0 27.9 29v73.2q0 11.6 5.3 16.1 5.4 4.5 18.5 4.5h7.2zm-34.7-184.7q6.9 6.7 6.9 16 0 9.3-6.9 16.1-6.8 6.8-16.1 6.8-9.3 0-16-6.8-6.6-6.8-6.6-16.1 0-9.3 6.6-16 6.7-6.7 16-6.7 9.3 0 16.1 6.7zm133.8 184.7q0 23.6-26.2 23.6-24.1 0-36.8-12.5-12.8-12.5-12.8-36.3v-161.8h11q14 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5zm94.7 0q0 23.5-26.2 23.5-24.1 0-36.9-12.5-12.7-12.4-12.7-36.3v-161.7h11q13.9 0 20.9 7.1 7 7.1 7 19v131.9q0 11.6 6.1 16.1 6.1 4.5 20.3 4.5h10.5z"/>
							<path id="Layer" fill-rule="evenodd" class="s11" d="m856.6 1816.7q0 29.9-19.6 47.4-19.6 17.6-55.4 17.6-29.3 0-45-10.6-15.7-10.6-15.7-28.6v-17.7h0.9q24.1 21.5 55.5 21.5 21.5 0 30.5-6.7 9-6.7 9-19.5 0-11-8.6-16.7-8.5-5.6-38.8-12-52.8-11.3-52.8-51.1 0-26.2 20.5-45.3 20.4-19.2 54.1-19.2 31.7 0 45.8 10.7 14.1 10.8 14.1 25.6v18.3h-1.2q-18.3-19.5-51.7-19.5-42.1 0-42.1 25.6 0 9.3 9.7 15.2 9.7 6 36.8 11.8 54 11.3 54 53.2zm244 61.5q-11.9 0-18.8-6.7-6.8-6.6-6.8-19.7 0-19.8-2.6-61.7-2.6-42-4.9-56.2-8.2 19.7-9.3 23.5l-27.9 85.4q-7.6 22.9-16.3 35.4h-10.7q-21.2 0-32-31.6l-28.5-84q-4.9-14.5-11-29-3.5 19.7-6.1 66.6-2.6 46.9-2.6 78h-13.9q-11.4 0-18.2-6.9-6.8-7-6.8-19.5 0-32.5 5.2-87.3 5.2-54.7 10.8-85.2h8.4q20.6 0 34.7 8.8 14.1 8.9 19.9 24.9l27.6 77.8q8.7 23.8 11.3 37.8 1.2-6.1 5.5-20.4l23-70.6q6.9-22 15.5-37.4 8.6-15.4 15.5-20.9h4.7q16.8 0 27.1 10.4 10.4 10.5 12.7 30.8 2.9 25 6.2 77.1 3.4 52.2 3.4 80.6zm189.9-186.4q0 7.5-7.2 14.9-7.3 7.4-16.6 7.4h-36.6v164.1h-16q-10.7 0-17.4-6.9-6.7-7-6.7-19.8v-137.4h-57.2v-13.1q0-8.1 6.4-14.9 6.4-6.8 17.7-6.8h133.6v12.5zm161.2 47.9q0 31.1-19 49.5-19 18.4-48.1 18.4-20.9 0-33.4-6.6v77.2h-15.7q-9.5 0-16.8-6.9-7.3-7-7.3-21.5v-162.7q8.7-3.2 25.2-6.2 16.4-3.1 26.5-3.1h22.4q29.6 0 47.9 16.3 18.3 16.2 18.3 45.6zm-40.9 3.5q0-16.9-7.3-24.3-7.3-7.4-22.9-7.4h-12.8q-2.6 0-16.6 2.6v56.4q11.9 3.4 28.2 3.4 15.1 0 23.2-8.1 8.2-8.1 8.2-22.6z"/>
							<g id="Clip-Path" clip-path="url(#cp2)">
								<g id="Layer">
									<g id="Layer">
										<path id="_1_0" fill-rule="evenodd" class="s9" d="m720.7 1505.7c454.2 0 822.4 63.9 822.4 142.9 0 78.9-368.2 142.9-822.4 142.9-454.2 0-822.4-64-822.4-142.9 0-79 368.2-142.9 822.4-142.9z"/>
									</g>
								</g>
							</g>
							<path id="Layer" fill-rule="evenodd" class="s10" d="m856.6 1816.7q0 29.9-19.6 47.4-19.6 17.6-55.4 17.6-29.3 0-45-10.6-15.7-10.6-15.7-28.6v-17.7h0.9q24.1 21.5 55.5 21.5 21.5 0 30.5-6.7 9-6.7 9-19.5 0-11-8.6-16.7-8.5-5.6-38.8-12-52.8-11.3-52.8-51.1 0-26.2 20.5-45.3 20.4-19.2 54.1-19.2 31.7 0 45.8 10.7 14.1 10.8 14.1 25.6v18.3h-1.2q-18.3-19.5-51.7-19.5-42.1 0-42.1 25.6 0 9.3 9.7 15.2 9.7 6 36.8 11.8 54 11.3 54 53.2zm244 61.5q-11.9 0-18.8-6.7-6.8-6.6-6.8-19.7 0-19.8-2.6-61.7-2.6-42-4.9-56.2-8.2 19.7-9.3 23.5l-27.9 85.4q-7.6 22.9-16.3 35.4h-10.7q-21.2 0-32-31.6l-28.5-84q-4.9-14.5-11-29-3.5 19.7-6.1 66.6-2.6 46.9-2.6 78h-13.9q-11.4 0-18.2-6.9-6.8-7-6.8-19.5 0-32.5 5.2-87.3 5.2-54.7 10.8-85.2h8.4q20.6 0 34.7 8.8 14.1 8.9 19.9 24.9l27.6 77.8q8.7 23.8 11.3 37.8 1.2-6.1 5.5-20.4l23-70.6q6.9-22 15.5-37.4 8.6-15.4 15.5-20.9h4.7q16.8 0 27.1 10.4 10.4 10.5 12.7 30.8 2.9 25 6.2 77.1 3.4 52.2 3.4 80.6zm189.9-186.4q0 7.5-7.2 14.9-7.3 7.4-16.6 7.4h-36.6v164.1h-16q-10.7 0-17.4-6.9-6.7-7-6.7-19.8v-137.4h-57.2v-13.1q0-8.1 6.4-14.9 6.4-6.8 17.7-6.8h133.6v12.5zm161.2 47.9q0 31.1-19 49.5-19 18.4-48.1 18.4-20.9 0-33.4-6.6v77.2h-15.7q-9.5 0-16.8-6.9-7.3-7-7.3-21.5v-162.7q8.7-3.2 25.2-6.2 16.4-3.1 26.5-3.1h22.4q29.6 0 47.9 16.3 18.3 16.2 18.3 45.6zm-40.9 3.5q0-16.9-7.3-24.3-7.3-7.4-22.9-7.4h-12.8q-2.6 0-16.6 2.6v56.4q11.9 3.4 28.2 3.4 15.1 0 23.2-8.1 8.2-8.1 8.2-22.6z"/>
						</g>
					</g>
				</svg>
				'
			),
			30
		);

		// Home.
		add_submenu_page(
			'quillsmtp',
			__( 'Home', 'quillsmtp' ),
			__( 'Home', 'quillsmtp' ),
			'manage_options',
			'quillsmtp',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Settings.
		add_submenu_page(
			'quillsmtp',
			__( 'Settings', 'quillsmtp' ),
			__( 'Settings', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=settings',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Email Test.
		add_submenu_page(
			'quillsmtp',
			__( 'Email Test', 'quillsmtp' ),
			__( 'Email Test', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=email-test',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Alerts.
		add_submenu_page(
			'quillsmtp',
			__( 'Alerts', 'quillsmtp-pro' ),
			__( 'Alerts', 'quillsmtp-pro' ),
			'manage_options',
			'quillsmtp&path=alerts',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Logs.
		add_submenu_page(
			'quillsmtp',
			__( 'Logs', 'quillsmtp' ),
			__( 'Logs', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=logs',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// Debug.
		add_submenu_page(
			'quillsmtp',
			__( 'Debug', 'quillsmtp' ),
			__( 'Debug', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=debug',
			array( Admin_Loader::class, 'page_wrapper' )
		);

		// License.
		add_submenu_page(
			'quillsmtp',
			__( 'License', 'quillsmtp' ),
			__( 'License', 'quillsmtp' ),
			'manage_options',
			'quillsmtp&path=license',
			array( Admin_Loader::class, 'page_wrapper' )
		);
	}
}
