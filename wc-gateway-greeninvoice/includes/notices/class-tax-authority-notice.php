<?php
/**
 * Class Tax_Authority_Notice
 *
 * @package    Morning\WC\Notices
 * @subpackage Tax_Authority_Notice
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
 * Class Tax_Authority_Notice
 *
 * @package Morning\WC\Notices
 */
final class Tax_Authority_Notice extends Base_Notice {
	/**
	 * @inheritDoc
	 */
	public function get_id(): string {
		return 'tax_authority';
	}


	/**
	 * @inheritDoc
	 */
	public function get_type(): string {
		return Notice_Type::INFO;
	}


	/**
	 * @inheritDoc
	 */
	public function is_dismissible(): bool {
		return true;
	}


	/**
	 * @inheritDoc
	 */
	public function should_display(): bool {
		return $this->settings->get_options()->is_license_valid();
	}


	/**
	 * @inheritDoc
	 */
	public function get_body(): string {
		$intro = sprintf(
			'<strong>%s</strong> %s',
			esc_html__( 'Important to know:', 'wc-gateway-greeninvoice' ),
			esc_html__( 'Starting June 1, 2026, you must obtain an allocation number from the Tax Authority for every invoice over ₪5,000 so that your customer can deduct VAT for it.', 'wc-gateway-greeninvoice' )
		);

		$details = sprintf(
			'%s <strong>%s</strong>',
			esc_html__( 'Allocation numbers are received only for invoices that include the customer\'s business number / Tax ID,', 'wc-gateway-greeninvoice' ),
			esc_html__( 'so make sure to enable the fields in the Tax ID setting later on this page.', 'wc-gateway-greeninvoice' )
		);

		$link = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( 'https://www.greeninvoice.co.il/help-center/ecommerce-tax-auth/' ),
			esc_html__( 'Read the full guide', 'wc-gateway-greeninvoice' )
		);

		return "<p>{$intro}</p><p>{$details}</p><p>{$link}</p>";
	}
}
