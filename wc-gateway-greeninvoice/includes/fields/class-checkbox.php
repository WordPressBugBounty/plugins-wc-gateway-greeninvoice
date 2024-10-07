<?php
/**
 * Class Checkbox
 *
 * @package    Morning\WC\Fields
 * @subpackage Checkbox
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.2
 * @since      1.2.0
 */


namespace Morning\WC\Fields;

use Morning\WC\Abstracts\Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Checkbox
 *
 * @package Morning\WC\Fields
 */
final class Checkbox extends Settings_Field {
	/**
	 * Checkbox label text.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected $label;


	/**
	 * Checkbox constructor.
	 *
	 * @param string $id Field id
	 * @param string $label Field label text.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $id, string $label, ?string $value = null, ?string $name = null, array $options = [] ) {
		$this->type  = 'checkbox';
		$this->label = $label;

		parent::__construct( $id, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<label for="%3$s"><input type="%1$s" name="%2$s" id="%3$s" value="1" class="%6$s"%4$s%5$s> %7$s</label>',
			$this->normalize_type( $this->type ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->is_checked(),
			$this->is_disabled() . $this->is_readonly(),
			$this->normalize_css_classes( $this->css_classes ),
			$this->sanitize_label( $this->label )
		);
		// @phpcs:enable
	}


	/**
	 * Sanitize label text.
	 *
	 * @param string $label Label text.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function sanitize_label( string $label ): string {
		return esc_html( $label );
	}

	/**
	 * Print `checked` attribute in case field is checked.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function is_checked(): string {
		return checked( $this->value, 1, false );
	}
}
