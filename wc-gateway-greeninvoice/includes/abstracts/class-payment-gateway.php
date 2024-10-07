<?php
/**
 * Class Payment_Gateway
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Payment_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC\Abstracts;

use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Setting;
use Morning\WC\Utilities\API;
use Morning\WC\Utilities\Logger;
use Morning\WC\Utilities\Settings;
use Throwable;
use WC_Payment_Gateway;
use WC_Order;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Gateway
 *
 * @package Morning\WC\Abstracts
 */
abstract class Payment_Gateway extends WC_Payment_Gateway {
	/**
	 * Payment gateway type.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	public $type = 0;

	/**
	 * Morning API instance.
	 *
	 * @var null|API
	 *
	 * @since 1.0.0
	 */
	public $api = null;

	/**
	 * Payment gateway currencies support.
	 *
	 * @var array
	 *
	 * @since 1.2.0
	 */
	protected $currencies = [
		Currency::AUD,
		Currency::BRL,
		Currency::CAD,
		Currency::CHF,
		Currency::CNY,
		Currency::CZK,
		Currency::DKK,
		Currency::EUR,
		Currency::GBP,
		Currency::HKD,
		Currency::HRK,
		Currency::HUF,
		Currency::IDR,
		Currency::ILS,
		Currency::INR,
		Currency::JPY,
		Currency::KRW,
		Currency::MXN,
		Currency::NOK,
		Currency::NZD,
		Currency::PLN,
		Currency::RON,
		Currency::RUB,
		Currency::SEK,
		Currency::SGD,
		Currency::THB,
		Currency::TRY,
		Currency::USD,
		Currency::ZAR,
	];

	/**
	 * Payment gateway capabilities.
	 *
	 * @var string[]
	 *
	 * @since 1.2.0
	 */
	public $capabilities = [];


	/**
	 * Payment_Gateway constructor.
	 *
	 * @param bool $init_hooks Should register hooks?
	 *
	 * @version 1.5.0
	 * @since 1.0.0
	 */
	public function __construct( bool $init_hooks = true ) {
		$this->has_fields = true;

		$this->supports[] = 'products';
		$this->supports[] = 'refunds';

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );

		$this->view_transaction_url = Settings::is_enabled( Setting::SANDBOX ) ?
			'https://app.sandbox.d.greeninvoice.co.il/incomes/transactions/%s' :
			'https://app.greeninvoice.co.il/incomes/transactions/%s';

		if ( ! $this->supports_currency() ) {
			$this->enabled = 'no';
		}

		if ( $this->is_capable_of( Capability::INSTALLMENTS ) ) {
			$this->form_fields['installments'] = [
				'title'       => __( 'Max Number of Installments ', 'wc-gateway-greeninvoice' ),
				'type'        => 'select',
				'description' => __( 'Maximum number of installments available for the customer (leave 1 for no installments).', 'wc-gateway-greeninvoice' ),
				'default'     => 1,
				'desc_tip'    => true,
				'options'     => array_combine( range( 1, 12 ), range( 1, 12 ) ),
			];
		}

		$this->api = API::get_instance();

