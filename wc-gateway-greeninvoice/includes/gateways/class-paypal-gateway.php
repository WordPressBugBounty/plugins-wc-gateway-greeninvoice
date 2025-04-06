<?php
/**
 * Class PayPal_Gateway
 *
 * @package    Morning\WC\Gateways
 * @subpackage PayPal_Gateway
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.0.0
 */

namespace Morning\WC\Gateways;

use Morning\WC\Base\Base_Payment_Gateway;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Utilities\Api;

defined( 'ABSPATH' ) || exit;


/**
 * Class PayPal_Gateway
 *
 * @package Morning\WC\Gateways
 */
class PayPal_Gateway extends Base_Payment_Gateway {
	/**
	 * PayPal_Gateway constructor.
	 *
	 * @param Api $api
	 * @param Settings $settings
	 *
	 * @since 1.0.0
	 */
	public function __construct( Api $api, Settings $settings ) {
		$this->type               = Payment_Type::PAYPAL;
		$this->id                 = MRN_WC_SLUG . '-paypal';
		$this->method_title       = esc_html__( 'Morning - PayPal', 'wc-gateway-greeninvoice' );
		$this->method_description = esc_html__( 'Accept credit cards with Morning PayPal integration. In order to complete the process, go to your WooCommerce plugin settings in Greeninvoice, and choose paypal in the "payment options" section.', 'wc-gateway-greeninvoice' );

		parent::__construct( $api, $settings );
	}
}
