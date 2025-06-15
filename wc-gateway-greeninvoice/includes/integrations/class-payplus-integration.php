<?php
/**
 * Class PayPlus_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage PayPlus_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.4
 * @since      2.0.4
 */

namespace Morning\WC\Integrations;

use Morning\WC\Base\Base_Integration;
use Morning\WC\Enum\Request_Flow;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class PayPlus_Integration
 *
 * @package Morning\WC\Integrations
 */
class PayPlus_Integration extends Base_Integration {
	/**
	 * @return void
	 *
	 * @since 2.0.4
	 */
	protected function register_hooks(): void {
		parent::register_hooks();

		add_filter( 'morning/wc/order_invoice_params', [ $this, 'inject_payplus_params' ], 10, 4 );
	}


	/**
	 * @param array $doc Document parameters.
	 * @param WC_Order $order Current order.
	 * @param int|null $payment_method Payment method.
	 * @param int $flow Request flow.
	 *
	 * @return array
	 *
	 * @since 2.0.4
	 */
	public function inject_payplus_params( array $doc, WC_Order $order, ?int $payment_method, int $flow ): array {
		if ( Request_Flow::CREATE_DOCUMENT !== $flow ) {
			return $doc;
		}

		if ( false === strpos( $order->get_payment_method(), 'payplus' ) ) {
			return $doc;
		}

		$payplus_type = $order->get_meta( 'payplus_method' );

		if ( 'credit-card' !== $payplus_type ) {
			return $doc;
		}

		$doc['cardNumber']   = $order->get_meta( 'payplus_four_digits' ) ?? null;
		$doc['installments'] = $order->get_meta( 'payplus_number_of_payments' ) ?? null;

		return $doc;
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.4
	 */
	protected function is_compatible(): bool {
		return class_exists( 'WC_PayPlus_Gateway' );
	}

	/**
	 * @return string
	 *
	 * @since 2.0.4
	 */
	protected function get_integration_name(): string {
		return 'PayPlus';
	}
}
