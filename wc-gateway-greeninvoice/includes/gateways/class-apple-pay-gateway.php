<?php
/**
 * Class Apple_Pay_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Apple_Pay_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.1
 * @since      1.1.5
 */

namespace Morning\WC\Gateways;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Payment_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Apple_Pay_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Apple_Pay_Gateway extends Payment_Gateway {
	/**
	 * Class Apple_Pay_Gateway
	 *
	 * @param bool $init_hooks Should register hooks?
	 *
	 * @version 1.3.0
	 * @since 1.2.1
	 */
	public function __construct( bool $init_hooks = true ) {
		$this->type               = Payment_Type::APPLE_PAY;
		$this->id                 = MRN_WC_SLUG . '-apple-pay';
		$this->method_title       = esc_html__( 'Morning - Apple Pay', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept Apple Pay payments with Morning-Meshulam plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose Apple Pay in the "payment options" section.', 'wc-gateway-greeninvoice' );
		$this->currencies         = [
			Currency::ILS,
		];

		parent::__construct( $init_hooks );
	}
}
