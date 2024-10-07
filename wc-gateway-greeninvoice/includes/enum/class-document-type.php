<?php
/**
 * Class Document_Type
 *
 * @package    Morning\WC\Enum
 * @subpackage Document_Type
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Document_Type
 *
 * @package Morning\WC\Enum
 */
class Document_Type {
	/**
	 * @since 1.0.0
	 */
	const PRICE_QUOTE = 10;

	/**
	 * @since 1.0.0
	 */
	const PAYMENT_CONFIRM = 20;

	/**
	 * @since 1.0.0
	 */
	const ORDER = 100;

	/**
	 * @since 1.0.0
	 */
	const DELIVERY_NOTE = 200;

	/**
	 * @since 1.0.0
	 */
	const RETURN_DELIVERY = 210;

	/**
	 * @since 1.0.0
	 */
	const PROFORMA_INVOICE = 300;

	/**
	 * @since 1.0.0
	 */
	const INVOICE = 305;

	/**
	 * @since 1.0.0
	 */
	const INVOICE_RECEIPT = 320;

	/**
	 * @since 1.0.0
	 */
	const CREDIT_NOTE = 330;

	/**
	 * @since 1.0.0
	 */
	const RECEIPT = 400;

	/**
	 * @since 1.0.0
	 */
	const DONATE_RECEIPT = 405;

	/**
	 * @since 1.0.0
	 */
	const PURCHASE_ORDER = 500;

	/**
	 * @since 1.0.0
	 */
	const DEPOSIT_RECEIPT = 600;

	/**
	 * @since 1.0.0
	 */
	const DEPOSIT_WITHDRAWAL = 610;


	/**
	 * Retrieve document name by type.
	 *
	 * @param int $doc_type Document type number.
	 *
	 * @return null|string
	 *
	 * @since 1.0.0
	 */
	public static function get_type( int $doc_type ): ?string {
		$doc_names = [
			self::PRICE_QUOTE        => esc_html_x( 'Price Quotation', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::PAYMENT_CONFIRM    => esc_html_x( 'Payment Confirmation', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::ORDER              => esc_html_x( 'Order', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::DELIVERY_NOTE      => esc_html_x( 'Delivery Note', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::RETURN_DELIVERY    => esc_html_x( 'Return Delivery', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::PROFORMA_INVOICE   => esc_html_x( 'Proforma Invoice', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::INVOICE            => esc_html_x( 'Invoice', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::INVOICE_RECEIPT    => esc_html_x( 'Invoice / Receipt', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::CREDIT_NOTE        => esc_html_x( 'Credit Note', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::RECEIPT            => esc_html_x( 'Receipt', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::DONATE_RECEIPT     => esc_html_x( 'Donation Receipt', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::PURCHASE_ORDER     => esc_html_x( 'Purchase Order', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::DEPOSIT_RECEIPT    => esc_html_x( 'Deposit Receipt', 'Document Type', 'wc-gateway-greeninvoice' ),
			self::DEPOSIT_WITHDRAWAL => esc_html_x( 'Deposit Withdrawal', 'Document Type', 'wc-gateway-greeninvoice' ),
		];

		return $doc_names[ $doc_type ] ?? null;
	}
}
