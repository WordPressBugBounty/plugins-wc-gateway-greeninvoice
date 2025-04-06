<?php
/**
 * Class Woo_Subscriptions_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage Woo_Subscriptions_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.6.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Base\Base_Integration;
use Morning\WC\Base\Base_Payment_Gateway;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Capability;

defined( 'ABSPATH' ) || exit;


/**
 * Class Woo_Subscriptions_Integration
 *
 * @package Morning\WC\Integrations
 */
final class Woo_Subscriptions_Integration extends Base_Integration {
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;


	/**
	 * Woo_Subscriptions_Integration constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 2.0.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		parent::__construct();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function register_hooks(): void {
		parent::register_hooks();

		add_action( 'morning/wc/gateway_init', [ $this, 'declare_features_support' ] );
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

		add_action(
			"woocommerce_scheduled_subscription_payment_{$gateway->id}",
			[ $gateway, 'process_scheduled_payment' ],
			10,
			2
		);
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_compatible(): bool {
		$options = $this->settings->get_options();

		return $this->is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) && ! $options->is_basic_mode();
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
