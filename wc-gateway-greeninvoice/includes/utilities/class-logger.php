<?php
/**
 * Class Logger
 *
 * @package    Morning\WC\Utilities
 * @subpackage Logger
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.0.0
 */

namespace Morning\WC\Utilities;

use WC_Log_Levels;

defined( 'ABSPATH' ) || exit;


/**
 * Class Logger
 *
 * @package Morning\WC\Utilities
 */
final class Logger {
	/**
	 * @param string $message Message to log.
	 * @param array $context Log extra data.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public static function debug( string $message, array $context = [] ): void {
		self::log( $message, $context, WC_Log_Levels::DEBUG );
	}

	/**
	 * @param string $message Message to log.
	 * @param array $context Log extra data.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public static function info( string $message, array $context = [] ): void {
		self::log( $message, $context, WC_Log_Levels::INFO );
	}

	/**
	 * @param string $message Message to log.
	 * @param array $context Log extra data.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public static function error( string $message, array $context = [] ): void {
		self::log( $message, $context, WC_Log_Levels::ERROR );
	}


	/**
	 * @param string $message Message to log.
	 * @param array $context Log extra data.
	 * @param string $level Log level.
	 *
	 * @since 1.0.0
	 */
	public static function log( string $message, array $context = [], string $level = WC_Log_Levels::INFO ): void {
		$log = $message;

		if ( ! empty( $context ) ) {
			$log .= ' with context:' . PHP_EOL . '---------------[START]---------------' . PHP_EOL . wc_print_r( $context, true ) . '----------------[END]----------------';
		}

		( wc_get_logger() )->log( $level, $log, [ 'source' => MRN_WC_SLUG ] );
	}
}
