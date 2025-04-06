<?php
/**
 * Class PW_Gift_Cards_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage PW_Gift_Cards_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.5.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Base\Base_Integration;
use WC_Order;
use WC_Order_Item_PW_Gift_Card;

defined( 'ABSPATH' ) || exit;


/**
 * Class PW_Gift_Cards_Integration
 *
 * @package Morning\WC\Integrations
 */
final class PW_Gift_Cards_Integration extends Base_Integration {
	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function register_hooks(): void {
		parent::register_hooks();

		add_filter( 'morning/wc/order_invoice_params', [ $this, 'maybe_inject_gift_cards_payload' ], 10, 2 );
	}


	/**
	 * @param array $params Invoice parameters
	 * @param WC_Order $order Current order.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 */
	public function maybe_inject_gift_cards_payload( array $params, WC_Order $order ): array {
		$gifts_card_items = $order->get_items( 'pw_gift_card' );

		if ( empty( $gifts_card_items ) ) {
			return $params;
		}

		/** @var WC_Order_Item_PW_Gift_Card $gifts_card_item */
		foreach ( $gifts_card_items as $gifts_card_item ) {
			if ( floatval( $gifts_card_item->get_amount() ) <= 0 ) {
				continue;
			}

			$tax = is_callable(
				[
					$gifts_card_item,
					'get_subtotal_tax',
				]
			) ? ( $gifts_card_item->get_subtotal_tax() / $gifts_card_item->get_quantity() ) : $order->get_item_tax( $gifts_card_item, false );

			$params['items'][] = [
				'description' => sprintf(
				/* translators: %s Gift Card Number */
					__( 'Gift Card: %s', 'wc-gateway-greeninvoice' ),
					$gifts_card_item->get_card_number()
				),
				'quantity'    => $gifts_card_item->get_quantity(),
				'price'       => floatval( $gifts_card_item->get_amount() ) * - 1,
				'sku'         => '',
				'taxable'     => $gifts_card_item->get_tax_status() === 'taxable',
				'tax'         => $tax,
			];
		}

		return $params;
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_compatible(): bool {
		return $this->is_plugin_active( 'pw-woocommerce-gift-cards/pw-gift-cards.php' ) || $this->is_plugin_active( 'pw-gift-cards/pw-gift-cards.php' );
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function get_integration_name(): string {
		return 'Pimwick Gift Cards';
	}
}
