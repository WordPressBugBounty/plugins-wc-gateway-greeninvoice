<?php
/**
 * Class PayPal_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage PayPal_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.3.0
 * @since      1.3.0
 */

namespace Morning\WC\Gateways\Blocks;

use Morning\WC\Abstracts\Payment_Gateway_Block;
use Morning\WC\Gateways\PayPal_Gateway;

defined( 'ABSPATH' ) || exit;


/**
 * Class PayPal_Gateway_Block
 *
 * @package Morning\WC\Gateways\Blocks
 */
class PayPal_Gateway_Block extends Payment_Gateway_Block {
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->gateway       = new PayPal_Gateway( false );
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-paypal',
				'file' => MRN_WC_URL . 'assets/js/blocks/paypal.min.js',
			],
		];

		parent::__construct();
	}
}
