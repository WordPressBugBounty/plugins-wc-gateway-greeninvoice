<?php
/**
 * Class API
 *
 * @package    Morning\WC\Utilities
 * @subpackage API
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Setting;
use Morning\WC\Enum\HTTP_Code;
use Morning\WC\Formatters\Gateways_Response_Formatter;
use Morning\WC\Mappers\Document_Client_Mapper;
use Morning\WC\Mappers\Document_Coupons_Rows_Mapper;
use Morning\WC\Mappers\Document_Income_Rows_Mapper;
use Morning\WC\Mappers\Document_Shipping_Rows_Mapper;
use WC_Order;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class API
 *
 * @package Morning\WC\Utilities
 */
class API {
	/**
	 * Plugin instance.
	 *
	 * @var null|API
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;


	/**
	 * Client license key.
	 *
	 * @var null|string
	 *
	 * @since 1.0.0
	 */
	protected $license_key = null;

	/**
	 * Use sandbox API.
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	protected $sandbox = false;


	/**
	 * API constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$options = Settings::get_options();

		if ( ! empty( $options[ Setting::SANDBOX ] ) ) {
			$this->sandbox = (bool) $options[ Setting::SANDBOX ];
		}

		if ( ! empty( $options[ Setting::LICENSE_KEY ] ) ) {
			$this->set_license_key( $options[ Setting::LICENSE_KEY ] );
		}
	}


	/**
	 * Connect the store to Morning.
	 *
	 * @return array|WP_Error
	 *
	 * @since 1.0.0
	 */
	public function connect_store() {
		if ( empty( $this->license_key ) ) {
			return new WP_Error( 'morning-api', 'License key is missing.' );
		}

		$url = $this->get_request_url( 'api/v1/plugins/woocommerce/auth' );

		$response = $this->request( $url );

		$options = Settings::get_options();

		if ( $this->is_response_ok( $response ) ) {
			$body = $this->get_body( $response );

			$options[ Setting::ACTIVATED ] = 'yes';
			$options[ Setting::GATEWAYS ]  = Gateways_Response_Formatter::format( $body['gateways'] );
		} else {
			$options[ Setting::ACTIVATED ] = 'no';
			$options[ Setting::GATEWAYS ]  = null;
		}

		update_option( Settings::OPTIONS_KEY, $options );

		return $response;
	}

	/**
	 * Request a new payment url.
	 *
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 * @param int $installments Amount of split payments.
	 *
	 * @return string|WP_Error
	 *
	 * @since 1.0.0
	 */
	public function request_payment_url( int $payment_method, WC_Order $order, int $installments = 1 ) {
		$params = [
			'body' => $this->build_order_document_data( $payment_method, $order, $installments ),
		];

		$params = apply_filters( 'morning/wc/order_invoice_params', $params, $order );

		$url = $this->get_request_url( 'api/v1/plugins/woocommerce/pay/url' );

		$response = $this->request( $url, $params );

		if ( ! $this->is_response_ok( $response ) ) {
			return new WP_Error( 'morning-api', 'Could not retrieve payment url.' );
		}

		$response_body = $this->get_body( $response );

		return $response_body['url'] ?? new WP_Error( 'morning-api', 'Could not retrieve payment url.' );
	}

	/**
	 * Request a new token creation url.
	 *
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 *
	 * @return string|WP_Error
	 *
	 * @since 1.6.0
	 */
	public function request_payment_token_url( int $payment_method, WC_Order $order ) {
		$params = [
			'body' => $this->build_order_document_data( $payment_method, $order ),
		];

		unset( $params['body']['amount'] );

		$params['body']['initialAmount'] = $order->get_total();

		$params = apply_filters( 'morning/wc/order_invoice_params', $params, $order );

		$url = $this->get_request_url( 'api/v1/plugins/woocommerce/token/url' );

		$response = $this->request( $url, $params );

		if ( ! $this->is_response_ok( $response ) ) {
			return new WP_Error( 'morning-api', 'Could not retrieve payment url.' );
		}

		$response_body = $this->get_body( $response );

		return $response_body['url'] ?? new WP_Error( 'morning-api', 'Could not retrieve payment url.' );
	}

