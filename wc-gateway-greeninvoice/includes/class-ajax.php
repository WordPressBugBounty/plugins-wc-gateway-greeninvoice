<?php
/**
 * Class AJAX
 *
 * @package    Morning\WC
 * @subpackage AJAX
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.2.0
 */

namespace Morning\WC;

use Morning\WC\Enum\HTTP_Code;
use Morning\WC\Exceptions\FileSystem_Exception;
use Morning\WC\Utilities\API;
use Morning\WC\Utilities\Exporter;

defined( 'ABSPATH' ) || exit;


/**
 * Class AJAX
 *
 * @package Morning\WC
 */
class AJAX {
	/**
	 * AJAX constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_morning_sync_gateways', [ $this, 'sync_gateways' ] );
		add_action( 'wp_ajax_greeninvoice_generate_debug_file', [ $this, 'generate_debug_file' ] );
	}


	/**
	 * Send sync request to get recent changes.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function sync_gateways(): void {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'morning-sync-gateways' ) ) {
			wp_die( 'Invalid nonce' );
		}

		$api      = API::get_instance();
		$response = $api->connect_store();

		$json = json_decode( wp_remote_retrieve_body( $response ) );

		if ( HTTP_Code::OK === wp_remote_retrieve_response_code( $response ) ) {
			wp_send_json_success( $json );
		} else {
			wp_send_json_error( $json );
		}
	}

	/**
	 * Generate and stream a debugging file including logs and environment data.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	public function generate_debug_file(): void {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'morning-download-debug-file' ) ) {
			wp_die( 'Invalid nonce' );
		}

		try {
			( new Exporter() )->stream();
		} catch ( FileSystem_Exception $ex ) {
			wp_send_json_error();
		}
	}
}
