<?php
/**
 * Class PayPal_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage PayPal_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.3.0
 */

namespace Morning\WC\Gateways\Blocks;

use Morning\WC\Base\Base_Payment_Gateway_Block;
use Morning\WC\Exceptions\Gateway_Exception;

defined( 'ABSPATH' ) || exit;


/**
 * Class PayPal_Gateway_Block
 *
 * @package Morning\WC\Gateways\Blocks
 */
class PayPal_Gateway_Block extends Base_Payment_Gateway_Block {
	/**
	 * PayPal_Gateway_Block constructor.
	 *
	 * @throws Gateway_Exception
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		$this->name          = 'greeninvoice-paypal';
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-paypal',
				'file' => MRN_WC_URL . 'assets/js/blocks/paypal.js',
				'deps' => $this->get_block_dependencies( MRN_WC_PATH . 'assets/js/blocks/paypal.asset.php' ),
			],
		];

		parent::__construct();
	}
}
