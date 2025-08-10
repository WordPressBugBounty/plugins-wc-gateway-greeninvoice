<?php
/**
 * Class Gateway_Selection
 *
 * @package    Morning\WC\Fields
 * @subpackage Gateway_Selection
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.2.0
 * @since      2.2.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Gateway_Selection
 *
 * @package Morning\WC\Fields
 */
class Gateway_Selection extends Base_Settings_Field {
	/**
	 * Gateway_Selection constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string $label Field label.
	 * @param array|null $value Field label.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 2.2.0
	 */
	public function __construct( string $section_id, string $id, string $label, ?array $value = null, ?string $name = null, array $options = [] ) {
		$this->type = 'gateway_selection';

		parent::__construct( $section_id, $id, $label, $value, $name, $options );
	}


	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function get_gateways(): array {
		return WC()->payment_gateways()->get_available_payment_gateways();
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<fieldset class="morning-gateways-selection-wrapper">';

		foreach ( $this->get_gateways() as $gateway ) {
			( new Checkbox(
				$this->section_id,
				"{$this->id}_{$gateway->id}",
				$gateway->get_title(),
				$this->value[ $gateway->id ] ?? false,
				"{$this->name}[{$gateway->id}]",
				[
					'css_classes'   => [ 'morning-payment-gateway' ],
					'override_name' => true,
				]
			) )->render_field();

			echo '<br>';
		}

		echo '</fieldset>';
		// @phpcs:enable
	}
}