		if ( $init_hooks ) {
			$this->init_hooks();
		}
	}


	/**
	 * Init Payment gateway hooks.
	 *
	 * @return void
	 *
	 * @version 1.3.0
	 * @since 1.3.0
	 */
	protected function init_hooks(): void {
		do_action( 'morning/wc/gateway_init', $this );

		if ( $this->is_capable_of( Capability::IFRAME_FORM ) ) {
			add_action( "woocommerce_receipt_{$this->id}", [ $this, 'receipt_page' ] );
		}

		// @phpstan-ignore-next-line
		add_action( "woocommerce_update_options_payment_gateways_{$this->id}", [ $this, 'process_admin_options' ] );
		add_action( 'woocommerce_api_wc_gateway_' . MRN_WC_SLUG, [ $this, 'check_ipn_response' ] );
	}


	/**
	 * Define gateway settings fields.
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields(): void {
		$this->form_fields = [
			'enabled'     => [
				'title'   => __( 'Enable/Disable', 'wc-gateway-greeninvoice' ),
				'type'    => 'checkbox',
				/* translators: %s Gateway Name */
				'label'   => sprintf( __( 'Enable %s', 'wc-gateway-greeninvoice' ), esc_html( $this->method_title ) ),
				'default' => 'yes',
			],
			'title'       => [
				'title'       => __( 'Title', 'wc-gateway-greeninvoice' ),
				'type'        => 'text',
				'description' => __( 'The title the customers will see at the checkout page.', 'wc-gateway-greeninvoice' ),
				/* translators: %s Gateway Name */
				'default'     => sprintf( __( 'Pay with %s', 'wc-gateway-greeninvoice' ), esc_html( $this->method_title ) ),
				'desc_tip'    => true,
			],
			'description' => [
				'title'       => __( 'Description (optional)', 'wc-gateway-greeninvoice' ),
				'type'        => 'textarea',
				'description' => __( 'The description will appear when selecting the payment method at the checkout page.', 'wc-gateway-greeninvoice' ),
				'default'     => '',
				'desc_tip'    => true,
			],
		];
	}

	/**
	 * Check if the currency is supported.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function supports_currency(): bool {
		$supported_currencies = apply_filters( "morning/wc/supported_currencies_{$this->id}", $this->currencies );

		return in_array( get_woocommerce_currency(), $supported_currencies, true );
	}

	/**
	 * Check if the capability is supported.
	 *
	 * @param string $capability Asked capability.
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	public function is_capable_of( string $capability ): bool {
		$supported_capabilities = apply_filters( "morning/wc/supported_capabilities_{$this->id}", $this->capabilities );

		return in_array( $capability, $supported_capabilities, true );
	}

	/**
	 * Checks whether this order can be refunded.
	 *
	 * @param WC_Order $order Order to refund.
	 *
	 * @return bool
	 *
	 * @since 1.5.0
	 */
	public function can_refund_order( $order ): bool {
		return parent::can_refund_order( $order ) && ! empty( $order->get_transaction_id() ) && empty( $order->get_meta( Setting::ORDER_REFUND_META ) );
	}

	/**
	 * Does the plugin needs setup?
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function needs_setup(): bool {
		$options = Settings::get_options();

		return ! ( 'yes' === $options[ Setting::ACTIVATED ] );
	}

	/**
	 * Generate gateway url for IPN/Success/Failure.
	 *
	 * @param string $type Url type (ipn,success,failure).
	 * @param WC_Order $order Order object.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public static function get_gateway_url( string $type, WC_Order $order ): string {
		$params = apply_filters(
			'morning/wc/get_gateway_url_params',
			[
				'wc-api'    => 'WC_Gateway_GreenInvoice',
				'gi-type'   => $type,
				'order-id'  => $order->get_id(),
				'order-key' => $order->get_order_key(),
			]
		);

		$site_url = defined( 'MRN_WEBHOOK_URI' ) ? trailingslashit( MRN_WEBHOOK_URI ) : home_url( '/' );

		return add_query_arg( $params, $site_url );
	}


	/**
	 * Process payment request via gateway.
	 *
	 * @param int $order_id Current order id.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );

		if ( $this->is_capable_of( Capability::IFRAME_FORM ) ) {
			return [
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			];
		}

		$payment_url = $this->api->request_payment_url( $this->type, $order );

		if ( is_wp_error( $payment_url ) ) {
			return [
				'result'  => 'error',
				'message' => $payment_url->get_error_message(),
			];
		}

		return [
			'result'   => 'success',
			'redirect' => $payment_url,
		];
	}

	/**
	 * Process partial or full refund via gateway.
	 *
	 * @param int $order_id Order ID.
	 * @param float|null $amount Refund amount.
	 * @param string $reason Refund reason.
	 *
	 * @return bool|WP_Error True or false based on success, or a WP_Error object.
	 *
	 * @since 1.5.0
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order     = wc_get_order( $order_id );
		$paid_date = $order->get_date_paid();

		if ( ! $paid_date ) {
			return new WP_Error( 'morning-api', _x( 'Unable to refund an unpaid order.', 'Refund Order Errors', 'wc-gateway-greeninvoice' ) );
		}

		if ( $paid_date->format( 'Y-m-d' ) === gmdate( 'Y-m-d' ) && $order->get_total() > $amount ) {
			return new WP_Error( 'morning-api', _x( 'Unable to partially refund an order that was made today, please issue a full refund', 'Refund Order Errors', 'wc-gateway-greeninvoice' ) );
		}

		$refund_meta = $this->api->request_transaction_refund( $order, $amount, $reason );

		if ( is_wp_error( $refund_meta ) ) {
			return $refund_meta;
		}

		$order->add_meta_data( Setting::ORDER_REFUND_META, (array) $refund_meta, true );
		$order->add_order_note(
			sprintf(
			/* translators: %s Refund Amount */
				__( 'A refund of %s was made via payment gateway.', 'wc-gateway-greeninvoice' ),
				wc_price( $amount, [ 'currency' => $order->get_currency() ] )
			)
		);
		$order->save();

		return true;
	}

	/**
	 * Process scheduled payment with token.
	 *
	 * @param int $amount Amount to charge.
	 * @param WC_Order $order Order ID.
	 *
	 * @since 1.6.0
	 */
	public function process_scheduled_payment( int $amount, WC_Order $order ): void {
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $order );

		if ( empty( $subscriptions ) ) {
			$order->set_status( 'failed', 'Unable to process subscription.' );
			$order->save();

			return;
		}

		/** @var WC_Order $subscription */
		$subscription = array_pop( $subscriptions );
		$token_id     = $subscription->get_meta( Setting::SUBSCRIPTION_TOKEN_ID );

		$response = $this->api->request_token_charge( $token_id, $this->type, $order );

		if ( is_wp_error( $response ) ) {
			$order->set_status( 'failed', $response->get_error_message() );
			$order->save();

			return;
		}

		$order->set_status( 'on-hold', 'Payment received' );
		$order->save();
	}


	/**
	 * Display iframe payment gateway iframe.
	 *
	 * @param int $order_id Current order id.
	 *
	 * @since 1.0.0
	 */
	public function receipt_page( int $order_id ): void {
		$order        = wc_get_order( $order_id );
		$installments = empty( $this->settings['installments'] ) ? 1 : intval( $this->settings['installments'] );

		$is_subscription = function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order_id );

		if ( 1 === $installments || ! empty( $_POST[ MRN_WC_SLUG . '_installments' ] ) || $is_subscription ) {
			$installments = ( empty( $_POST[ MRN_WC_SLUG . '_installments' ] ) ) ? 1 : intval( $_POST[ MRN_WC_SLUG . '_installments' ] );

			if ( $is_subscription ) {
				$payment_url = $this->api->request_payment_token_url( $this->type, $order );
			} else {
				$payment_url = $this->api->request_payment_url( $this->type, $order, $installments );
			}

			if ( is_wp_error( $payment_url ) ) {
				echo esc_html( $payment_url->get_error_message() );

				return;
			}

			$additional_atts = apply_filters( "morning/wc/{$this->id}_payment_form_atts", '' );

			// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div id="gi_wrapper mrn_wrapper" class="greeninvoice-payment-wrapper morning-payment-wrapper">';
			echo "	<iframe src='{$payment_url}' class='greeninvoice-payment-iframe morning-payment-iframe'{$additional_atts}></iframe>";
			echo '</div>';
			// @phpcs:enable
		} else {
			include_once MRN_WC_PATH . '/templates/frontend/installments-form.php';
		}
	}

	/**
	 * Receive and handle IPN response.
	 *
	 * @since 1.0.0
	 */
	public function check_ipn_response(): void {
		/* phpcs:ignore */
		if ( ! empty( $_REQUEST ) ) {
			$order_id  = wc_clean( $_REQUEST['order-id'] ); /* phpcs:ignore */
			$order_key = wc_clean( $_REQUEST['order-key'] ); /* phpcs:ignore */
			$action    = wc_clean( $_REQUEST['gi-type'] ); /* phpcs:ignore */

			$order = wc_get_order( $order_id );

			if ( ! empty( $order->get_meta( Setting::ORDER_META ) ) || $order->is_paid() ) {
				Logger::get_instance()->log( "Skipping update for Order #{$order->get_id()}.", 'info' );

				return;
			}

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
	 * Handle success response.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function handle_success_response( WC_Order $order ): void {
		$order->update_status( 'on-hold', esc_html__( 'Payment received but awaiting confirmation.', 'wc-gateway-greeninvoice' ) );

		Logger::get_instance()->log( "Order #{$order->get_id()} processed successfully.", 'info' );

		/* phpcs:ignore */
		echo "<script type='text/javascript'>window.top.location.href = '" . $order->get_checkout_order_received_url() . "';</script>";
	}

	/**
	 * Handle failure response.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function handle_failure_response( WC_Order $order ): void {
		$order->update_status( 'failed', esc_html__( 'Payment failed.', 'wc-gateway-greeninvoice' ) );

		if ( ! empty( $_REQUEST['message'] ) ) {
			$message = esc_html( $_REQUEST['message'] );
		} else {
			$message = esc_html__( 'Payment failed, please try again.', 'wc-gateway-greeninvoice' );
		}

		Logger::get_instance()->log( "Order #{$order->get_id()} failed with error: {$message}", 'info' );

		$failure_url = add_query_arg( 'mrn-wc-error', $message, $order->get_cancel_order_url() );

		/* phpcs:ignore */
		echo "<script type='text/javascript'>window.top.location.href = '" . $failure_url . "';</script>";
	}

	/**
	 * Handle IPN response.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function handle_ipn_response( WC_Order $order ): void {
		Logger::get_instance()->log( "Order #{$order->get_id()} received an IPN.", 'info' );

		$order->add_order_note( __( 'IPN payment completed.', 'wc-gateway-greeninvoice' ) );
		$order->payment_complete();

		parse_str( file_get_contents( 'php://input' ), $ipn_data );

		Logger::get_instance()->log( [ 'IPN Received' => $ipn_data ], 'debug' );

		try {
			$order->set_transaction_id( $ipn_data['transaction_id'] );
		} catch ( Throwable $ex ) {
		}

		$order->add_meta_data( Setting::ORDER_META, $ipn_data, true );

		if ( ! empty( $ipn_data['token_id'] ) ) {
			$order->add_meta_data( Setting::SUBSCRIPTION_TOKEN_ID, $ipn_data['token_id'], true );

			$subscriptions = wcs_get_subscriptions_for_order( $order );
			foreach ( $subscriptions as $subscription ) {
				$subscription->add_meta_data( Setting::SUBSCRIPTION_TOKEN_ID, $ipn_data['token_id'], true );
				$subscription->save();
			}
		}

		$order->save();
	}
}
