<?php
/**
 * Class Updater
 *
 * @package    Morning\WC
 * @subpackage Updater
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.2.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Blocks\Options;
use Morning\WC\Enum\Setting;
use Morning\WC\Utilities\API;
use Morning\WC\Utilities\Settings;

defined( 'ABSPATH' ) || exit;


/**
 * Class Updater
 *
 * @package Morning\WC
 */
class Updater {
	/**
	 * Updater constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'maybe_run_updates' ] );
	}

	/**
	 * Check whether to run updates.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function maybe_run_updates(): void {
		if ( $this->before( '1.2.0' ) ) {
			$this->v1_2_0_migration();
		}

		if ( $this->before( '1.2.2' ) ) {
			$this->v1_2_2_migration();
		}

		if ( $this->before( '1.4.0' ) ) {
			$this->v1_4_0_migration();
		}
	}

	/**
	 * Checked if current version is below a required version.
	 *
	 * @param string $required_version
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	public function before( string $required_version ): bool {
		$version = get_option( Setting::DB_VERSION );

		return version_compare( $version, $required_version, '<' );
	}

	/**
	 * Update database version with latest plugin version.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	private function update_version(): void {
		update_option( Setting::DB_VERSION, MRN_WC_VERSION );
	}


	/**
	 * v1.2.0 Migration:
	 * - Run store connect in order to support dynamic gateways.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	private function v1_2_0_migration(): void {
		API::get_instance()->connect_store();

		$this->update_version();
	}

	/**
	 * v1.2.2 Migration:
	 * - Setup IPN Completed order status.
	 *
	 * @return void
	 *
	 * @since 1.2.2
	 */
	private function v1_2_2_migration(): void {
		$options = Settings::get_options();

		$options[ Setting::ORDER_STATUS ] = defined( 'MRN_WC_IPN_COMPLETED' ) && MRN_WC_IPN_COMPLETED ? 'completed' : 'processing';

		update_option( Settings::OPTIONS_KEY, $options );

		$this->update_version();
	}

	/**
	 * v1.4.0 Migration:
	 * - Fix Sync Gateway method.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function v1_4_0_migration(): void {
		API::get_instance()->connect_store();

		$this->update_version();
	}
}
