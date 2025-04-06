<?php
/**
 * Class Google_Pay_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Google_Pay_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.1.5
 */

namespace Morning\WC\Gateways;

use Morning\WC\Base\Base_Payment_Gateway;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Capability;
use Morning\WC\Enum\Currency;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Utilities\Api;

defined( 'ABSPATH' ) || exit;


/**
 * Class Google_Pay_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Google_Pay_Gateway extends Base_Payment_Gateway {
	/**
	 * Class Google_Pay_Gateway
	 *
	 * @param Api $api
	 * @param Settings $settings
	 *
	 * @since 1.1.5
	 */
	public function __construct( Api $api, Settings $settings ) {
		$this->type               = Payment_Type::GOOGLE_PAY;
		$this->id                 = MRN_WC_SLUG . '-google-pay';
		$this->method_title       = esc_html__( 'Morning - Google Pay', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept Google Pay payments with Morning-Meshulam plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose Google Pay in the "payment options" section.', 'wc-gateway-greeninvoice' );
		$this->currencies         = [ Currency::ILS ];
		$this->capabilities       = [ Capability::INSTALLMENTS, Capability::IFRAME_FORM ];

		parent::__construct( $api, $settings );
	}


	/**
	 * @return void
	 *
	 * @since 1.1.5
	 */
	protected function register_hooks(): void {
		add_filter( "morning/wc/{$this->id}_payment_form_atts", [ $this, 'custom_payment_form_atts' ] );

		parent::register_hooks();
	}


	/**
	 * @param string $atts Payment form attributes.
	 *
	 * @return string
	 *
	 * @since 1.1.5
	 */
	public function custom_payment_form_atts( string $atts ): string {
		return $atts . ' allow="payment"';
	}
}
