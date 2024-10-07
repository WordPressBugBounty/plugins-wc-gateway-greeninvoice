<?php
/**
 * Class HTTP_Code
 *
 * @package    Morning\WC\Enum
 * @subpackage HTTP_Code
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class HTTP_Code
 *
 * @package Morning\WC\Enum
 */
class HTTP_Code {
	/**
	 * @since 1.0.0
	 */
	const OK = 200;

	/**
	 * @since 1.2.0
	 */
	const BAD_REQUEST = 400;

	/**
	 * @since 1.0.0
	 */
	const UNAUTHORIZED = 401;

	/**
	 * @since 1.0.0
	 */
	const FORBIDDEN = 403;

	/**
	 * @since 1.0.0
	 */
	const NOT_FOUND = 404;
}
