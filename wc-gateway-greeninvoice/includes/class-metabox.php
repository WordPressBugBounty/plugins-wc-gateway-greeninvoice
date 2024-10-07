<?php
/**
 * Class Metabox
 *
 * @package    Morning\WC
 * @subpackage Metabox
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Morning\WC\Enum\Setting;
use Morning\WC\Enum\Document_Type;
use WC_Order;
use WP_Post;

defined( 'ABSPATH' ) || exit;


/**
 * Class Metabox
 *
 * @package Morning\WC
 */
class Metabox {
	/**
	 * Metabox constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
	}


	/**
	 * Register plugin admin metaboxes.
	 *
	 * @since 1.0.0
	 */
	public function register_meta_boxes(): void {
		$screen = Compatibility::is_wc_hpos_active() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';

		add_meta_box(
			MRN_WC_SLUG . '-invoice-metabox',
			esc_html_x( 'Morning Document Info', 'Admin Meta Box', 'wc-gateway-greeninvoice' ),
			[ $this, 'admin_metabox_output' ],
			$screen,
			'side'
		);
	}

	/**
	 * Print gateway meta details.
	 *
	 * @param WP_Post|WC_Order $post_or_order Current post details or WC Order.
	 *
	 * @since 1.0.0
	 */
	public function admin_metabox_output( $post_or_order ): void {
		$order = ( $post_or_order instanceof WP_Post ) ? wc_get_order( $post_or_order->ID ) : $post_or_order;

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( false === strpos( $order->get_payment_method(), MRN_WC_SLUG ) ) {
			return;
		}

		$order_meta = $order->get_meta( Setting::ORDER_META );

		if ( empty( $order_meta ) ) {
			return;
		}

		$order_meta_labels = [
			'document_id' => esc_html__( 'Document Number', 'wc-gateway-greeninvoice' ),
			'type'        => esc_html__( 'Document Type', 'wc-gateway-greeninvoice' ),
			'id'          => esc_html__( 'Document ID', 'wc-gateway-greeninvoice' ),
		];

		$order_meta['type'] = sprintf( '%s (%d)', Document_Type::get_type( $order_meta['type'] ), $order_meta['type'] );

		require_once MRN_WC_PATH . 'templates/admin/order-metabox.php';
	}
}
