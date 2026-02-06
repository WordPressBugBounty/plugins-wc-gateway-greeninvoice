<?php
/**
 * Class Payment_Gateway_Manager
 *
 * @package    Morning\WC\Gateways
 * @subpackage Payment_Gateway_Manager
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.6
 * @since      2.0.0
 */

namespace Morning\WC\Gateways;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Gateways\Blocks\Apple_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\Bit_Gateway_Block;
use Morning\WC\Gateways\Blocks\Credit_Card_Gateway_Block;
use Morning\WC\Gateways\Blocks\Google_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\PayPal_Gateway_Block;
use Morning\WC\Utilities\IPN_Handler;
use WC_Payment_Gateway;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Gateway_Manager
 *
 * @package Morning\WC\Gateways
 */
class Payment_Gateway_Manager {
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;


	/**
	 * Payment_Gateway_Manager constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 2.0.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'woocommerce_blocks_payment_method_type_registration', [ $this, 'declare_payment_blocks' ] );

		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_payment_gateways' ] );
	}


	/**
	 * @param PaymentMethodRegistry $payment_method_registry
	 *
	 * @return void
	 *
	 * @throws Container_Exception
	 *
	 * @since 1.3.0
	 */
	public function declare_payment_blocks( PaymentMethodRegistry $payment_method_registry ): void {
		$options   = $this->settings->get_options();
		$container = mrn_get_container();

		if ( $options->is_payment_gateway_enabled( Payment_Type::CREDIT_CARD ) ) {
			$payment_method_registry->register( $container->get( Credit_Card_Gateway_Block::class ) );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::PAYPAL ) ) {
			$payment_method_registry->register( $container->get( PayPal_Gateway_Block::class ) );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::BIT ) ) {
			$payment_method_registry->register( $container->get( Bit_Gateway_Block::class ) );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::GOOGLE_PAY ) ) {
			$payment_method_registry->register( $container->get( Google_Pay_Gateway_Block::class ) );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::APPLE_PAY ) ) {
			$payment_method_registry->register( $container->get( Apple_Pay_Gateway_Block::class ) );
		}
	}


	/**
	 * @param WC_Payment_Gateway[] $registered_gateways Registered payment gateways.
	 *
	 * @return WC_Payment_Gateway[]
	 *
	 * @throws Container_Exception
	 *
	 * @since 1.0.0
	 */
	public function register_payment_gateways( array $registered_gateways ): array {
		$options   = $this->settings->get_options();
		$container = mrn_get_container();
		$gateways  = [];

		if ( $options->is_payment_gateway_enabled( Payment_Type::CREDIT_CARD ) ) {
			$gateways[] = $container->get( Credit_Card_Gateway::class );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::PAYPAL ) ) {
			$gateways[] = $container->get( PayPal_Gateway::class );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::BIT ) ) {
			$gateways[] = $container->get( Bit_Gateway::class );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::GOOGLE_PAY ) ) {
			$gateways[] = $container->get( Google_Pay_Gateway::class );
		}

		if ( $options->is_payment_gateway_enabled( Payment_Type::APPLE_PAY ) ) {
			$gateways[] = $container->get( Apple_Pay_Gateway::class );
		}

		if ( ! empty( $gateways ) ) {
			$container->get( IPN_Handler::class );
		}

		return array_merge( $registered_gateways, $gateways );
	}
}
