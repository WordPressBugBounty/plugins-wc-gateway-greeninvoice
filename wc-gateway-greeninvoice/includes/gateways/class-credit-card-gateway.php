<?php
/**
 * Class Credit_Card_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage Credit_Card_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.0.0
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
 * Class Credit_Card_Gateway
 *
 * @package Morning\WC\Gateways
 */
class Credit_Card_Gateway extends Base_Payment_Gateway {
	/**
	 * Credit_Card_Gateway constructor.
	 *
	 * @param Api $api
	 * @param Settings $settings
	 *
	 * @since 1.0.0
	 */
	public function __construct( Api $api, Settings $settings ) {
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

		parent::__construct( $api, $settings );
	}
}
