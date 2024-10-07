<?php
/**
 * Class Credit_Card_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Credit_Card_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.0.0
 */

namespace Morning\WC\Gateways;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Payment_Type;
use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Class Credit_Card_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Credit_Card_Gateway extends Payment_Gateway {
	/**
	 * Credit_Card_Gateway constructor.
	 *
	 * @param bool $init_hooks Should register hooks?
	 *
	 * @version 1.6.0
	 * @since 1.0.0
	 */
	public function __construct( bool $init_hooks = true ) {
		$this->type               = Payment_Type::CREDIT_CARD;
		$this->id                 = MRN_WC_SLUG . '-creditcard';
		$this->method_title       = esc_html__( 'Morning - Credit Cards', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept credit cards with Morning plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose the clearing provider in the "payment options" section.', 'wc-gateway-greeninvoice' );

		$this->currencies = [
			Currency::ILS,
			Currency::USD,
			Currency::EUR,
			Currency::GBP,
			Currency::CAD,
		];

		$this->capabilities = [
			Capability::INSTALLMENTS,
			Capability::IFRAME_FORM,
			Capability::TOKENIZATION,
		];

		parent::__construct( $init_hooks );
	}
}
