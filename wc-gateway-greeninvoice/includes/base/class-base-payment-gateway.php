<?php
/**
 * Class Base_Payment_Gateway
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Base_Payment_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.6
 * @since      1.0.0
 */

namespace Morning\WC\Base;

use Morning\WC\Config\Options;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Utilities\Api;
use WC_Order;
use WC_Payment_Gateway;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Payment_Gateway
 *
 * @package Morning\WC\Abstracts
 */
abstract class Base_Payment_Gateway extends WC_Payment_Gateway {
	/**
	 * @var int
	 *
	 * @since 1.0.0
	 */
	public int $type = 0;
	/**
	 * @var Api
	 *
	 * @since 1.0.0
	 */
	public Api $api;
	/**
	 * @var Options
	 *
	 * @since 2.0.0
	 */
	private Options $options;
	/**
	 * @var array
	 *
	 * @since 1.2.0
	 */
	protected array $currencies = [
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
	 * @var string[]
	 *
	 * @since 1.2.0
	 */
	public array $capabilities = [];


	/**
	 * Base_Payment_Gateway constructor.
	 *
	 * @param Api $api
	 * @param Settings $settings
	 *
	 * @since 1.0.0
	 */
	public function __construct( Api $api, Settings $settings ) {
		$this->api     = $api;
		$this->options = $settings->get_options();

		$this->supports[] = 'refunds';

		$this->init_form_fields();
		$this->init_settings();

		$this->icon        = apply_filters( "morning/wc/{$this->id}_gateway_icon", '' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );

		$this->view_transaction_url = $this->options->is_sandbox_mode() ?
			'https://app.sandbox.d.greeninvoice.co.il/incomes/transactions/%s' :
			'https://app.greeninvoice.co.il/incomes/transactions/%s';

		if ( ! $this->supports_currency() ) {
			$this->enabled = 'no';
		}

		if ( $this->is_capable_of( Capability::INSTALLMENTS ) ) {
			if ( $this->get_installments() > 1 || $this->options->is_advanced_installments_on() ) {
				$this->has_fields = true;
			}

			if ( ! $this->options->is_advanced_installments_on() ) {
				$this->form_fields['installments'] = [
					'title'       => __( 'Max Number of Installments ', 'wc-gateway-greeninvoice' ),
					'type'        => 'select',
					'description' => __( 'Maximum number of installments available for the customer (leave 1 for no installments).', 'wc-gateway-greeninvoice' ),
					'default'     => 1,
					'desc_tip'    => true,
					'options'     => array_combine( range( 1, 12 ), range( 1, 12 ) ),
				];
			}
		}

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 1.3.0
	 */
	protected function register_hooks(): void {
		do_action( 'morning/wc/gateway_init', $this );

		if ( $this->is_capable_of( Capability::IFRAME_FORM ) ) {
			add_action( "woocommerce_receipt_{$this->id}", [ $this, 'receipt_page' ] );
		}

		// @phpstan-ignore-next-line
		add_action( "woocommerce_update_options_payment_gateways_{$this->id}", [ $this, 'process_admin_options' ] );
	}


	/**
	 * @return void
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
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function supports_currency(): bool {
		$supported_currencies = apply_filters( "morning/wc/supported_currencies_{$this->id}", $this->currencies );

		return in_array( get_woocommerce_currency(), $supported_currencies, true );
	}

	/**
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
	 * @param WC_Order $order Order to refund.
	 *
	 * @return bool
	 *
	 * @since 1.5.0
	 */
	public function can_refund_order( $order ): bool {
		return parent::can_refund_order( $order ) && ! empty( $order->get_transaction_id() ) && empty( $order->get_meta( MRN_WC_SLUG . '_refund_data' ) );
	}

	/**
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function needs_setup(): bool {
		return ! ( 'yes' === $this->options->get_activated() );
	}

	/**
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

		$site_url = ! is_null( MRN_WEBHOOK_URI ) ? trailingslashit( MRN_WEBHOOK_URI ) : home_url( '/' );

		return add_query_arg( $params, $site_url );
	}

	/**
	 * @return int
	 *
	 * @since 2.1.0
	 */
	public function get_installments(): int {
		return $this->settings['installments'] ?? 1;
	}


	/**
	 * @return void
	 *
	 * @since 2.1.0
	 */
	public function payment_fields(): void {
		parent::payment_fields();

		if ( $this->get_installments() > 1 || $this->options->is_advanced_installments_on() ) {
			include MRN_WC_PATH . '/templates/frontend/installments-form.php';
		}
	}


	/**
	 * @param int $order_id Current order id.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );

		$installments = ( empty( $_POST[ "{$this->id}_installments" ] ) ) ? 1 : absint( $_POST[ "{$this->id}_installments" ] );

		if ( ! empty( $_POST[ "{$this->id}_installments" ] ) ) {
			$installments = absint( $_POST[ "{$this->id}_installments" ] );
		}

		$order->update_meta_data( MRN_WC_SLUG . '_installments', (string) $installments );
		$order->save();

		if ( $this->is_capable_of( Capability::IFRAME_FORM ) ) {
			return [
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			];
		}

		$payment_url = $this->api->request_payment_form_url( $this->type, $order, $installments );

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

		$refund_meta = $this->api->request_refund( $order, $amount, $reason );

		if ( is_wp_error( $refund_meta ) ) {
			return $refund_meta;
		}

		$order->add_meta_data( MRN_WC_SLUG . '_refund_data', (array) $refund_meta, true );
		$order->add_order_note(
			sprintf(
			/* translators: %1$s Morning Brand, %2$s Refund Amount */
				__( '%1$s: A refund of %2$s was made via payment gateway.', 'wc-gateway-greeninvoice' ),
				'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>',
				wc_price( $amount, [ 'currency' => $order->get_currency() ] )
			)
		);
		$order->save();

