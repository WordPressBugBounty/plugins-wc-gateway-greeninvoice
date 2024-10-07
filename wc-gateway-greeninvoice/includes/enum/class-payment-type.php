<?php
/**
 * Class Payment_Type
 *
 * @package    Morning\WC\Enum
 * @subpackage Payment_Type
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.1
 * @since      1.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Type
 *
 * @package Morning\WC\Enum
 */
class Payment_Type {
	/**
	 * @since 1.0.0
	 */
	const CREDIT_CARD = 100;

	/**
	 * @since 1.0.0
	 */
	const PAYPAL = 110;

	/**
	 * @since 1.0.0
	 */
	const BIT = 120;

	/**
	 * @since 1.1.5
	 */
	const GOOGLE_PAY = 150;

	/**
	 * @since 1.2.1
	 */
	const APPLE_PAY = 160;


	/**
	 * Get all supported payment types.
	 *
	 * @return int[]
	 *
	 * @version 1.2.1
	 * @since 1.2.0
	 */
	public static function get_all(): array {
		return [ self::CREDIT_CARD, self::PAYPAL, self::BIT, self::GOOGLE_PAY, self::APPLE_PAY ];
	}


	/**
	 * Retrieve enum type label.
	 *
	 * @param int $type Type value.
	 *
	 * @return string
	 *
	 * @version 1.2.1
	 * @since 1.2.0
	 */
	public static function get_label( int $type ): string {
		$labels = [
			self::CREDIT_CARD => esc_html__( 'Credit Card', 'wc-gateway-greeninvoice' ),
			self::PAYPAL      => esc_html__( 'PayPal', 'wc-gateway-greeninvoice' ),
			self::BIT         => esc_html__( 'Bit', 'wc-gateway-greeninvoice' ),
			self::GOOGLE_PAY  => esc_html__( 'Google Pay', 'wc-gateway-greeninvoice' ),
			self::APPLE_PAY   => esc_html__( 'Apple Pay', 'wc-gateway-greeninvoice' ),
		];

		return $labels[ $type ] ?? '';
	}
}
