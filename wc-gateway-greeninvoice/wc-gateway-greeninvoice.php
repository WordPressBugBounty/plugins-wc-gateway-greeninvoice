<?php
/**
 * Plugin Name: Morning for WooCommerce
 * Description: Accept payments from clients with automated invoice production.
 * Version: 2.2.1
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 8.9.0
 * WC tested up to: 10.1.2
 * Author: Morning
 * Author URI: https://www.greeninvoice.co.il
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc-gateway-greeninvoice
 *
 * @package Morning\WC
 * @author  Dor Zuberi <admin@dorzki.io>
 * @version 2.2.1
 * @since   1.0.0
 */

use Morning\WC\Autoloader;
use Morning\WC\Exceptions\Container_Exception;

defined( 'ABSPATH' ) || exit;


// Declare constants.
const MRN_WC_VERSION = '2.2.1';
const MRN_WC_SLUG    = 'greeninvoice';
const MRN_WC_FILE    = __FILE__;

define( 'MRN_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'MRN_WC_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'MRN_WC_DEFAULT_COUNTRY' ) ) {
	define( 'MRN_WC_DEFAULT_COUNTRY', 'IL' );
}

if ( ! defined( 'MRN_WEBHOOK_URI' ) ) {
	define( 'MRN_WEBHOOK_URI', null );
}


// Register autoloader.
require_once 'includes/class-autoloader.php';

try {
	( new Autoloader() )->register();
} catch ( Exception $e ) {
	die( esc_html( $e->getMessage() ) );
}


// Initiate container.
$GLOBALS['mrn_container'] = new Morning\WC\Container();

/**
 * @return \Morning\WC\Container
 *
 * @since 2.0.0
 */
function mrn_get_container(): Morning\WC\Container {
	return $GLOBALS['mrn_container'];
}


// Init plugin.
try {
	mrn_get_container()->get_plugin();
} catch ( Container_Exception $e ) {
}
