<?php
/**
 * Class Bit_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Bit_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.1.0
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
 * Class Bit_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Bit_Gateway extends Base_Payment_Gateway {
	/**
	 * Bit_Gateway constructor.
	 *
	 * @param Api $api
	 * @param Settings $settings
	 *
	 * @since 1.1.0
	 */
	public function __construct( Api $api, Settings $settings ) {
		$this->type               = Payment_Type::BIT;
		$this->id                 = MRN_WC_SLUG . '-bit';
		$this->method_title       = esc_html__( 'Morning - Bit', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept bit payments with Morning-Meshulam plugin. In order to complete the process, go to your WooCommerce plugin settings in Morning, and choose bit in the "payment options" section.', 'wc-gateway-greeninvoice' );
		$this->currencies         = [ Currency::ILS ];
		$this->capabilities       = [ Capability::IFRAME_FORM ];

		parent::__construct( $api, $settings );
	}
}
