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

use Morning\WC\Config\Options;
use Morning\WC\Config\Settings;
use Morning\WC\Utilities\Auth;

defined( 'ABSPATH' ) || exit;


/**
 * Class Updater
 *
 * @package Morning\WC
 */
class Updater {
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;
	/**
	 * @var Auth
	 *
	 * @since 2.0.0
	 */
	private Auth $auth;

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const DB_VERSION = MRN_WC_SLUG . '_db_version';


	/**
	 * Updater constructor.
	 *
	 * @param Settings $settings
	 * @param Auth $auth
	 *
	 * @since 1.2.0
	 */
	public function __construct( Settings $settings, Auth $auth ) {
		$this->settings = $settings;
		$this->auth     = $auth;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
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

		if ( $this->before( '2.0.0' ) ) {
			$this->v2_0_0_migration();
		}

		$this->update_version();
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
		$this->auth->authorize_store();
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
		$options = $this->settings->get_options();
		$options->set_order_status( defined( 'MRN_WC_IPN_COMPLETED' ) && MRN_WC_IPN_COMPLETED ? 'completed' : 'processing' );

		$options->save();
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
		$this->auth->authorize_store();
	}

	/**
	 * v2.0.0 Migration:
	 *  - New settings structure and fields.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function v2_0_0_migration(): void {
		$options = get_option( MRN_WC_SLUG . '_options' );

		$new_options = new Options();
		$new_options->set_license_key( $options['greeninvoice_license_key'] );
		$new_options->set_activated( $options['greeninvoice_activated'] );
		$new_options->set_order_status( $options['greeninvoice_order_status'] );
		$new_options->set_show_tax_id_field( '1' === $options['greeninvoice_tax_id_field'] );
		$new_options->set_sandbox_mode( '1' === $options['greeninvoice_sandbox'] );

		$gateways = [];
		if ( ! empty( $options['greeninvoice_gateways'] ) ) {
			foreach ( $options['greeninvoice_gateways'] as $gateway => $status ) {
				$gateways[ $gateway ] = '1' === $status ? 'yes' : 'no';
			}
		}

		$new_options->set_gateways( $gateways );
		$new_options->save();

		$this->settings->set_options( $new_options );
	}


	/**
	 * @param string $version Required version.
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	private function before( string $version ): bool {
		$db_version = $this->get_db_version();

		return version_compare( $db_version, $version, '<' );
	}

	/**
	 * @return void
	 *
	 * @since 1.2.0
	 */
	private function update_version(): void {
		$db_version = $this->get_db_version();

		if ( MRN_WC_VERSION !== $db_version ) {
			$this->set_db_version( MRN_WC_VERSION );
		}
	}


	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_db_version(): string {
		return get_option( self::DB_VERSION );
	}

	/**
	 * @param string $version Database version.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function set_db_version( string $version ): void {
		update_option( self::DB_VERSION, $version );
	}
}
