<?php
/**
 * Class Plan_Type
 *
 * @package    Morning\WC\Enum
 * @subpackage Plan_Type
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Plan_Type
 *
 * @package Morning\WC\Enum
 */
class Plan_Type {
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const BASIC = 0;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CLEARING = 1;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const INVOICING = 2;


	/**
	 * @param int $plan Plan number.
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public static function get_label( int $plan ): string {
		$plans = [
			self::BASIC     => __( 'Basic (Legacy)', 'wc-gateway-greeninvoice' ),
			self::CLEARING  => __( 'Clearing', 'wc-gateway-greeninvoice' ),
			self::INVOICING => __( 'Invoicing', 'wc-gateway-greeninvoice' ),
		];

		return $plans[ $plan ] ?? '';
	}
}
