<?php
/**
 * Class Sandbox_Notice
 *
 * @package    Morning\WC\Notices
 * @subpackage Sandbox_Notice
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
 * Class Sandbox_Notice
 *
 * @package Morning\WC\Notices
 */
final class Sandbox_Notice extends Base_Notice {
	/**
	 * @inheritDoc
	 */
	public function get_id(): string {
		return 'sandbox_mode';
	}


	/**
	 * @inheritDoc
	 */
	public function get_type(): string {
		return Notice_Type::WARNING;
	}


	/**
	 * @inheritDoc
	 */
	public function should_display(): bool {
		return $this->settings->get_options()->is_sandbox_mode();
	}


	/**
	 * @inheritDoc
	 */
	public function get_body(): string {
		return esc_html__( 'Attention! Sandbox mode is enabled for "Morning for WooCommerce". You can disable it from the advanced options below.', 'wc-gateway-greeninvoice' );
	}
}
