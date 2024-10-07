<?php
/**
 * Class Woo_Subscriptions_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage Woo_Subscriptions_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.0
 * @since      1.6.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Capability;
use Morning\WC\Utilities\Logger;

defined( 'ABSPATH' ) || exit;


/**
 * Class Woo_Subscriptions_Integration
 *
 * @package Morning\WC\Integrations
 */
class Woo_Subscriptions_Integration {
	/**
	 * Woo_Subscriptions_Integration constructor.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {
		add_action( 'morning/wc/gateway_init', [ $this, 'declare_features_support' ] );
	}


	/**
	 * Register WooCommerce Subscriptions payment gateway support.
	 *
	 * @param Payment_Gateway $gateway
	 *
	 * @return void
	 *
	 * @since 1.6.0
	 */
	public function declare_features_support( Payment_Gateway $gateway ): void {
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

		add_action( "woocommerce_scheduled_subscription_payment_{$gateway->id}", [ $gateway, 'process_scheduled_payment' ], 10, 2 );
	}
}
