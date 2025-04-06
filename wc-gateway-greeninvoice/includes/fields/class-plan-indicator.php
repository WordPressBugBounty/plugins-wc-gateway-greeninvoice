<?php
/**
 * Class Plan_Indicator
 *
 * @package    Morning\WC\Fields
 * @subpackage Plan_Indicator
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;
use Morning\WC\Enum\Plan_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Plan_Indicator
 *
 * @package Morning\WC\Fields
 */
class Plan_Indicator extends Base_Settings_Field {
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected array $plans;


	/**
	 * Plan_Indicator constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string $label Field label.
	 * @param int|null $value Field value.
	 *
	 * @since 2.0.0
	 */
	public function __construct( string $section_id, string $id, string $label, ?int $value = null ) {
		$this->type  = 'plan_indicator';
		$this->plans = [
			Plan_Type::BASIC     => Plan_Type::get_label( Plan_Type::CLEARING ),
			Plan_Type::CLEARING  => Plan_Type::get_label( Plan_Type::CLEARING ),
			Plan_Type::INVOICING => Plan_Type::get_label( Plan_Type::INVOICING ),
		];

		parent::__construct( $section_id, $id, $label, $value, $id );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<span class="morning-plan-indicator plan-%1$s">%2$s</span><input type="hidden" name="%3$s" value="%1$s" id="%4$s">',
			$this->sanitize_value( $this->value ),
			$this->get_plan_label( $this->value ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id )
		);
		// @phpcs:enable
	}


	/**
	 * @param string $plan Plan type.
	 *
	 * @return string|null
	 *
	 * @since 2.0.0
	 */
	protected function get_plan_label( string $plan ): ?string {
		return esc_html( $this->plans[ $plan ] ?? null );
	}
}
