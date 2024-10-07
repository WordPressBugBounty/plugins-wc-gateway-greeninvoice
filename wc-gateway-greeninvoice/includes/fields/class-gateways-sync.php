<?php
/**
 * Class Gateways_Sync
 *
 * @package    Morning\WC\Fields
 * @subpackage Gateways_Sync
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.2
 * @since      1.2.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Abstracts\Settings_Field;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Enum\Setting;
use Morning\WC\Utilities\Settings;

defined( 'ABSPATH' ) || exit;


/**
 * Class Gateways_Sync
 *
 * @pakcage Morning\Fields
 */
class Gateways_Sync extends Settings_Field {
	/**
	 * Gateways_Sync constructor.
	 *
	 * @param string $id Field id.
	 * @param array|null $value Field label.
	 * @param string|null $name Field name.
	 * @param array $options Field additional details.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $id, ?array $value = null, ?string $name = null, array $options = [] ) {
		$this->type = 'gateways_sync';

		parent::__construct( $id, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<fieldset class="morning-gateways-sync-wrapper">';

		foreach ( Payment_Type::get_all() as $value ) {
			new Checkbox(
				"{$this->id}_{$value}",
				Payment_Type::get_label( $value ),
				$this->value[ $value ] ?? false,
				"{$this->name}[{$value}]",
				[
					'readonly'    => true,
					'css_classes' => [ 'morning-payment-gateway' ],
				]
			);

			echo '<br>';
		}

		echo '<br>';

		printf(
			'<button type="%1$s" name="%2$s" id="%3$s" class="%4$s"><span class="morning-sync-button-label">%5$s</span></button>',
			$this->normalize_type( 'button' ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->normalize_css_classes( [ 'button', 'button-secondary', 'morning-sync-button' ] ),
			esc_html__( 'Sync with Account', 'wc-gateway-greeninvoice' )
		);

		echo '</fieldset>';
		// @phpcs:enable
	}
}
