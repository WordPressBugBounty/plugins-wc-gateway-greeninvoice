<?php
/**
 * Class Tax_ID_Formatter
 *
 * @package    Morning\WC\Formatters
 * @subpackage Tax_ID_Formatter
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC\Formatters;

use Morning\WC\Abstracts\Formatter;

defined( 'ABSPATH' ) || exit;


/**
 * Class Tax_ID_Formatter
 *
 * @package Morning\WC\Formatters
 */
class Tax_ID_Formatter extends Formatter {
	/**
	 * Format value to a valid Israeli Tax ID number.
	 *
	 * @param string|integer $value Value to format.
	 * @param array $args Optional arguments to pass
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public static function format( $value, array $args = [] ): string {
		$meta = self::parse_args( $args, [ 'country' => '' ] );

		if ( empty( $value ) || 'IL' !== $meta['country'] ) {
			return $value;
		}

		return str_pad( $value, 9, '0', STR_PAD_LEFT );
	}
}
