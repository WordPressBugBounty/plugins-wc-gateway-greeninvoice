<?php
/**
 * Class Document_Shipping_Rows_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Document_Shipping_Rows_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.0
 * @since      1.6.0
 */

namespace Morning\WC\Mappers;

use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * class Document_Shipping_Rows_Mapper
 *
 * @package Morning\WC\Mappers
 */
class Document_Shipping_Rows_Mapper {
	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public static function map( WC_Order $order ): array {
		$rows = [];

		if ( ! empty( $order->get_shipping_method() ) ) {
			$rows[] = [
				'description' => $order->get_shipping_method(),
				'price'       => $order->get_shipping_total(),
				'taxable'     => (bool) $order->get_shipping_tax(),
				'tax'         => $order->get_shipping_tax(),
			];
		}

		return $rows;
	}
}
