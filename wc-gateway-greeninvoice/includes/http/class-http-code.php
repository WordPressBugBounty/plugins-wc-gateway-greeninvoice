<?php
/**
 * Class Http_Code
 *
 * @package    Morning\WC\Http
 * @subpackage Http_Code
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Http;

defined( 'ABSPATH' ) || exit;


/**
 * Class Http_Code
 *
 * @package Morning\WC\Http
 */
class Http_Code {
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const OK = 200;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CREATED = 201;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const BAD_REQUEST = 400;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const UNAUTHORIZED = 401;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const FORBIDDEN = 403;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const NOT_FOUND = 404;


	/**
	 * @param int $code Http code.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public static function is_success( int $code ): bool {
		return in_array( $code, [ self::OK, self::CREATED ], true );
	}
}
