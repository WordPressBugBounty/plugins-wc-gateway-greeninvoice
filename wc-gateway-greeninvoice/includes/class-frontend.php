<?php
/**
 * Class Frontend
 *
 * @package    Morning\WC
 * @subpackage Frontend
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.2
 * @since      1.2.0
 */

namespace Morning\WC;

use Morning\WC\Config\Settings;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class Frontend
 *
 * @package Morning\WC
 */
class Frontend {
	/**
	 * @var Settings
	 *
	 * @since 2.0.5
	 */
	private Settings $settings;


	/**
	 * Frontend constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 1.2.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

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
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'inject_plugin_fragments' ] );
	}


	/**
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register_assets(): void {
		wp_register_style( MRN_WC_SLUG . '-frontend', MRN_WC_URL . 'assets/css/frontend.css', [], MRN_WC_VERSION );

		wp_register_script( MRN_WC_SLUG . '-frontend', MRN_WC_URL . 'assets/js/frontend.js', [ 'jquery' ], MRN_WC_VERSION, [ 'in_footer' => true ] );

		wp_localize_script(
			MRN_WC_SLUG . '-frontend',
			MRN_WC_SLUG . '_vars',
			[
				'installments_data'  => $this->settings->get_options()->get_installments(),
				'apply_pay_disabled' => __( 'Payment with Apple Pay is available on Safari browser only', 'wc-gateway-greeninvoice' ),
			]
		);

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

	/**
	 * @param array $fragments Fragments.
	 *
	 * @return array
	 *
	 * @since 2.3.0
	 */
	public function inject_plugin_fragments( array $fragments ): array {
		$fragments[ MRN_WC_SLUG . '_fragments' ] = [
			'cart_total' => floatval( WC()->cart->get_total( 'raw' ) ),
		];

		return $fragments;
	}
}
