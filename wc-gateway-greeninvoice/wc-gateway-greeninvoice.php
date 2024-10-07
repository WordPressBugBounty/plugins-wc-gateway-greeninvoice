<?php
/**
 * Plugin Name: Morning for WooCommerce
 * Description: Accept payments from clients, with automated invoice production.
 * Version: 1.6.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 7.7
 * WC tested up to: 9.0.0
 * Author: Morning
 * Author URI: https://greeninvoice.co.il
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc-gateway-greeninvoice
 *
 * @package Morning\WC
 * @author  Dor Zuberi <admin@dorzki.io>
 * @version 1.6.0
 * @since   1.0.0
 */

use Morning\WC\Autoloader;
use Morning\WC\Compatibility;
use Morning\WC\Plugin;

defined( 'ABSPATH' ) || exit;


// Register autoloader.
require_once 'includes/class-autoloader.php';

try {
	( new Autoloader() )->register();
} catch ( Exception $e ) {
	die( esc_html( $e->getMessage() ) );
}


// Load deprecated code.
require_once 'includes/deprecated/constants.php';

// Define constants.
Plugin::define( 'MRN_WC_VERSION', '1.6.0' );
Plugin::define( 'MRN_WC_SLUG', 'greeninvoice' );
Plugin::define( 'MRN_WC_FILE', __FILE__ );
Plugin::define( 'MRN_WC_PATH', plugin_dir_path( __FILE__ ) );
Plugin::define( 'MRN_WC_URL', plugin_dir_url( __FILE__ ) );
Plugin::define( 'MRN_WC_DEFAULT_COUNTRY', 'IL' );
Plugin::define( 'MRN_API_BASE', null );

Plugin::define( 'MRN_REQUIRED_PHP', '7.4' );
Plugin::define( 'MRN_REQUIRED_WP', '6.4' );
Plugin::define( 'MRN_REQUIRED_WC', '7.7' );


/**
 * Check environment compatibility.
 *
 * @since 1.0.0
 */
function morning_wc_payment_gateway_init(): void {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$wc_version = get_plugins()['woocommerce/woocommerce.php']['Version'] ?? null;

	if ( ! Compatibility::is_version_compatible( PHP_VERSION, MRN_REQUIRED_PHP ) ) {
		add_action( 'admin_notices', '\Morning\WC\Compatibility::php_version' );
	} elseif ( ! Compatibility::is_version_compatible( get_bloginfo( 'version' ), MRN_REQUIRED_WP ) ) {
		add_action( 'admin_notices', '\Morning\WC\Compatibility::wordpress_version' );
	} elseif ( ! Compatibility::is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_action( 'admin_notices', '\Morning\WC\Compatibility::woocommerce_not_installed' );
	} elseif ( ! Compatibility::is_version_compatible( $wc_version, MRN_REQUIRED_WC ) ) {
		add_action( 'admin_notices', '\Morning\WC\Compatibility::woocommerce_version' );
	} else {
		Plugin::get_instance();
	}
}

add_action( 'plugin_loaded', 'morning_wc_payment_gateway_init' );


/**
 * Load plugin textdomain to support i18n.
 *
 * @since 1.0.0
 */
function morning_wc_load_plugin_textdomain(): void {
	load_plugin_textdomain( 'wc-gateway-greeninvoice' );
}

add_action( 'plugin_loaded', 'morning_wc_load_plugin_textdomain' );
