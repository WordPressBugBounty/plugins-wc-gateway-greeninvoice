<?php
/**
 * Class Document_Client_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Document_Client_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.0
 * @since      1.6.0
 */

namespace Morning\WC\Mappers;

use Morning\WC\Base\Base_Mapper;
use Morning\WC\Checkout;
use Morning\WC\Formatters\Tax_ID_Formatter;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * class Document_Client_Mapper
 *
 * @package Morning\WC\Mappers
 */
class Document_Client_Mapper implements Base_Mapper {
	/**
	 * @param WC_Order $order Order details
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public static function map( WC_Order $order ): array {
		$country = ( ! empty( $order->get_billing_country() ) ) ? $order->get_billing_country() : MRN_WC_DEFAULT_COUNTRY;
		$tax_id  = self::get_tax_id( $order );

		return [
			'name'    => $order->get_billing_company() ? $order->get_billing_company() : $order->get_formatted_billing_full_name(),
			'taxId'   => Tax_ID_Formatter::format( $tax_id, [ 'country' => $country ] ),
			'country' => $country,
			'address' => $order->get_billing_address_1(),
			'city'    => $order->get_billing_city(),
			'zip'     => $order->get_billing_postcode(),
			'phone'   => $order->get_billing_phone(),
			'emails'  => [
				$order->get_billing_email(),
			],
		];
	}

	/**
	 * @param WC_Order $order Order details.
	 *
	 * @return string
	 *
	 * @since 2.4.0
	 */
	private static function get_tax_id( WC_Order $order ): string {
		$tax_id = (string) $order->get_meta( '_billing_tax_id' );

		if ( '' !== $tax_id ) {
			return $tax_id;
		}

		return (string) $order->get_meta( '_wc_billing/' . Checkout::TAX_ID_FIELD );
	}
}
