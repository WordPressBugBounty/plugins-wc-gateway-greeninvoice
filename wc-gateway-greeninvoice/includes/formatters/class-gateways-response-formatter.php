<?php
/**
 * Class Gateways_Response_Formatter
 *
 * @package    Morning\WC\Formatters
 * @subpackage Gateways_Response_Formatter
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC\Formatters;

use Morning\WC\Abstracts\Formatter;
use Morning\WC\Enum\Payment_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Gateways_Response_Formatter
 *
 * @package Morning\WC\Formatters
 */
class Gateways_Response_Formatter extends Formatter {
	/**
	 * Format API gateways sync response.
	 *
	 * @param array $value API raw gateways sync response
	 * @param array $args Optional arguments to pass
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public static function format( $value, array $args = [] ): array {
		$gateways = [];

		foreach ( Payment_Type::get_all() as $payment_type ) {
			if ( empty( $value[ $payment_type ] ) ) {
				continue;
			}

			$gateways[ $payment_type ] = (string) $value[ $payment_type ];
		}

		return $gateways;
	}
}
