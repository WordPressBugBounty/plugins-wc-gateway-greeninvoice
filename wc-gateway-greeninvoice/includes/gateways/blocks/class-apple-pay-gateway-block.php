<?php
/**
 * Class Apple_Pay_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage Apple_Pay_Gateway_Block
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
 * Class Apple_Pay_Gateway_Block
 *
 * @package Morning\WC\Gateways\Blocks
 */
class Apple_Pay_Gateway_Block extends Base_Payment_Gateway_Block {
	/**
	 * Apple_Pay_Gateway_Block constructor.
	 *
	 * @throws Gateway_Exception
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		$this->name          = 'greeninvoice-apple-pay';
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-apple-pay',
				'file' => MRN_WC_URL . 'assets/js/blocks/apple-pay.js',
				'deps' => $this->get_block_dependencies( MRN_WC_PATH . 'assets/js/blocks/apple-pay.asset.php' ),
			],
		];

		parent::__construct();
	}
}
