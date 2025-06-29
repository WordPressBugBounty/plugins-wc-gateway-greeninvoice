<?php
/**
 * Class API
 *
 * @package    Morning\WC\Utilities
 * @subpackage API
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.5
 * @since      1.0.0
 */

namespace Morning\WC\Utilities;

use DateTime;
use Morning\WC\Base\Base_Payment_Gateway;
use Morning\WC\Enum\Payment_Method;
use Morning\WC\Enum\Request_Flow;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Http\HTTP_Client;
use Morning\WC\Http\Http_Request;
use Morning\WC\Http\Http_Response;
use Morning\WC\Mappers\Document_Client_Mapper;
use Morning\WC\Mappers\Document_Coupons_Rows_Mapper;
use Morning\WC\Mappers\Document_Income_Rows_Mapper;
use Morning\WC\Mappers\Document_Shipping_Rows_Mapper;
use WC_Order;
use WC_Order_Refund;
use WC_Payment_Gateway_CC;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class API
 *
 * @package Morning\WC\Utilities
 */
class Api {
	/**
	 * @var HTTP_Client
	 *
	 * @since 2.0.0
	 */
	private Http_Client $client;
	/**
	 * @var Auth
	 *
	 * @since 2.0.0
	 */
	private Auth $auth;


	/**
	 * Api constructor.
	 *
	 * @param HTTP_Client $client
	 * @param Auth $auth
	 *
	 * @since 1.0.0
	 */
	public function __construct( HTTP_Client $client, Auth $auth ) {
		$this->client = $client;
		$this->auth   = $auth;
	}


