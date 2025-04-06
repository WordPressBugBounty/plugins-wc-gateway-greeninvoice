<?php
/**
 * Class Frontend
 *
 * @package    Morning\WC
 * @subpackage Frontend
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.2.0
 */

namespace Morning\WC;

use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class Frontend
 *
 * @package Morning\WC
 */
class Frontend {
	/**
	 * Frontend constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'woocommerce_before_cart', [ $this, 'maybe_print_error' ] );

		add_filter( 'woocommerce_my_account_my_orders_actions', [ $this, 'inject_download_invoice_action' ], 10, 2 );
		add_filter( 'render_block_woocommerce/cart', [ $this, 'maybe_print_error_block' ] );
	}


	/**
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register_assets(): void {
		wp_register_style( MRN_WC_SLUG . '-frontend', MRN_WC_URL . 'assets/css/frontend.css', [], MRN_WC_VERSION );

		wp_register_script( MRN_WC_SLUG . '-frontend', MRN_WC_URL . 'assets/js/frontend.js', [ 'jquery' ], MRN_WC_VERSION, [ 'in_footer' => true ] );

		if ( is_checkout() || is_cart() ) {
			wp_enqueue_style( MRN_WC_SLUG . '-frontend' );
			wp_enqueue_script( MRN_WC_SLUG . '-frontend' );
		}
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_error() {
		if ( ! empty( $_REQUEST['mrn-wc-error'] ) ) {
			wc_print_notice( $_REQUEST['mrn-wc-error'], 'error' );
		}
	}


	/**
	 * @param array $actions Order actions.
	 * @param WC_Order $order Current order.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function inject_download_invoice_action( array $actions, WC_Order $order ): array {
		$order_meta = $order->get_meta( MRN_WC_SLUG . '_data' );

		if ( ! empty( $order_meta['copy_doc_url'] ) ) {
			$actions['download_invoice'] = [
				'url'  => $order_meta['copy_doc_url'],
				'name' => esc_html_x( 'Download Invoice', 'My Account Orders', 'wc-gateway-greeninvoice' ),
			];
		}

		return $actions;
	}

	/**
	 * @param string $block_content Block content.
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_error_block( string $block_content ): string {
		$output = '';

		if ( ! empty( $_REQUEST['mrn-wc-error'] ) ) {
			$output = wc_print_notice( $_REQUEST['mrn-wc-error'], 'error', [], true );
		}

		return $output . $block_content;
	}
}
