<?php
/**
 * Class Google_Pay_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage Google_Pay_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.3.0
 * @since      1.3.0
 */

namespace Morning\WC\Gateways\Blocks;

use Morning\WC\Abstracts\Payment_Gateway_Block;
use Morning\WC\Gateways\Google_Pay_Gateway;

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
		$this->gateway       = new Google_Pay_Gateway( false );
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-google-pay',
				'file' => MRN_WC_URL . 'assets/js/blocks/google-pay.min.js',
			],
		];

		parent::__construct();
	}
}
