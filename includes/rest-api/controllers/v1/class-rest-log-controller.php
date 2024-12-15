<?php
/**
 * REST API: Log Controller
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage API
 */

namespace QuillSMTP\REST_API\Controllers\V1;

use QuillSMTP\Abstracts\REST_Controller;
use QuillSMTP\Log_Handlers\Log_Handler_DB;
use QuillSMTP\QuillSMTP;
use QuillSMTP\Security;
use QuillSMTP\Utils;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST_Log_Controller is REST api controller class for log
 *
 * @since 1.0.0
 */
class REST_Log_Controller extends REST_Controller {

	/**
	 * REST Base
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $rest_base = 'logs';

	/**
	 * Register the routes for the controller.
	 *
	 * @since 1.6.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_items' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<log_id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
			)
		);

		// Export logs.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/export',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'export_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get all logs.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$levels = $request->get_param( 'levels' ) ?? false;
		if ( $levels ) {
			$levels = explode( ',', $levels );
		}

		// check export.
		$export = $request->get_param( 'export' );
		if ( $export ) {
			return $this->export_items( $export, $levels );
		}

		$per_page = $request->get_param( 'per_page' );
		$page     = $request->get_param( 'page' );
		$offset   = $per_page * ( $page - 1 );
		$logs     = Log_Handler_DB::get_all( $levels, $offset, $per_page );

		$total_items = Log_Handler_DB::get_count( $levels );
		$total_pages = ceil( $total_items / $per_page );

		$data = array(
			'items'       => $logs,
			'total_items' => $total_items,
			'page'        => $page,
			'per_page'    => $per_page,
			'total_pages' => $total_pages,
		);

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Export items with pagination and file creation.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function export_items( $request ) {
		$download           = $request->get_param( 'download' ) ?? false;
		$level              = $request->get_param( 'level' ) ?? false;
		$offset             = intval( $request->get_param( 'offset' ) ?? 0 );
		$limit              = 100;
		$max_execution_time = Utils::get_max_execution_time();
		$start_time         = microtime( true );
		$file_id            = ! empty( $request->get_param( 'file_id' ) ) ? $request->get_param( 'file_id' ) : time();
		$file_path          = $this->get_temp_file_path( $file_id );

		if ( is_wp_error( $file_path ) ) {
			return $file_path;
		}

		if ( $download ) {
			$this->export_json( $file_path );
		}

		$fp = file_exists( $file_path ) ? fopen( $file_path, 'a' ) : fopen( $file_path, 'w' );

		if ( ! $fp ) {
			return new WP_Error(
				'quillsmtp_cannot_create_file',
				esc_html__( 'Cannot create export file', 'quillsmtp' ),
				[ 'status' => 500 ]
			);
		}

		if ( $offset === 0 ) {
			fwrite( $fp, "[\n" );
		}

		while ( ( microtime( true ) - $start_time ) < $max_execution_time && ! Utils::is_memory_limit_reached() ) {
			$logs = Log_Handler_DB::get_all( $level, $offset, $limit );

			if ( empty( $logs ) ) {
				fseek( $fp, -2, SEEK_END );
				fwrite( $fp, "\n]\n" );
				fclose( $fp );

				return new WP_REST_Response(
					[
						'status'  => 'done',
						'file_id' => $file_id,
					],
					200
				);
			}

			foreach ( $logs as $log ) {
				fwrite( $fp, json_encode( $log ) . ",\n" );
				$offset++;
			}
		}

		fclose( $fp );

		return new WP_REST_Response(
			[
				'status'  => 'continue',
				'offset'  => $offset,
				'file_id' => $file_id,
			],
			200
		);
	}

	/**
	 * Export rows as JSON file for download.
	 *
	 * @param string $file_path File path.
	 *
	 * @return void
	 */
	private function export_json( $file_path ) {
		$filename = 'logs_export.json';

		if ( ini_get( 'display_errors' ) ) {
			ini_set( 'display_errors', '0' );
		}

		nocache_headers();
		header( 'X-Robots-Tag: noindex', true );
		header( 'Content-Type: application/json' );
		header( 'Content-Description: File Transfer' );
		header( "Content-Disposition: attachment; filename=\"$filename\";" );
		header( 'Content-Length: ' . filesize( $file_path ) );

		readfile( $file_path );
		// Delete temp file.
		unlink( $file_path );
		exit;
	}

	/**
	 * get temp file path.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID.
	 * @param int $file_id File ID.
	 *
	 * @return string
	 */
	private function get_temp_file_path( $file_id ) {
		$temp_dir = QuillSMTP::get_upload_dir() . '/temp';
		if ( ! Security::prepare_upload_dir( $temp_dir ) ) {
			return new WP_Error( 'quillsmtp_cannot_create_dir', 'Cannot create dir' );
		}

		$file_name = sanitize_file_name( "quillsmtp-export-logs-{$file_id}.json" );
		$file_path = "{$temp_dir}/{$file_name}";

		return $file_path;
	}

	/**
	 * Check if a given request has access to get all items.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}

	/**
	 * Delete items from the collection
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_RESPONSE
	 */
	public function delete_items( $request ) {
		if ( isset( $request['ids'] ) ) {
			$ids     = empty( $request['ids'] ) ? array() : explode( ',', $request['ids'] );
			$deleted = (bool) Log_Handler_DB::delete( $ids );
		} else {
			$deleted = (bool) Log_Handler_DB::flush();
		}

		return new WP_REST_Response( array( 'success' => $deleted ), 200 );
	}

	/**
	 * Delete items permission check
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function delete_items_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}

	/**
	 * Delete one item from the collection
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_RESPONSE
	 */
	public function delete_item( $request ) {
		$deleted = Log_Handler_DB::delete( $request->get_param( 'log_id' ) );

		if ( ! $deleted ) {
			return new WP_Error( 'quillsmtp_logs_db_error_on_deleting_log', __( 'Error on deleting log in db!', 'quillsmtp' ), array( 'status' => 422 ) );
		}

		return new WP_REST_Response();
	}

	/**
	 * Delete item permission check
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check( $request ) {
		$capability = 'manage_options';
		return current_user_can( $capability, $request );
	}


}