		return true;
	}

	/**
	 * @param int $amount Amount to charge.
	 * @param WC_Order $order Order ID.
	 *
	 * @return void
	 *
	 * @since 1.6.0
	 */
	public function process_scheduled_payment( int $amount, WC_Order $order ): void {
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $order );

		if ( empty( $subscriptions ) ) {
			$order->set_status( 'failed', 'Unable to process order - no subscriptions found.' );
			$order->save();

			return;
		}

		/** @var WC_Order $subscription */
		$subscription = array_pop( $subscriptions );

		$token_id = $subscription->get_meta( MRN_WC_SLUG . '_subscription_token_id' );

		if ( ! $token_id ) {
			$parent_order = $subscription->get_parent_id() ? wc_get_order( $subscription->get_parent_id() ) : null;

			if ( $parent_order ) {
				$token_id = $parent_order->get_meta( MRN_WC_SLUG . '_subscription_token_id' );
			}
		}

		if ( ! $token_id ) {
			$order->set_status( 'failed', 'Unable to process order - missing token.' );
			$order->save();

			return;
		}

		$response = $this->api->charge_token( $token_id, $this->type, $order );

		if ( is_wp_error( $response ) ) {
			$order->set_status( 'failed', $response->get_error_message() );
			$order->save();

			return;
		}

		$order->set_status( 'on-hold', 'Payment received' );
		$order->save();
	}

	/**
	 * @param WC_Order $original_order Original order object.
	 * @param WC_Order $renewal_order Renewal order object.
	 *
	 * @return void
	 *
	 * @since 2.3.6
	 */
	public function process_payment_method_updated( WC_Order $original_order, WC_Order $renewal_order ): void {
		$original_order->add_order_note(
			sprintf(
			/* translators: %s Morning Brand */
				__( '%s: Credit card token replaced.', 'wc-gateway-greeninvoice' ),
				'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
			)
		);

		$original_order->add_meta_data( MRN_WC_SLUG . '_subscription_token_id', $renewal_order->get_meta( MRN_WC_SLUG . '_subscription_token_id' ), true );
		$original_order->save();
	}


	/**
	 * @param int $order_id Current order id.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function receipt_page( int $order_id ): void {
		$order        = wc_get_order( $order_id );
		$installments = (int) ( $order->get_meta( MRN_WC_SLUG . '_installments' ) ?: 1 );

		$requires_token = apply_filters( 'morning/wc/order_requires_token', false, $order );

		if ( $requires_token ) {
			$payment_url = $this->api->request_payment_token_url( $this->type, $order );
		} else {
			$payment_url = $this->api->request_payment_form_url( $this->type, $order, $installments );
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
	}
}
