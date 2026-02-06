<?php
/**
 * Class Document_Income_Rows_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Document_Income_Rows_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.7
 * @since      1.6.0
 */

namespace Morning\WC\Mappers;

use Morning\WC\Base\Base_Mapper;
use WC_Order;
use WC_Order_Item_Fee;
use WC_Order_Item_Product;

defined( 'ABSPATH' ) || exit;


/**
 * class Document_Income_Rows_Mapper
 *
 * @package Morning\WC\Mappers
 */
class Document_Income_Rows_Mapper implements Base_Mapper {
	/**
	 * @param WC_Order $order Order details
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public static function map( WC_Order $order ): array {
		$rows = [];

		$has_discount = count( $order->get_items( 'coupon' ) ) > 0;

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$product = $item->get_product();
			$tax     = self::get_item_tax( $item, $has_discount ) / $item->get_quantity();

			$rows[] = [
				'description' => $item->get_name(),
				'quantity'    => $item->get_quantity(),
				'price'       => self::get_item_price( $order, $item, $has_discount ),
				'sku'         => $product->get_sku(),
				'taxable'     => $product->is_taxable(),
				'tax'         => $tax,
			];
		}

		if ( ! empty( $order->get_fees() ) ) {
			/** @var WC_Order_Item_Fee $fee */
			foreach ( $order->get_fees() as $fee ) {
				$tax = floatval( $fee->get_total_tax() ) / $fee->get_quantity();

				$rows[] = [
					'description' => $fee->get_name(),
					'quantity'    => $fee->get_quantity(),
					'price'       => floatval( $fee->get_amount() ),
					'sku'         => '',
					'taxable'     => 'taxable' === $fee->get_tax_status(),
					'tax'         => $tax,
				];
			}
		}

		return $rows;
	}


	/**
	 * @param WC_Order_Item_Product $item Order product item
	 * @param bool $has_discount Does the order have a discount?
	 *
	 * @return float
	 *
	 * @since 2.3.7
	 */
	private static function get_item_tax( WC_Order_Item_Product $item, bool $has_discount ): float {
		return floatval( $has_discount ? $item->get_subtotal_tax() : $item->get_total_tax() );
	}

	/**
	 * @param WC_Order $order Order details
	 * @param WC_Order_Item_Product $item Order product item
	 * @param bool $has_discount Does the order have a discount?
	 *
	 * @return float
	 *
	 * @since 2.3.7
	 */
	private static function get_item_price( WC_Order $order, WC_Order_Item_Product $item, bool $has_discount ): float {
		return $has_discount ? $order->get_item_subtotal( $item, true, false ) : $order->get_item_total( $item, true, false );
	}
}
