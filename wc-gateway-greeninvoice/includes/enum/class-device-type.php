<?php
/**
 * Class Device_Type
 *
 * @package    Morning\WC\Enum
 * @subpackage Device_Type
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.1.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Device_Type
 *
 * @package Morning\WC\Enum
 */
class Device_Type {
	/**
	 * Desktop user.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	const WEB = 'web';

	/**
	 * iOS user.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	const IOS = 'ios';

	/**
	 * Android user.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	const ANDROID = 'android';
}
