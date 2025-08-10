<?php
/**
 * Class Plugin
 *
 * @package    Morning\WC
 * @subpackage Plugin
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.2.0
 * @since      1.0.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Morning\WC\Config\Settings;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Gateways\Payment_Gateway_Manager;
use Morning\WC\Integrations\Integration_Manager;
use Morning\WC\Utilities\Auth;

defined( 'ABSPATH' ) || exit;


/**
 * Class Plugin
 *
 * @package Morning\WC
 */
final class Plugin {
	/**
	 * @var Compatibility
	 *
	 * @since 2.0.0
	 */
	private Compatibility $compatibility;
	/**
	 * @var Admin
	 *
	 * @since 2.0.0
	 */
	private Admin $admin;
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;


	/**
	 * Plugin constructor.
	 *
	 * @param Compatibility $compatibility
	 * @param Admin $admin
	 * @param Settings $settings
	 *
	 * @throws Container_Exception
	 *
	 * @since 1.0.0
	 */
	public function __construct( Compatibility $compatibility, Admin $admin, Settings $settings ) {
		$this->compatibility = $compatibility;
		$this->admin         = $admin;
		$this->settings      = $settings;

		if ( $this->compatibility->is_compatible() ) {
			$this->register_hooks();
		}
	}

	/**
	 * @return void
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	private function register_hooks() {
		add_action( 'init', [ $this, 'load_textdomain' ] );

		add_action( 'before_woocommerce_init', [ $this, 'declare_compatibilities' ] );

		add_filter( 'woocommerce_payment_complete_order_status', [ $this, 'change_ipn_order_status' ] );

		$options = $this->settings->get_options();
		if ( $options->is_sandbox_mode() ) {
			add_action( 'admin_notices', [ $this, 'sandbox_mode_enabled' ] );
		}

		// Load crucial classes.
		$container = mrn_get_container();
		$container->get( Updater::class );
		$container->get( Frontend::class );
		$container->get( Auth::class );
		$container->get( Ajax::class );
		$container->get( Checkout::class );
		$container->get( Integration_Manager::class );

		if ( $options->is_invoicing_mode() ) {
			$container->get( Invoicing::class );
		}

		if ( $options->is_clearing_mode() || $options->is_basic_mode() ) {
			$container->get( Payment_Gateway_Manager::class );
		}
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wc-gateway-greeninvoice' );
	}

	/**
	 * Added on:
	 * * High Performance Order Storage (HPOS) - v1.2.3
	 * * Cart & Checkout Blocks - v1.3.0
	 *
	 * @return void
	 *
	 * @since 1.2.3
	 */
	public function declare_compatibilities(): void {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', MRN_WC_FILE );
		FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', MRN_WC_FILE );
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function sandbox_mode_enabled(): void {
		/* translators: %s Settings Page */
		$notice = sprintf( __( 'Attention! Sandbox mode is enabled for Morning for WooCommerce. You can disable it from the %s.', 'wc-gateway-greeninvoice' ), '<a href="admin.php?page=greeninvoice">' . __( 'settings page', 'wc-gateway-greeninvoice' ) . '</a>' );

		$this->admin->print_notice( $notice );
	}


	/**
	 * @param string $order_status Default payment done order status.
	 *
	 * @return string
	 *
	 * @since 1.1.3
	 */
	public function change_ipn_order_status( string $order_status ): string {
		$options = $this->settings->get_options();

		if ( $options->is_invoicing_mode() ) {
			return $order_status;
		}

		if ( in_array( $options->get_order_status(), [ 'processing', 'completed' ], true ) ) {
			return $options->get_order_status();
		}

		return $order_status;
	}
}