	/**
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 * @param int $installments Number of split payments.
	 *
	 * @return string|WP_Error
	 *
	 * @since 1.0.0
	 */
	public function request_payment_form_url( int $payment_method, WC_Order $order, int $installments = 1 ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/pay/url' ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body( $this->build_order_document_data( $order, Request_Flow::SINGLE_PAYMENT, $payment_method, $installments ) );
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response ) );
		}

		return $response->json_body()['url'] ?? '';
	}

	/**
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 *
	 * @return string|WP_Error
	 *
	 * @since 1.6.0
	 */
	public function request_payment_token_url( int $payment_method, WC_Order $order ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/token/url' ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body( $this->build_order_document_data( $order, Request_Flow::CREATE_TOKEN, $payment_method ) );
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response ) );
		}

		return $response->json_body()['url'] ?? '';
	}

	/**
	 * @param WC_Order $order Order to refund.
	 * @param float $amount Amount to refund.
	 * @param string $reason Refund reason.
	 *
	 * @return array|WP_Error
	 *
	 * @since 1.5.0
	 */
	public function request_refund( WC_Order $order, float $amount, string $reason ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/transactions/{id}/refund', [ '{id}' => $order->get_transaction_id() ] ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body(
			[
				'amount' => $amount,
				'reason' => empty( $reason ) ? null : $reason,
			]
		);
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response, false ) );
		}

		return $response->json_body();
	}

	/**
	 * @param string $token_id Token id.
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 *
	 * @return string|WP_Error
	 *
	 * @string 1.6.0
	 */
	public function charge_token( string $token_id, int $payment_method, WC_Order $order ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/token/{id}/charge', [ '{id}' => $token_id ] ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body( $this->build_order_document_data( $order, Request_Flow::CHARGE_TOKEN, $payment_method ) );
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response, false ) );
		}

		return $response->json_body()['url'];
	}

	/**
	 * @param WC_Order $order Order details.
	 *
	 * @return array|WP_Error
	 *
	 * @since 2.0.0
	 */
	public function create_document( WC_Order $order ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/documents' ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body( $this->build_order_document_data( $order, Request_Flow::CREATE_DOCUMENT ) );
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response, false ) );
		}

		return $response->json_body();
	}

	/**
	 * @param WC_Order_Refund $order Order details.
	 * @param string $transaction_id Transaction id.
	 *
	 * @return array|WP_Error
	 *
	 * @since 2.0.3
	 */
	public function create_refund_document( WC_Order_Refund $order, string $transaction_id ) {
		try {
			$request = new Http_Request( self::get_request_url( '/api/v1/plugins/woocommerce/transactions/{id}/cancel', [ '{id}' => $transaction_id ] ) );
		} catch ( Container_Exception $ex ) {
			return new WP_Error( 'morning-api', $ex->getMessage() );
		}

		$request->set_body( $this->build_refund_document_data( $order, Request_Flow::CANCEL_DOCUMENT ) );
		$request->add_header( 'Authorization', $this->auth->get_authorization_token() );

		$response = $this->client->post( $request );

		if ( ! $response->is_ok() ) {
			return new WP_Error( 'morning-api', $this->get_api_error_message( $response, false ) );
		}

		return $response->json_body();
	}


	/**
	 * @param string $endpoint Request endpoint.
	 * @param array $params Endpoint params.
	 *
	 * @return string
	 *
	 * @throws Container_Exception
	 *
	 * @since 1.4.0
	 */
	public static function get_request_url( string $endpoint, array $params = [] ): string {
		$options = ( mrn_get_container()->get_settings() )->get_options();

		$base_api = self::get_base_url( $options->is_sandbox_mode() );

		$endpoint = str_replace( array_keys( $params ), array_values( $params ), $endpoint );

		return $base_api . $endpoint;
	}

	/**
	 * @param bool $sandbox_mode Is sandbox mode enabled?
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public static function get_base_url( bool $sandbox_mode = false ): string {
		if ( defined( 'MRN_API_BASE' ) ) {
			$base_url = MRN_API_BASE;
		} else {
			$base_url = $sandbox_mode ? 'https://sandbox.d.greeninvoice.co.il' : 'https://api.greeninvoice.co.il';
		}

		return untrailingslashit( $base_url );
	}


	/**
	 * @param WC_Order $order Current order.
	 * @param int $flow Request flow.
	 * @param int|null $payment_method Desired payment method.
	 * @param int $installments Number of split payments.
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public function build_order_document_data( WC_Order $order, int $flow, int $payment_method = null, int $installments = 1 ): array {
		$doc = [
			'type'        => $payment_method,
			'maxPayments' => $installments,
			'taxable'     => wc_tax_enabled(),
			'amount'      => $order->get_total(),
			'currency'    => $order->get_currency(),
			'lang'        => ( 'he_IL' === get_locale() ) ? 'he' : 'en',
			/* translators: %s Order Number */
			'description' => sprintf( esc_html__( 'Order #%s', 'wc-gateway-greeninvoice' ), $order->get_id() ),
			'successUrl'  => Base_Payment_Gateway::get_gateway_url( 'success', $order ),
			'failureUrl'  => Base_Payment_Gateway::get_gateway_url( 'failure', $order ),
			'notifyUrl'   => Base_Payment_Gateway::get_gateway_url( 'ipn', $order ),
			'client'      => Document_Client_Mapper::map( $order ),
			'items'       => Document_Income_Rows_Mapper::map( $order ),
			'shipping'    => Document_Shipping_Rows_Mapper::map( $order ),
			'coupons'     => Document_Coupons_Rows_Mapper::map( $order ),
		];

		switch ( $flow ) {
			case Request_Flow::CREATE_TOKEN:
				unset( $doc['amount'] );

				$doc['initialAmount'] = $order->get_total();
				break;

			case Request_Flow::CREATE_DOCUMENT:
				unset( $doc['successUrl'], $doc['failureUrl'], $doc['notifyUrl'] );

				$method = $this->identify_payment_method( $order );

				$doc['date']          = ( $order->get_date_paid() ?? new DateTime() )->format( 'Y-m-d' );
				$doc['transactionId'] = $this->generate_transaction_id( $order );
				$doc['paymentMethod'] = $method;

				if ( Payment_Method::CREDIT_CARD === $method ) {
					if ( method_exists( $order, 'get_payment_card_info' ) ) {
						$cc_details = $order->get_payment_card_info();

						$doc['cardNumber'] = $cc_details['last4'];
					}
				}
				break;
		}

		return apply_filters( 'morning/wc/order_invoice_params', $doc, $order, $payment_method, $flow );
	}

	/**
	 * @param WC_Order_Refund $refund Current refund.
	 * @param int $flow Request flow.
	 *
	 * @return array
	 *
	 * @since 2.0.3
	 */
	public function build_refund_document_data( WC_Order_Refund $refund, int $flow ): array {
		$doc = [
			'taxable'     => wc_tax_enabled(),
			'amount'      => $refund->get_total(),
			'currency'    => $refund->get_currency(),
			'lang'        => ( 'he_IL' === get_locale() ) ? 'he' : 'en',
			/* translators: %s Order Number */
			'description' => sprintf( esc_html__( 'Order #%s', 'wc-gateway-greeninvoice' ), $refund->get_parent_id() ),
		];

		if ( Request_Flow::CANCEL_DOCUMENT === $flow ) {
			$doc['reason'] = $refund->get_reason();
			$doc['amount'] = floatval( $refund->get_amount() );
		}

		return $doc;
	}

	/**
	 * @param WC_Order $order Order details.
	 *
	 * @return int
	 *
	 * @since 2.0.0
	 */
	public function identify_payment_method( WC_Order $order ): int {
		$payment_method = Payment_Method::CREDIT_CARD;

		$gateways        = WC()->payment_gateways()->payment_gateways();
		$payment_gateway = $gateways[ $order->get_payment_method() ] ?? null;

		switch ( $order->get_payment_method() ) {
			case 'cod':
				$payment_method = Payment_Method::CASH;
				break;
			case 'paypal':
				$payment_method = Payment_Method::PAYPAL;
				break;
			case 'bacs':
				$payment_method = Payment_Method::WIRE_TRANSFER;
				break;
		}

		if ( false !== strpos( $order->get_payment_method(), 'paypal' ) ) {
			$payment_method = Payment_Method::PAYPAL;
		}

		if ( false !== strpos( $order->get_payment_method(), 'bit' ) ) {
			$payment_method = Payment_Method::BIT;
		}

		if (
			false !== strpos( $order->get_payment_method(), 'cc' ) ||
			false !== strpos( $order->get_payment_method(), 'credit' ) ||
			false !== strpos( $order->get_payment_method(), 'card' ) ||
			$payment_gateway instanceof WC_Payment_Gateway_CC
		) {
			$payment_method = Payment_Method::CREDIT_CARD;
		}

		if ( false !== strpos( $order->get_payment_method(), 'google' ) ) {
			$payment_method = Payment_Method::GOOGLE_PAY;
		}

		if ( false !== strpos( $order->get_payment_method(), 'apple' ) ) {
			$payment_method = Payment_Method::APPLE_PAY;
		}

		return $payment_method;
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function generate_transaction_id( WC_Order $order ): string {
		$hash = md5( "{$order->get_id()}/{$order->get_order_key()}/{$order->get_order_number()}" );

		return vsprintf( 'WC-%s%s-%s-%s-%s-%s%s%s', str_split( $hash, 4 ) );
	}

	/**
	 * @param Http_Response $response Http response.
	 * @param bool $mask Should error response be masked?
	 *
	 * @return string
	 *
	 * @since 1.6.1
	 */
	public function get_api_error_message( Http_Response $response, bool $mask = true ): string {
		$error_code    = $response->get_error_code() ?? 3000;
		$error_message = $response->get_error_message() ?? __( 'General Error', 'wc-gateway-greeninvoice' );

		if ( ! $mask ) {
			return $error_message;
		}

		switch ( $error_code ) {
			case 1006:
			case 1012:
			case 1015:
			case 1118:
			case 2001:
			case 2122:
			case 2814:
			case 5001:
				return __( 'General Error', 'wc-gateway-greeninvoice' );

			default:
				return $error_message;
		}
	}
}