	/**
	 * Request a partial or full refund for a specific order.
	 *
	 * @param WC_Order $order Order to refund.
	 * @param float $amount Amount to refund.
	 * @param string $reason Refund reason.
	 *
	 * @return mixed|WP_Error
	 *
	 * @since 1.5.0
	 */
	public function request_transaction_refund( WC_Order $order, float $amount, string $reason ) {
		$params = [
			'body' => [
				'amount' => $amount,
				'reason' => empty( $reason ) ? null : $reason,
			],
		];

		$url = $this->get_request_url( 'api/v1/plugins/woocommerce/transaction/{id}/refund', [ '{id}' => $order->get_transaction_id() ] );

		$response = $this->request( $url, $params );

		if ( ! $this->is_response_ok( $response ) ) {
			return new WP_Error( 'morning-api', 'Could not issue refund.' );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Charge saved token.
	 *
	 * @param string $token_id Token id.
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 *
	 * @return mixed|WP_Error
	 *
	 * @string 1.6.0
	 */
	public function request_token_charge( string $token_id, int $payment_method, WC_Order $order ) {
		$params = [
			'body' => $this->build_order_document_data( $payment_method, $order ),
		];

		$params = apply_filters( 'morning/wc/order_invoice_params', $params, $order );

		$url = $this->get_request_url( 'api/v1/plugins/woocommerce/token/{id}/charge', [ '{id}' => $token_id ] );

		$response = $this->request( $url, $params );

		if ( ! $this->is_response_ok( $response ) ) {
			return new WP_Error( 'morning-api', 'Could not charge token.' );
		}

		$response_body = $this->get_body( $response );

		return $response_body['url'] ?? new WP_Error( 'morning-api', 'Could not charge token.' );
	}


	/**
	 * Build document data.
	 *
	 * @param int $payment_method Desired payment method.
	 * @param WC_Order $order Current order.
	 * @param int $installments Amount of split payments.
	 *
	 * @return array
	 *
	 * @since 1.6.0
	 */
	public function build_order_document_data( int $payment_method, WC_Order $order, int $installments = 1 ): array {
		return [
			'type'        => $payment_method,
			'device'      => Device::get_type(),
			'maxPayments' => $installments,
			'taxable'     => wc_tax_enabled(),
			'amount'      => $order->get_total(),
			'currency'    => $order->get_currency(),
			'lang'        => ( 'he_IL' === get_locale() ) ? 'he' : 'en',
			/* translators: %s Order Number */
			'description' => sprintf( esc_html__( 'Order #%s', 'wc-gateway-greeninvoice' ), $order->get_id() ),
			'successUrl'  => Payment_Gateway::get_gateway_url( 'success', $order ),
			'failureUrl'  => Payment_Gateway::get_gateway_url( 'failure', $order ),
			'notifyUrl'   => Payment_Gateway::get_gateway_url( 'ipn', $order ),
			'client'      => Document_Client_Mapper::map( $order ),
			'items'       => Document_Income_Rows_Mapper::map( $order ),
			'shipping'    => Document_Shipping_Rows_Mapper::map( $order ),
			'coupons'     => Document_Coupons_Rows_Mapper::map( $order ),
		];
	}


	/**
	 * Make an API call to Morning.
	 *
	 * @param string $url API call endpoint.
	 * @param array $params API call params.
	 * @param string $method API call method.
	 *
	 * @return array|WP_Error
	 *
	 * @since 1.0.0
	 */
	public function request( string $url, array $params = [], string $method = 'POST' ) {
		if ( empty( $url ) ) {
			return new WP_Error( 'morning-api', 'Endpoint is invalid.' );
		}

		$payload = [
			'method'      => $method,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'user-agent'  => 'WooCommerce/' . WC()->version,
			'headers'     => [
				'Content-Type'  => 'application/json',
				'Authorization' => $this->get_authorization_token(),
			],
		];

		if ( ! empty( $params['body'] ) ) {
			$payload['body'] = wp_json_encode( $params['body'] );
		}

		if ( ! empty( $params['headers'] ) ) {
			$payload['headers'] = array_merge( $payload['headers'], $params['headers'] );
		}

		$response = wp_safe_remote_request(
			$url,
			apply_filters(
				'morning/wc/api_payload',
				$payload,
				$params,
				$url,
				$method
			)
		);

		Logger::get_instance()->log(
			[
				'url'      => $url,
				'request'  => $payload,
				'response' => $response,
			],
			'debug'
		);

		return $response;
	}


	/**
	 * Returns whether the response is ok.
	 *
	 * @param array|WP_Error $response API call response.
	 *
	 * @return bool
	 *
	 * @since 1.6.0
	 */
	public function is_response_ok( $response ): bool {
		return HTTP_Code::OK === wp_remote_retrieve_response_code( $response );
	}


	/**
	 * Return authorization header.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function get_authorization_token(): string {
		$shop_url = str_replace( [ 'https://', 'http://' ], '', untrailingslashit( site_url() ) );

		// @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return 'Basic ' . base64_encode( "{$shop_url}:{$this->license_key}" );
		// @phpcs:enable
	}

	/**
	 * Retrieve full endpoint uri, including params.
	 *
	 * @param string $endpoint Endpoint with placeholders.
	 * @param string[] $params Endpoint params to replace.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public function get_request_url( string $endpoint, array $params = [] ): string {
		$base_api = $this->sandbox ? 'https://sandbox.d.greeninvoice.co.il' : 'https://api.greeninvoice.co.il';

		if ( defined( 'MRN_API_BASE' ) && MRN_API_BASE ) {
			$base_api = MRN_API_BASE;
		}

		$base_api = trailingslashit( $base_api );

		$endpoint = str_replace( array_keys( $params ), array_values( $params ), $endpoint );

		return $base_api . $endpoint;
	}

	/**
	 * Retrieve API response body.
	 *
	 * @param array|WP_Error $response API call response.
	 *
	 * @return null|array
	 *
	 * @since 1.0.0
	 */
	public function get_body( $response ): ?array {
		return json_decode( wp_remote_retrieve_body( $response ), true );
	}


	/**
	 * Set client license key.
	 *
	 * @param string $license_key Client license key.
	 *
	 * @since 1.0.0
	 */
	public function set_license_key( string $license_key ): void {
		if ( empty( $license_key ) ) {
			return;
		}

		$this->license_key = $license_key;
	}


	/**
	 * Retrieve plugin's instance.
	 *
	 * @return API
	 *
	 * @since 1.0.0
	 */
	public static function get_instance(): API {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
