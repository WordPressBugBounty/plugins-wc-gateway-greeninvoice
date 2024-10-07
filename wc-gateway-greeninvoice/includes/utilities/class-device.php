<?php
/**
 * Class Device
 *
 * @package    Morning\WC\Utilities
 * @subpackage Device
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.1.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Enum\Device_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Device
 *
 * @package Morning\WC\Utilities
 */
class Device {
	/**
	 * Determine what device type is the user using.
	 *
	 * @return string
	 *
	 * @since 1.1.0
	 */
	public static function get_type(): string {
		$ua = self::get_user_agent();

		if ( empty( $ua ) ) {
			return Device_Type::WEB;
		}

		if ( preg_match( '/iPod|iPhone|iPad/i', $ua ) ) {
			return Device_Type::IOS;
		}

		if ( preg_match( '/Android/i', $ua ) ) {
			return Device_Type::ANDROID;
		}

		return Device_Type::WEB;
	}


	/**
	 * Retrieve current user agent.
	 *
	 * @return null|string
	 *
	 * @since 1.1.0
	 */
	public static function get_user_agent(): ?string {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return null;
		}

		return $_SERVER['HTTP_USER_AGENT'];
	}
}
