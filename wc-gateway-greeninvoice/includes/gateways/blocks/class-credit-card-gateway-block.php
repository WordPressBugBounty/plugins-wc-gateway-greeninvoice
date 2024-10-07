<?php
/**
 * Class Credit_Card_Gateway_Block
 *
 * @package    Morning\WC\Gateways\Blocks
 * @subpackage Credit_Card_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.3.0
 * @since      1.3.0
 */

namespace Morning\WC\Gateways\Blocks;

use Morning\WC\Abstracts\Payment_Gateway_Block;
use Morning\WC\Gateways\Credit_Card_Gateway;

defined( 'ABSPATH' ) || exit;


/**
 * Class Credit_Card_Gateway_Block
 *
 * @package Morning\WC\Gateways\Blocks
 */
class Credit_Card_Gateway_Block extends Payment_Gateway_Block {
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->gateway       = new Credit_Card_Gateway( false );
		$this->block_scripts = [
			[
				'id'   => MRN_WC_SLUG . '-gateway-credit-card',
				'file' => MRN_WC_URL . 'assets/js/blocks/credit-card.min.js',
			],
		];

		parent::__construct();
	}
}
