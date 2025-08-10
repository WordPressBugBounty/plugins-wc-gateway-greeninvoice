<?php
/**
 * Class Invoicing
 *
 * @package    Morning\WC
 * @subpackage Invoicing
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.2.0
 * @since      2.2.0
 */

namespace Morning\WC;

use Morning\WC\Config\Settings;
use Morning\WC\Utilities\Api;
use Morning\WC\Utilities\Logger;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class Invoicing
 *
 * @package Morning\WC
 */
class Invoicing {
	/**
	 * @var Settings
	 *
	 * @since 2.2.0
	 */
	private Settings $settings;
	/**
	 * @var Api
	 *
	 * @since 2.2.0
	 */
	private Api $api;


	/**
	 * Invoicing constructor
	 *
	 * @since 2.2.0
	 */
	public function __construct( Settings $settings, Api $api ) {
		$this->settings = $settings;
		$this->api      = $api;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.2.0
	 */
	private function register_hooks(): void {
		add_action( 'woocommerce_order_status_changed', [ $this, 'on_order_status_change' ], PHP_INT_MAX );
		add_action( 'woocommerce_order_refunded', [ $this, 'on_order_refunded' ], PHP_INT_MAX, 2 );
		add_action(
			'woocommerce_order_action_' . MRN_WC_SLUG . '_recreate_invoice',
			[ $this, 'handle_create_document_action' ]
		);
	}


	/**
	 * @param int $order_id Order id.
	 *
	 * @return void
	 *
	 * @since 2.2.0
	 */
	public function on_order_status_change( int $order_id ): void {
		$options = $this->settings->get_options();
		$order   = wc_get_order( $order_id );

		if ( ! $options->is_payment_gateway_allowed( $order->get_payment_method() ) ) {
			Logger::info( "Skipping document creation for order #{$order->get_id()} because the payment method is not allowed to automatic invoicing." );

			return;
		}

		if ( ! $order->has_status( $options->get_invoicing_order_status() ) ) {
			Logger::info( "Skipping document creation for order #{$order->get_id()} because it's in the right status." );

			return;
		}

		$this->create_document( $order );
	}

	/**
	 * @param int $order_id Order id.
	 * @param int $refund_id Refund id.
	 *
	 * @return void
	 *
	 * @since 2.2.0
	 */
	public function on_order_refunded( int $order_id, int $refund_id ): void {
		$options = $this->settings->get_options();

		if ( ! $options->is_invoicing_mode() ) {
			Logger::info( "Skipping refund document creation for order #{$order_id} because invoicing mode is disabled." );

			return;
		}

		$order      = wc_get_order( $order_id );
		$order_data = $order->get_meta( MRN_WC_SLUG . '_data' );

		if ( empty( $order_data ) || empty( $order_data['transaction_id'] ) ) {
			Logger::info( "Skipping refund document creation for order #{$order_id} because not transaction id was found." );

			return;
		}

		$refund = wc_get_order( $refund_id );

		$response = $this->api->create_refund_document( $refund, $order_data['transaction_id'] );

		if ( is_wp_error( $response ) ) {
			$order->add_order_note(
				sprintf(
				/* translators: %1$s Morning Brand, %2$s Error Message */
					__( '%1$s: Failed to create a refund document with error: `%2$s`', 'wc-gateway-greeninvoice' ),
					'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>',
					$response->get_error_message()
				)
			);
		} else {
			$order->add_meta_data( MRN_WC_SLUG . '_refund_data', (array) $response, true );

			$order->add_order_note(
				sprintf(
				/* translators: %s Morning Brand */
					__( '%s: Refund document created successfully.', 'wc-gateway-greeninvoice' ),
					'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
				)
			);
		}

		$order->save();
	}


	/**
	 * @param WC_Order $order Order details.
	 *
	 * @return void
	 *
	 * @since 2.2.0
	 */
	public function handle_create_document_action( WC_Order $order ): void {
		$this->create_document( $order );
	}


	/**
	 * @param WC_Order $order Order data.
	 *
	 * @return void
	 *
	 * @since 2.2.0
	 */
	private function create_document( WC_Order $order ): void {
		$options = $this->settings->get_options();

		if ( ! $options->is_invoicing_mode() ) {
			Logger::info( "Skipping document creation for order #{$order->get_id()} because invoicing mode is disabled." );

			return;
		}

		if ( ! empty( $order->get_meta( MRN_WC_SLUG . '_data' ) ) ) {
			Logger::info( "Skipping document creation for order #{$order->get_id()} because a document was already created." );

			return;
		}

		$response = $this->api->create_document( $order );

		if ( is_wp_error( $response ) ) {
			$order->add_order_note(
				sprintf(
				/* translators: %1$s Morning Brand, %2$s Error Message */
					__( '%1$s: Failed to create a document with error: `%2$s`', 'wc-gateway-greeninvoice' ),
					'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>',
					$response->get_error_message()
				)
			);
		} else {
			$ipn_data = [
				'id'               => $response['id'],
				'document_id'      => $response['number'],
				'number'           => $response['number'],
				'type'             => $response['type'],
				'transaction_id'   => $response['transactionId'],
				'url'              => $response['url']['origin'],
				'original_doc_url' => $response['url']['origin'],
				'copy_doc_url'     => $response['url']['en'] ?? $response['url']['he'],
			];

			$order->add_order_note(
				sprintf(
				/* translators: %s Morning Brand */
					__( '%s: Document created successfully.', 'wc-gateway-greeninvoice' ),
					'<strong>' . __( 'Morning', 'wc-gateway-greeninvoice' ) . '</strong>'
				)
			);
			$order->add_meta_data( MRN_WC_SLUG . '_data', $ipn_data, true );
		}
		$order->save();
	}
}
