<?php
/**
 * Class Document_Coupons_Rows_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Document_Coupons_Rows_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.6.0
 */

namespace Morning\WC\Mappers;

use Morning\WC\Base\Base_Mapper;
use WC_Order;
use WC_Order_Item_Coupon;

defined( 'ABSPATH' ) || exit;


/**
 * class Document_Coupons_Rows_Mapper
 *
 * @package Morning\WC\Mappers
 */
class Document_Coupons_Rows_Mapper implements Base_Mapper {
	/**
	 * @param WC_Order $order Order details
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public static function map( WC_Order $order ): array {
		$rows = [];

		if ( ! empty( $order->get_coupons() ) ) {
			/** @var WC_Order_Item_Coupon $coupon */
			foreach ( $order->get_items( 'coupon' ) as $coupon ) {
				$rows[] = [
					'description' => $coupon->get_name(),
					'price'       => $coupon->get_discount(),
					'taxable'     => (bool) $coupon->get_discount_tax(),
					'tax'         => $coupon->get_discount_tax(),
				];
			}
		}

		return $rows;
	}
}
