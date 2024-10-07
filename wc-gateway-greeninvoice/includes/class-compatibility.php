<?php
/**
 * Class Compatibility
 *
 * @package    Morning\WC
 * @subpackage Compatibility
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.1
 * @since      1.0.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

defined( 'ABSPATH' ) || exit;


/**
 * Class Compatibility
 *
 * @package Morning\WC
 */
class Compatibility {
	/**
	 * Checks whether a desired plugin is installed & active.
	 *
	 * @param string $plugin Plugin directory & file.
	 *
	 * @return bool
	 *
	 * @since 1.4.0
	 */
	public static function is_plugin_active( string $plugin ): bool {
		return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
	}

	/**
	 * Checks whether HPOS feature is in use.
	 *
	 * @return bool
	 *
	 * @since 1.4.1
	 */
	public static function is_wc_hpos_active(): bool {
		return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled();
	}

	/**
	 * Check whether we have a compatible version of dependency.
	 *
	 * @param string|null $existing Existing dependency version
	 * @param string $required Required dependency version
	 *
	 * @return bool
	 *
	 * @since 1.5.0
	 */
	public static function is_version_compatible( ?string $existing, string $required ): bool {
		return version_compare( $existing, $required, '>=' );
	}


	/**
	 * Print PHP version incompatibility notice.
	 *
	 * @since 1.0.0
	 */
	public static function php_version(): void {
		/* translators: %s PHP Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires PHP version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), MRN_REQUIRED_PHP );

		self::admin_notice( $notice );
	}

	/**
	 * Print WordPress version incompatibility notice.
	 *
	 * @since 1.0.0
	 */
	public static function wordpress_version(): void {
		/* translators: %s WordPress Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires WordPress version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), MRN_REQUIRED_WP );

		self::admin_notice( $notice );
	}

	/**
	 * Print WooCommerce not installed notice.
	 *
	 * @since 1.0.0
	 */
	public static function woocommerce_not_installed(): void {
		$notice = esc_html__( 'Morning for WooCommerce requires WooCommerce to be installed and active.', 'wc-gateway-greeninvoice' );

		self::admin_notice( $notice );
	}

	/**
	 * Print WooCommerce version incompatibility notice.
	 *
	 * @since 1.4.2
	 */
	public static function woocommerce_version(): void {
		/* translators: %s WooCommerce Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires WooCommerce version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), MRN_REQUIRED_WC );

		self::admin_notice( $notice );
	}

	/**
	 * Print needs activation message.
	 *
	 * @since 1.0.0
	 */
	public static function needs_activation(): void {
		/* translators: %s Plugin Name */
		$notice = sprintf( __( 'Please activate the plugin by entering your license key for %s.', 'wc-gateway-greeninvoice' ), '<a href="admin.php?page=greeninvoice">' . __( 'Morning for WooCommerce', 'wc-gateway-greeninvoice' ) . '</a>' );

		self::admin_notice( $notice );
	}

	/**
	 * Print sandbox active message.
	 *
	 * @since 1.0.0
	 */
	public static function sandbox_active(): void {
		/* translators: %s Settings Page */
		$notice = sprintf( __( 'Attention! Sandbox mode is enabled for Morning for WooCommerce. You can disable it from the %s.', 'wc-gateway-greeninvoice' ), '<a href="admin.php?page=greeninvoice">' . __( 'settings page', 'wc-gateway-greeninvoice' ) . '</a>' );

		self::admin_notice( $notice );
	}


	/**
	 * Print admin notice.
	 *
	 * @param string $notice Notice to display.
	 *
	 * @since 1.0.0
	 */
	public static function admin_notice( string $notice ): void {
		$notice = wpautop( $notice );

		echo wp_kses_post( "<div class='morning-notice error'>{$notice}</div>" );
	}
}
