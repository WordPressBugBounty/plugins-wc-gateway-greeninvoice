<?php
/**
 * Class Google_Pay_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Google_Pay_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.1.5
 */

namespace Morning\WC\Gateways;

use Morning\WC\Abstracts\Payment_Gateway;
use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Payment_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Google_Pay_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Google_Pay_Gateway extends Payment_Gateway {
	/**
	 * Class Google_Pay_Gateway
	 *
	 * @param bool $init_hooks Should register hooks?
	 *
	 * @version 1.3.0
	 * @since 1.1.5
	 */
	public function __construct( bool $init_hooks = true ) {
		$this->type               = Payment_Type::GOOGLE_PAY;
		$this->id                 = MRN_WC_SLUG . '-google-pay';
		$this->method_title       = esc_html__( 'Morning - Google Pay', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept Google Pay payments with Morning-Meshulam plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose Google Pay in the "payment options" section.', 'wc-gateway-greeninvoice' );
		$this->currencies         = [
			Currency::ILS,
		];
		$this->capabilities       = [
			Capability::INSTALLMENTS,
			Capability::IFRAME_FORM,
		];

		parent::__construct( $init_hooks );
	}


	/**
	 * @inheritDoc
	 */
	protected function init_hooks(): void {
		add_filter( "morning/wc/{$this->id}_payment_form_atts", [ $this, 'custom_payment_form_atts' ] );

		parent::init_hooks();
	}


	/**
	 * Inject custom payment form attributes.
	 *
	 * @param string $atts Payment form attributes.
	 *
	 * @return string
	 */
	public function custom_payment_form_atts( string $atts ): string {
		return $atts . ' allow="payment"';
	}
}
