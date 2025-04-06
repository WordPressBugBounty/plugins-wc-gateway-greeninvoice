<?php
/**
 * Class Base_Formatter
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Base_Formatter
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.4.0
 */

namespace Morning\WC\Base;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Formatter
 *
 * @package Morning\WC\Abstracts
 */
abstract class Base_Formatter {
	/**
	 * @param mixed $value Value to format.
	 * @param array $args Optional arguments to pass.
	 *
	 * @return mixed
	 *
	 * @since 1.4.0
	 */
	abstract public static function format( $value, array $args = [] );


	/**
	 * @param array $args Arguments list.
	 * @param array $defaults Defaults arguments values.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public static function parse_args( array $args, array $defaults = [] ): array {
		$out = [];

		foreach ( $defaults as $key => $value ) {
			$out[ $key ] = $args[ $key ] ?? $value;
		}

		return $out;
	}
}
