<?php
/**
 * Class Logger
 *
 * @package    Morning\WC\Utilities
 * @subpackage Logger
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Enum\Setting;
use WC_Logger_Interface;

defined( 'ABSPATH' ) || exit;


/**
 * Class Logger
 *
 * @package Morning\WC\Utilities
 */
final class Logger {
	/**
	 * Logger instance.
	 *
	 * @var null|Logger
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * WC_Logger instance.
	 *
	 * @var null|WC_Logger_Interface
	 *
	 * @since 1.0.0
	 */
	private $logger = null;

	/**
	 * Enable debugging.
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	private $debug = false;


	/**
	 * Logger constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_null( $this->logger ) ) {
			$this->logger = wc_get_logger();
		}

		$this->debug = Settings::is_enabled( Setting::DEBUGGING );
	}


	/**
	 * Log several events to log file.
	 *
	 * @param mixed $message Message to log.
	 * @param string $level Log level.
	 *
	 * @since 1.0.0
	 */
	public function log( $message, string $level = 'error' ): void {
		if ( ! $this->debug ) {
			return;
		}

		if ( ! is_scalar( $message ) ) {
			$message = wc_print_r( $message, true );
		}

		$message = PHP_EOL . '---------------[START]---------------' . PHP_EOL . $message . PHP_EOL . '----------------[END]----------------';

		$this->logger->log( $level, $message, [ 'source' => MRN_WC_SLUG ] );
	}


	/**
	 * Retrieve plugin's instance.
	 *
	 * @return Logger
	 *
	 * @since 1.0.0
	 */
	public static function get_instance(): Logger {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
