<?php
/**
 * Class Plugin
 *
 * @package    Morning\WC
 * @subpackage Plugin
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.0
 * @since      1.0.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Morning\WC\Enum\Setting;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Gateways\Blocks\Apple_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\Bit_Gateway_Block;
use Morning\WC\Gateways\Blocks\Credit_Card_Gateway_Block;
use Morning\WC\Gateways\Blocks\Google_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\PayPal_Gateway_Block;
use Morning\WC\Integrations\Polylang_Integration;
use Morning\WC\Integrations\PW_Gift_Cards_Integration;
use Morning\WC\Integrations\Woo_Subscriptions_Integration;
use Morning\WC\Utilities\Settings;

defined( 'ABSPATH' ) || exit;


/**
 * Class Plugin
 *
 * @package Morning\WC
 */
final class Plugin {
	/**
	 * Plugin instance.
	 *
	 * @var null|Plugin
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;


	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_before_cart', [ $this, 'maybe_print_error' ] );

		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_payment_gateways' ] );
		add_filter( 'woocommerce_payment_complete_order_status', [ $this, 'change_ipn_order_status' ] );

		add_action( 'woocommerce_blocks_payment_method_type_registration', [ $this, 'declare_payment_blocks' ] );
		add_action( 'before_woocommerce_init', [ $this, 'declare_woocommerce_compatibility' ] );

		$this->init_classes();
		$this->check_license_activation();
	}


	/**
	 * Maybe print an error notice or cart page if gateway returned an error.
	 *
	 * @since 1.1.3
	 */
	public function maybe_print_error(): void {
		if ( empty( $_REQUEST['mrn-wc-error'] ) ) {
			return;
		}

		wc_print_notice( $_REQUEST['mrn-wc-error'], 'error' );
	}


	/**
	 * Register Morning payment gateways.
	 *
	 * @param array $methods Registered WooCommerce payment gateways.
	 *
	 * @return array
	 *
	 * @version 1.2.1
	 * @since 1.0.0
	 */
	public function register_payment_gateways( array $methods ): array {
		$options  = Settings::get_options();
		$gateways = $options[ Setting::GATEWAYS ] ?? [];

		if ( $this->is_gateway_active( $gateways, Payment_Type::CREDIT_CARD ) ) {
			$methods[] = '\Morning\WC\Gateways\Credit_Card_Gateway';
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::PAYPAL ) ) {
			$methods[] = '\Morning\WC\Gateways\PayPal_Gateway';
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::BIT ) ) {
			$methods[] = '\Morning\WC\Gateways\Bit_Gateway';
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::GOOGLE_PAY ) ) {
			$methods[] = '\Morning\WC\Gateways\Google_Pay_Gateway';
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::APPLE_PAY ) ) {
			$methods[] = '\Morning\WC\Gateways\Apple_Pay_Gateway';
		}

		return $methods;
	}

	/**
	 * Register Morning payment gateways blocks.
	 *
	 * @param PaymentMethodRegistry $payment_method_registry WooCommerce Payment Method Registry
	 *
	 * @return void
	 *
	 * @version 1.3.0
	 * @since 1.3.0
	 */
	public function declare_payment_blocks( PaymentMethodRegistry $payment_method_registry ): void {
		$options  = Settings::get_options();
		$gateways = $options[ Setting::GATEWAYS ] ?? [];

		if ( $this->is_gateway_active( $gateways, Payment_Type::CREDIT_CARD ) ) {
			$payment_method_registry->register( new Credit_Card_Gateway_Block() );
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::PAYPAL ) ) {
			$payment_method_registry->register( new PayPal_Gateway_Block() );
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::BIT ) ) {
			$payment_method_registry->register( new Bit_Gateway_Block() );
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::GOOGLE_PAY ) ) {
			$payment_method_registry->register( new Google_Pay_Gateway_Block() );
		}

		if ( $this->is_gateway_active( $gateways, Payment_Type::APPLE_PAY ) ) {
			$payment_method_registry->register( new Apple_Pay_Gateway_Block() );
		}
	}


	/**
	 * Register compatibility with several WooCommerce features:
	 * * HPOS - v1.2.3
	 * * Cart & Checkout Blocks - v1.3.0
	 *
	 * @return void
	 *
	 * @version 1.3.0
	 * @since 1.2.3
	 */
	public function declare_woocommerce_compatibility(): void {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', MRN_WC_FILE );
			FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', MRN_WC_FILE );
		}
	}


	/**
	 * Allow plugin to change order status to `completed`.
	 *
	 * @param string $order_status Default payment done order status.
	 *
	 * @return string
	 *
	 * @since 1.1.3
	 */
	public function change_ipn_order_status( string $order_status ): string {
		$options = Settings::get_options();

		$valid_statutes = [ 'processing', 'completed' ];

		if ( ! empty( $options[ Setting::ORDER_STATUS ] ) && in_array( $options[ Setting::ORDER_STATUS ], $valid_statutes, true ) ) {
			return $options[ Setting::ORDER_STATUS ];
		}

		return $order_status;
	}


	/**
	 * Init plugin classes.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	private function init_classes(): void {
		new Updater();
		new Settings();
		new Metabox();
		new Admin();
		new AJAX();
		new Frontend();
		new Checkout();

		if ( Compatibility::is_plugin_active( 'polylang-pro/polylang.php' ) ) {
			new Polylang_Integration();
		}

		if (
			Compatibility::is_plugin_active( 'pw-woocommerce-gift-cards/pw-gift-cards.php' ) ||
			Compatibility::is_plugin_active( 'pw-gift-cards/pw-gift-cards.php' )
		) {
			new PW_Gift_Cards_Integration();
		}

		if ( Compatibility::is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			new Woo_Subscriptions_Integration();
		}
	}

	/**
	 * Check if the user activated the license.
	 *
	 * @since 1.0.0
	 */
	public function check_license_activation(): void {
		$settings = Settings::get_options();

		if ( ! empty( $settings ) ) {
			if ( isset( $settings[ Setting::ACTIVATED ] ) && 'yes' === $settings[ Setting::ACTIVATED ] ) {
				remove_action( 'admin_notices', '\Morning\WC\Compatibility::needs_activation' );

				return;
			}
		}

		add_action( 'admin_notices', '\Morning\WC\Compatibility::needs_activation' );
	}


	/**
	 * Define a constant if not defined.
	 *
	 * @param string $name Constant name
	 * @param mixed $value Constant value
	 *
	 * @return void
	 *
	 * @since 1.5.0
	 */
	public static function define( string $name, $value ): void {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}


	/**
	 * Check if a payment gateway is active.
	 *
	 * @param array $options Gateways status.
	 * @param int $type Gateway type.
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	public function is_gateway_active( array $options, int $type ): bool {
		return ! empty( $options[ $type ] ) && '1' === $options[ $type ];
	}


	/**
	 * Bootstrap plugin.
	 *
	 * @return Plugin
	 *
	 * @since 1.0.0
	 */
	public static function get_instance(): Plugin {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Disable cloning of this object.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'wc-gateway-greeninvoice' ), '1.0.0' );
	}


	/**
	 * Disable unserializing this object.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing is forbidden.', 'wc-gateway-greeninvoice' ), '1.0.0' );
	}
}
