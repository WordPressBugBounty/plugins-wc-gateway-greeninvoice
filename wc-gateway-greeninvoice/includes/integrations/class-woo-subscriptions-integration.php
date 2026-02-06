<?php
/**
 * Class Woo_Subscriptions_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage Woo_Subscriptions_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.6
 * @since      1.6.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Base\Base_Integration;
use Morning\WC\Base\Base_Payment_Gateway;
use Morning\WC\Enum\Capability;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class Woo_Subscriptions_Integration
 *
 * @package Morning\WC\Integrations
 */
final class Woo_Subscriptions_Integration extends Base_Integration {
	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function register_hooks(): void {
		parent::register_hooks();

		add_action( 'morning/wc/gateway_init', [ $this, 'declare_features_support' ] );
		add_action( 'morning/wc/ipn_received', [ $this, 'update_subscription_with_token' ], 10, 2 );

		add_filter( 'morning/wc/order_requires_token', [ $this, 'check_order_items' ], 10, 2 );
	}


	/**
	 * @param Base_Payment_Gateway $gateway Current payment gateway.
	 *
	 * @return void
	 *
	 * @since 1.6.0
	 */
	public function declare_features_support( Base_Payment_Gateway $gateway ) {
		if ( ! $gateway->is_capable_of( Capability::TOKENIZATION ) ) {
			return;
		}

		$gateway->supports[] = 'tokenization';
		$gateway->supports[] = 'subscriptions';
		$gateway->supports[] = 'subscription_cancellation';
		$gateway->supports[] = 'subscription_suspension';
		$gateway->supports[] = 'subscription_reactivation';
		$gateway->supports[] = 'subscription_amount_changes';
		$gateway->supports[] = 'subscription_date_changes';
		$gateway->supports[] = 'multiple_subscriptions';
		$gateway->supports[] = 'subscription_payment_method_change_customer';

		add_action(
			"woocommerce_scheduled_subscription_payment_{$gateway->id}",
			[ $gateway, 'process_scheduled_payment' ],
			10,
			2
		);

		add_action(
			"woocommerce_subscription_failing_payment_method_updated_{$gateway->id}",
			[ $gateway, 'process_payment_method_updated' ],
			10,
			2
		);
	}

	/**
	 * @param array $ipn_data IPN data.
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function update_subscription_with_token( array $ipn_data, WC_Order $order ): void {
		if ( empty( $ipn_data['token_id'] ) ) {
			return;
		}

		$order->add_meta_data( MRN_WC_SLUG . '_subscription_token_id', $ipn_data['token_id'], true );
		$order->save();

		$subscriptions = wcs_get_subscriptions_for_order( $order );
		foreach ( $subscriptions as $subscription ) {
			$subscription->add_order_note(
				sprintf(
				/* translators: %s Morning Brand */
					__( '%s: Credit card token replaced.', 'wc-gateway-greeninvoice' ),
					'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
				)
			);

			$subscription->add_meta_data( MRN_WC_SLUG . '_subscription_token_id', $ipn_data['token_id'], true );
			$subscription->save();
		}
	}


	/**
	 * @param bool $requires_token Does the order need tokenization?
	 * @param WC_Order $order Order object.
	 *
	 * @return bool
	 *
	 * @since 2.3.6
	 */
	public function check_order_items( bool $requires_token, WC_Order $order ): bool {
		return (
			function_exists( 'wcs_order_contains_subscription' )
			&& function_exists( 'wcs_is_subscription' )
			&& function_exists( 'wcs_order_contains_renewal' )
			&& ( wcs_order_contains_subscription( $order ) || wcs_is_subscription( $order ) || wcs_order_contains_renewal( $order ) )
		);
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_compatible(): bool {
		return $this->is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' );
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function get_integration_name(): string {
		return 'WooCommerce Subscriptions';
	}
}
