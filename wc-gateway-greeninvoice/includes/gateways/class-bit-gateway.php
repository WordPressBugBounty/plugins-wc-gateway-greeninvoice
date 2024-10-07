<?php
/**
 * Class Bit_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Bit_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.1.0
 */

namespace Morning\WC\Gateways;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Payment_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Bit_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Bit_Gateway extends Payment_Gateway {
	/**
	 * Bit_Gateway constructor.
	 *
	 * @param bool $init_hooks Should register hooks?
	 *
	 * @version 1.3.0
	 * @since 1.1.0
	 */
	public function __construct( bool $init_hooks = true ) {
		$this->type               = Payment_Type::BIT;
		$this->id                 = MRN_WC_SLUG . '-bit';
		$this->method_title       = esc_html__( 'Morning - Bit', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept bit payments with Morning-Meshulam plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose bit in the "payment options" section.', 'wc-gateway-greeninvoice' );
		$this->currencies         = [
			Currency::ILS,
		];
		$this->capabilities       = [
			Capability::IFRAME_FORM,
		];

		parent::__construct( $init_hooks );
	}
}
