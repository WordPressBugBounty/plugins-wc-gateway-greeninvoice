<?php
/**
 * Class Formatter
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Formatter
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC\Abstracts;

defined( 'ABSPATH' ) || exit;


/**
 * Class Formatter
 *
 * @package Morning\WC\Abstracts
 */
abstract class Formatter {
	/**
	 * Format a given value.
	 *
	 * @param mixed $value Value to format.
	 * @param array $args Optional arguments to pass
	 *
	 * @return mixed
	 *
	 * @since 1.4.0
	 */
	abstract public static function format( $value, array $args = [] );


	/**
	 * Parse arguments with default values and structure.
	 *
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
