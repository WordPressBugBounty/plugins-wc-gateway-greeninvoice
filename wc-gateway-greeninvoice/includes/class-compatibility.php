<?php
/**
 * Class Compatibility
 *
 * @package    Morning\WC
 * @subpackage Compatibility
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.6
 * @since      1.0.0
 */

namespace Morning\WC;

defined( 'ABSPATH' ) || exit;


/**
 * Class Compatibility
 *
 * @package Morning\WC
 */
class Compatibility {
	/**
	 * @var Admin
	 *
	 * @since 2.0.0
	 */
	private Admin $admin;

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const MIN_PHP = '7.4';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const MIN_WP = '6.7';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const MIN_WC = '8.0';


	/**
	 * Compatibility constructor.
	 *
	 * @param Admin $admin
	 *
	 * @since 2.0.0
	 */
	public function __construct( Admin $admin ) {
		$this->admin = $admin;
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_compatible(): bool {
		if ( ! $this->is_version_compatible( PHP_VERSION, self::MIN_PHP ) ) {
			add_action( 'admin_notices', [ $this, 'incompatible_php_version' ] );

			return false;
		}

		if ( ! $this->is_version_compatible( get_bloginfo( 'version' ), self::MIN_WP ) ) {
			add_action( 'admin_notices', [ $this, 'incompatible_wordpress_version' ] );

			return false;
		}

		if ( ! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', [ $this, 'woocommerce_not_active' ] );

			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$wc_version = get_plugins()['woocommerce/woocommerce.php']['Version'] ?? null;

		if ( ! $this->is_version_compatible( $wc_version, self::MIN_WC ) ) {
			add_action( 'admin_notices', [ $this, 'incompatible_woocommerce_version' ] );
		}

		return true;
	}


	/**
	 * @param string $existing Existing dependency version
	 * @param string $required Required dependency version
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_version_compatible( string $existing, string $required ): bool {
		return version_compare( $existing, $required, '>=' );
	}

	/**
	 * @param string $plugin Plugin directory & file.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_plugin_active( string $plugin ): bool {
		return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
	}


	/**
	 * @since 2.0.0
	 */
	public function incompatible_php_version(): void {
		/* translators: %s PHP Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires PHP version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), self::MIN_PHP );

		$this->admin->print_notice( $notice );
	}

	/**
	 * @since 2.0.0
	 */
	public function incompatible_wordpress_version(): void {
		/* translators: %s WordPress Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires WordPress version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), self::MIN_WP );

		$this->admin->print_notice( $notice );
	}

	/**
	 * @since 1.0.0
	 */
	public function woocommerce_not_active(): void {
		$notice = esc_html__( 'Morning for WooCommerce requires WooCommerce to be installed and active.', 'wc-gateway-greeninvoice' );

		$this->admin->print_notice( $notice );
	}

	/**
	 * Print WooCommerce version incompatibility notice.
	 *
	 * @since 2.0.0
	 */
	public function incompatible_woocommerce_version(): void {
		/* translators: %s WooCommerce Version */
		$notice = sprintf( esc_html__( 'Morning for WooCommerce requires WooCommerce version %s or higher to run properly.', 'wc-gateway-greeninvoice' ), self::MIN_WC );

		$this->admin->print_notice( $notice );
	}
}
