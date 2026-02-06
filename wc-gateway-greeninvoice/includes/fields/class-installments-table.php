<?php
/**
 * Class Installments_Table
 *
 * @package    Morning\WC\Fields
 * @subpackage Installments_Table
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.0
 * @since      2.3.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Installments_Table
 *
 * @package Morning\WC\Fields
 *
 * @since 2.3.0
 */
final class Installments_Table extends Base_Settings_Field {
	/**
	 * Installments_Table constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string|null $label Field label.
	 * @param mixed $value Field value.
	 * @param string|null $name Field name.
	 *
	 * @since 2.3.0
	 */
	public function __construct( string $section_id, string $id, ?string $label, $value = null, ?string $name = null ) {
		$this->type = 'installments_table';

		parent::__construct( $section_id, $id, $label, $value, $name );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<fieldset class="morning-installments-table-wrapper">';

		( new Checkbox(
			$this->section_id,
			"{$this->id}_enabled",
			__( 'Enable advanced installments management', 'wc-gateway-greeninvoice' ),
			$this->value['enabled'] ?? false,
			"{$this->name}[enabled]",
			[ 'override_name' => true ]
		) )->render_field();

		$css_classes = 'yes' === $this->value['enabled'] ? '' : ' hidden';

		echo '<table class="morning-installments-table widefat striped' . $css_classes . '">';
		echo '<thead>';
		echo '<tr>';
		echo '<th></th>';
		echo '<th>' . __( 'Min Amount', 'wc-gateway-greeninvoice' ) . '</th>';
		echo '<th>' . __( 'Max Amount', 'wc-gateway-greeninvoice' ) . '</th>';
		echo '<th>' . __( 'Installments', 'wc-gateway-greeninvoice' ) . '</th>';
		echo '<th>' . __( 'Actions', 'wc-gateway-greeninvoice' ) . '</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		foreach ( $this->value['rows'] as $idx => $row ) {
			$this->table_row_html( $idx, $row );
		}

		echo '</tbody>';
		echo '<tfoot>';
		echo '<tr>';
		echo '<td colspan="5">';
		( new Button(
			$this->section_id,
			"{$this->id}_add_row",
			__( 'Add Row', 'wc-gateway-greeninvoice' ),
			null,
			null,
			[ 'action' => MRN_WC_SLUG . '_installments_add_row' ]
		) )->render_field();
		echo '</td>';
		echo '</tr>';
		echo '</tfoot>';
		echo '</table>';

		echo '<template id="morning-installments-row-template">';
		$this->table_row_html(
			time(),
			[
				'min'    => '',
				'max'    => '',
				'amount' => '',
			]
		);
		echo '</template>';

		echo '</fieldset>';
		// @phpcs:enable
	}


	/**
	 * @param int $idx Row index
	 * @param array $row Row data
	 *
	 * @return void
	 *
	 * @since 2.3.0
	 */
	private function table_row_html( int $idx, array $row ): void {
		echo '<tr>';
		echo '<td class="morning-drag-handle"><span class="dashicons dashicons-menu"></td>';
		echo '<td>';
		( new Text_Input(
			$this->section_id,
			"{$this->id}_rows_{$idx}_min",
			'',
			'number',
			$row['min'],
			"{$this->name}[rows][min][]",
			[
				'override_name' => true,
				'min'           => 0,
				'step'          => 0.01,
			]
		) )->render_field();
		echo '</td>';
		echo '<td>';
		( new Text_Input(
			$this->section_id,
			"{$this->id}_rows_{$idx}_max",
			'',
			'number',
			$row['max'],
			"{$this->name}[rows][max][]",
			[
				'override_name' => true,
				'min'           => 0,
				'step'          => 0.01,
			]
		) )->render_field();
		echo '</td>';
		echo '<td>';
		( new Text_Input(
			$this->section_id,
			"{$this->id}_rows_{$idx}_amount",
			'',
			'number',
			$row['amount'],
			"{$this->name}[rows][amount][]",
			[
				'override_name' => true,
				'min'           => 0,
				'max'           => 12,
				'step'          => 1,
			]
		) )->render_field();
		echo '</td>';
		echo '<td>';
		( new Button(
			$this->section_id,
			"{$this->id}_rows_{$idx}_remove",
			__( 'Delete', 'wc-gateway-greeninvoice' ),
			null,
			null,
			[ 'action' => MRN_WC_SLUG . '_installments_remove_row' ]
		) )->render_field();
		echo '</td>';
	}
}
