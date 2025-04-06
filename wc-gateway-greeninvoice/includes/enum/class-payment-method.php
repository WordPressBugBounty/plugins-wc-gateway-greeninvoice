<?php
/**
 * Class Payment_Method
 *
 * @package    Morning\WC\Enum
 * @subpackage Payment_Method
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Method
 *
 * @package Morning\WC\Enum
 */
class Payment_Method {
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const OTHER = 0;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CASH = 1;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CREDIT_CARD = 2;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const WIRE_TRANSFER = 3;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const PAYPAL = 4;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const BIT = 5;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const GOOGLE_PAY = 6;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const APPLE_PAY = 7;
}
