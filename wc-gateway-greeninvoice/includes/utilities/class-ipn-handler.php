<?php
/**
 * Class Base_Payment_Gateway
 *
 * @package    Morning\WC\Utilities
 * @subpackage IPN_Handler
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.6
 * @since      2.3.6
 */

namespace Morning\WC\Utilities;

use Throwable;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class IPN_Handler
 *
 * @package Morning\WC\Utilities
 */
class IPN_Handler {
	/**
	 * IPN_Handler constructor.
	 *
	 * @since 2.3.6
	 */
	public function __construct() {
		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.3.6
	 */
	protected function register_hooks(): void {
		add_action( 'woocommerce_api_wc_gateway_' . MRN_WC_SLUG, [ $this, 'check_response' ] );
	}


	/**
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function check_response(): void {
		/* phpcs:ignore */
		if ( ! empty( $_REQUEST ) ) {
			$order_id  = wc_clean( $_REQUEST[ 'order-id' ] ); /* phpcs:ignore */
			$order_key = wc_clean( $_REQUEST[ 'order-key' ] ); /* phpcs:ignore */
			$action    = wc_clean( $_REQUEST[ 'gi-type' ] ); /* phpcs:ignore */

			$order = wc_get_order( $order_id );

			if ( $order->key_is_valid( $order_key ) ) {
				switch ( $action ) {
					case 'success':
						$this->handle_success_response( $order );
						break;

					case 'failure':
						$this->handle_failure_response( $order );
						break;

					case 'ipn':
						$this->handle_ipn_response( $order );
						break;
				}
			}
		}

		exit;
	}


	/**
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function handle_success_response( WC_Order $order ): void {
		if ( ! $order->is_paid() ) {
			$order->update_status( 'on-hold', esc_html__( 'Payment received but awaiting confirmation.', 'wc-gateway-greeninvoice' ) );
		}

		$order->add_order_note(
			sprintf(
			/* translators: %s Morning Brand */
				__( '%s: Payment received.', 'wc-gateway-greeninvoice' ),
				'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
			)
		);

		Logger::info( "Order #{$order->get_id()} processed successfully." );

		$this->print_iframe_redirect( $order->get_checkout_order_received_url() );
	}

	/**
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function handle_failure_response( WC_Order $order ): void {
		if ( $order->is_paid() ) {
			Logger::info( "Order #{$order->get_id()} received a failure return but is already paid; keeping status." );

			$this->print_iframe_redirect( $order->get_checkout_order_received_url() );

			return;
		}

		$order->update_status( 'failed', esc_html__( 'Payment failed.', 'wc-gateway-greeninvoice' ) );
		$order->add_order_note(
			sprintf(
			/* translators: %s Morning Brand */
				__( '%s: Payment failed.', 'wc-gateway-greeninvoice' ),
				'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
			)
		);

		if ( ! empty( $_REQUEST['message'] ) ) {
			$message = esc_html( $_REQUEST['message'] );
		} else {
			$message = esc_html__( 'Payment failed, please try again.', 'wc-gateway-greeninvoice' );
		}

		Logger::info( "Order #{$order->get_id()} failed with error: {$message}" );

		$this->print_iframe_redirect( add_query_arg( 'mrn-wc-error', $message, $order->get_cancel_order_url() ) );
	}

	/**
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function handle_ipn_response( WC_Order $order ): void {
		Logger::info( "Order #{$order->get_id()} received an IPN." );

		$order->add_order_note(
			sprintf(
			/* translators: %s Morning Brand */
				__( '%s: IPN received.', 'wc-gateway-greeninvoice' ),
				'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
			)
		);

		parse_str( file_get_contents( 'php://input' ), $ipn_data );

		Logger::debug( 'Received IPN', $ipn_data );

		do_action( 'morning/wc/ipn_received', $ipn_data, $order );

		if ( ! $order->is_paid() ) {
			$order->payment_complete();
		}

		try {
			$order->set_transaction_id( $ipn_data['transaction_id'] );
		} catch ( Throwable $ex ) {
		}

		$order->add_meta_data( MRN_WC_SLUG . '_data', $ipn_data, true );
		$order->save();
	}


	/**
	 * @param string $url Redirect url.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	private function print_iframe_redirect( string $url ): void {
		/* phpcs:ignore */
		echo "<script type='text/javascript'>window.top.location.href = '{$url}';</script>";
	}
}
