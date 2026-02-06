<?php
/**
 * Class Ajax
 *
 * @package    Morning\WC
 * @subpackage Ajax
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.2
 * @since      1.2.0
 */

namespace Morning\WC;

use Morning\WC\Http\Http_Code;
use Morning\WC\Utilities\Auth;
use Morning\WC\Utilities\Exporter;
use Throwable;

defined( 'ABSPATH' ) || exit;


/**
 * Class Ajax
 *
 * @package Morning\WC
 */
class Ajax {
	/**
	 * @var Auth
	 *
	 * @since 2.0.0
	 */
	private Auth $auth;
	/**
	 * @var Exporter
	 *
	 * @since 2.0.0
	 */
	private Exporter $exporter;


	/**
	 * AJAX constructor.
	 *
	 * @param Auth $auth
	 * @param Exporter $exporter
	 *
	 * @since 1.2.0
	 */
	public function __construct( Auth $auth, Exporter $exporter ) {
		$this->auth     = $auth;
		$this->exporter = $exporter;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'wp_ajax_morning_sync_gateways', [ $this, 'sync_gateways' ] );
		add_action( 'wp_ajax_greeninvoice_generate_debug_file', [ $this, 'generate_debug_file' ] );
	}


	/**
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function sync_gateways(): void {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'morning-sync-gateways' ) ) {
			wp_die( 'Invalid nonce' );
		}

		$response = $this->auth->authorize_store();

		if ( $response->is_ok() ) {
			wp_send_json_success( $response->json_body() );
		} else {
			wp_send_json_error( $response->json_body() );
		}
	}

	/**
	 * @return void
	 *
	 * @since 1.4.0
	 */
	public function generate_debug_file(): void {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'morning-download-debug-file' ) ) {
			wp_die( 'Invalid nonce' );
		}

		try {
			$this->exporter->stream();
		} catch ( Throwable $ex ) {
			wp_send_json_error(
				[
					'error' => $ex->getMessage(),
					'trace' => $ex->getTraceAsString(),
				],
				Http_Code::BAD_REQUEST
			);
		}
	}
}
