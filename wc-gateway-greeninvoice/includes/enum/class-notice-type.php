<?php
/**
 * Class Notice_Type
 *
 * @package    Morning\WC\Enum
 * @subpackage Notice_Type
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.0
 * @since      2.4.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Notice_Type
 *
 * @package Morning\WC\Enum
 */
class Notice_Type {
	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	const INFO = 'info';
	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	const SUCCESS = 'success';
	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	const WARNING = 'warning';
	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	const ERROR = 'error';


	/**
	 * @return string[]
	 *
	 * @since 2.4.0
	 */
	public static function get_all(): array {
		return [
			self::INFO,
			self::SUCCESS,
			self::WARNING,
			self::ERROR,
		];
	}

	/**
	 * @param string $type Notice type.
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	public static function is_valid( string $type ): bool {
		return in_array( $type, self::get_all(), true );
	}
}
