<?php
/**
 * Class Document_Income_Rows_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Document_Income_Rows_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.0
 * @since      1.6.0
 */

namespace Morning\WC\Mappers;

use WC_Order;
use WC_Order_Item_Fee;
use WC_Order_Item_Product;

defined( 'ABSPATH' ) || exit;


/**
 * class Document_Income_Rows_Mapper
 *
 * @package Morning\WC\Mappers
 */
class Document_Income_Rows_Mapper {
	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public static function map( WC_Order $order ): array {
		$rows = [];

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$product = $item->get_product();
			$tax     = floatval( $item->get_subtotal_tax() ) / $item->get_quantity();

			$rows[] = [
				'description' => $item->get_name(),
				'quantity'    => $item->get_quantity(),
				'price'       => $order->get_item_subtotal( $item, true, false ),
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
}
