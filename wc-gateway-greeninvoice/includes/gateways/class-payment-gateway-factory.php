<?php
/**
 * Class Payment_Gateway_Factory
 *
 * @package    Morning\WC\Gateways
 * @subpackage Payment_Gateway_Factory
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.1
 * @since      1.6.1
 */

namespace Morning\WC\Gateways;

use Morning\WC\Abstracts\Payment_Gateway;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Gateway_Factory
 *
 * @package Morning\WC\Gateways
 */
abstract class Payment_Gateway_Factory {
	/**
	 * Initiates new payment gateway.
	 *
	 * @param string $payment_gateway Payment gateway slug.
	 *
	 * @return Payment_Gateway|null
	 */
	public static function init( string $payment_gateway ): ?Payment_Gateway {
		switch ( $payment_gateway ) {
			case 'greeninvoice-creditcard':
				return new Credit_Card_Gateway();
			case 'greeninvoice-bit':
				return new Bit_Gateway();
			case 'greeninvoice-paypal':
				return new PayPal_Gateway();
			case 'greeninvoice-google-pay':
				return new Google_Pay_Gateway();
			case 'greeninvoice-apple-pay':
				return new Apple_Pay_Gateway();
		}

		return null;
	}
}
