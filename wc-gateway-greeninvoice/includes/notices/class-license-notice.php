<?php
/**
 * Class License_Notice
 *
 * @package    Morning\WC\Notices
 * @subpackage License_Notice
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.0
 * @since      2.4.0
 */

namespace Morning\WC\Notices;

use Morning\WC\Base\Base_Notice;
use Morning\WC\Enum\Notice_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class License_Notice
 *
 * @package Morning\WC\Notices
 */
final class License_Notice extends Base_Notice {
	/**
	 * @inheritDoc
	 */
	public function get_id(): string {
		return 'license_activation';
	}


	/**
	 * @inheritDoc
	 */
	public function get_type(): string {
		return Notice_Type::ERROR;
	}


	/**
	 * @inheritDoc
	 */
	public function should_display(): bool {
		return ! $this->settings->get_options()->is_license_valid();
	}


	/**
	 * @inheritDoc
	 */
	public function get_body(): string {
		return esc_html__( 'Please activate the plugin by entering your license key for "Morning for WooCommerce".', 'wc-gateway-greeninvoice' );
	}
}
