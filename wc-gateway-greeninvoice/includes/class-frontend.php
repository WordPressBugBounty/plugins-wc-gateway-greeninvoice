<?php
/**
 * Class Frontend
 *
 * @package    Morning\WC
 * @subpackage Frontend
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.2.0
 */

namespace Morning\WC;

use Morning\WC\Enum\Setting;
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
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

		add_filter( 'woocommerce_my_account_my_orders_actions', [ $this, 'inject_download_invoice_action' ], 10, 2 );
	}


	/**
	 * Register frontend stylesheets and scripts.
	 *
	 * @since 1.0.0
	 */
	public function register_assets(): void {
		wp_register_style( MRN_WC_SLUG . '-frontend', MRN_WC_URL . 'assets/css/frontend.min.css', [], MRN_WC_VERSION );

		wp_register_script(
			MRN_WC_SLUG . '-frontend',
			MRN_WC_URL . 'assets/js/frontend.min.js',
			[ 'jquery', 'wc-blocks-checkout' ],
			MRN_WC_VERSION,
			[ 'in_footer' => true ]
		);

		if ( is_checkout() ) {
			wp_enqueue_style( MRN_WC_SLUG . '-frontend' );
			wp_enqueue_script( MRN_WC_SLUG . '-frontend' );
		}
	}


	/**
	 * Add download invoice action to my account orders.
	 *
	 * @param array $actions Order actions.
	 * @param WC_Order $order Current order.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function inject_download_invoice_action( array $actions, WC_Order $order ): array {
		if ( false !== strpos( $order->get_payment_method(), MRN_WC_SLUG ) ) {
			$order_meta = $order->get_meta( Setting::ORDER_META );

			if ( ! empty( $order_meta['copy_doc_url'] ) ) {
				$actions['download_invoice'] = [
					'url'  => $order_meta['copy_doc_url'],
					'name' => esc_html_x( 'Invoice', 'My Account Orders', 'wc-gateway-greeninvoice' ),
				];
			}
		}

		return $actions;
	}
}
