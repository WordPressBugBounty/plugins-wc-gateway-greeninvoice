<?php
/**
 * Class Http_Method
 *
 * @package    Morning\WC\Http
 * @subpackage Http_Method
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Http;

defined( 'ABSPATH' ) || exit;


/**
 * Class Http_Method
 *
 * @package Morning\WC\Http
 */
class Http_Method {
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const GET = 'GET';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const POST = 'POST';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const PUT = 'PUT';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const DELETE = 'DELETE';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const PATCH = 'PATCH';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const HEAD = 'HEAD';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const OPTIONS = 'OPTIONS';


	/**
	 * @param string $value
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public static function is_supported( string $value ): bool {
		return in_array(
			$value,
			[
				self::GET,
				self::POST,
				self::PUT,
				self::DELETE,
				self::PATCH,
				self::HEAD,
				self::OPTIONS,
			],
			true
		);
	}
}
