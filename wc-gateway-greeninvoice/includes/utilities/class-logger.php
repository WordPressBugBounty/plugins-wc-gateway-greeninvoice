<?php
/**
 * Class Logger
 *
 * @package    Morning\WC\Utilities
 * @subpackage Logger
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.3
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
	 * @since 2.4.3
	 */
	private const REDACTED_KEYS = [ 'authorization', 'license_key' ];


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
			$log .= ' with context:' . PHP_EOL . '---------------[START]---------------' . PHP_EOL . wc_print_r( self::redact( $context ), true ) . '----------------[END]----------------';
		}

		( wc_get_logger() )->log( $level, $log, [ 'source' => MRN_WC_SLUG ] );
	}

	/**
	 * @param array $data Log context.
	 *
	 * @return array
	 *
	 * @since 2.4.3
	 */
	private static function redact( array $data ): array {
		foreach ( $data as $key => $value ) {
			if ( is_string( $key ) && in_array( strtolower( $key ), self::REDACTED_KEYS, true ) ) {
				$data[ $key ] = '[REDACTED]';
			} elseif ( is_array( $value ) ) {
				$data[ $key ] = self::redact( $value );
			}
		}

		return $data;
	}
}
