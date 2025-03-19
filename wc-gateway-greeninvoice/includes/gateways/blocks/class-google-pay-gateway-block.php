<?php
/**
 * Class Google_Pay_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage Google_Pay_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.1
 * @since      1.3.0
 */

namespace Morning\WC\Gateways\Blocks;

use Morning\WC\Abstracts\Payment_Gateway_Block;

defined( 'ABSPATH' ) || exit;


/**
 * Class Google_Pay_Gateway_Block
 *
 * @package Morning\WC\Gateways\Blocks
 */
class Google_Pay_Gateway_Block extends Payment_Gateway_Block {
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->name          = 'greeninvoice-google-pay';
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-google-pay',
				'file' => MRN_WC_URL . 'assets/js/blocks/google-pay.js',
				'deps' => $this->get_block_dependencies( MRN_WC_PATH . 'assets/js/blocks/google-pay.asset.php' ),
			],
		];

		parent::__construct();
	}
}
