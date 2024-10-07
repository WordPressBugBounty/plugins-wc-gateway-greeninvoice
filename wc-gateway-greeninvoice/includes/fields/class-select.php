<?php
/**
 * Class Select
 *
 * @package    Morning\WC\Fields
 * @subpackage Select
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.2
 * @since      1.2.2
 */

namespace Morning\WC\Fields;

use Morning\WC\Abstracts\Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Select
 *
 * @package Morning\WC\Fields
 */
final class Select extends Settings_Field {
	/**
	 * @inheritDoc
	 */
	protected $type = 'select';

	/**
	 * Field value options list.
	 *
	 * @var string[]
	 *
	 * @since 1.2.2
	 */
	private $options = [];


	/**
	 * Text_Input constructor.
	 *
	 * @param string $id Field id.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $id, ?string $value = null, ?string $name = null, array $options = [] ) {
		$this->options = $options['values'];

		parent::__construct( $id, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	public function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<select name="%1$s" id="%2$s" class="%3$s"%5$s>%4$s</select>',
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->normalize_css_classes( $this->css_classes ),
			$this->build_field_options_html( $this->options, $this->value ),
			$this->is_disabled()
		);
		// @phpcs:enable
	}


	/**
	 * Generates field options HTML.
	 *
	 * @param array $options Field options.
	 * @param string|null $selected_value Selected value.
	 *
	 * @return string
	 *
	 * @since 1.2.2
	 */
	private function build_field_options_html( array $options, ?string $selected_value = null ): string {
		return array_reduce(
			array_keys( $options ),
			function ( $carry, $key ) use ( $options, $selected_value ) {
				$carry .= sprintf(
					'<option value="%1$s"%3$s>%2$s</option>',
					$key,
					$options[ $key ],
					selected( $selected_value, $key, false )
				);

				return $carry;
			}
		);
	}
}
