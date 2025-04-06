<?php
/**
 * Class Checkbox
 *
 * @package    Morning\WC\Fields
 * @subpackage Checkbox
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.2.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Checkbox
 *
 * @package Morning\WC\Fields
 */
final class Checkbox extends Base_Settings_Field {
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected string $checkbox_text;


	/**
	 * Checkbox constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string $label Field label.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $section_id, string $id, string $label, ?string $value = null, ?string $name = null, array $options = [] ) {
		$this->type          = 'checkbox';
		$this->checkbox_text = $options['checkbox_text'] ?? $label;

		parent::__construct( $section_id, $id, $label, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<label for="%3$s"><input type="%1$s" name="%2$s" id="%3$s" value="yes" class="%6$s"%4$s%5$s> %7$s</label>',
			$this->normalize_type( $this->type ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->is_checked(),
			$this->is_disabled() . $this->is_readonly(),
			$this->normalize_css_classes( $this->css_classes ),
			$this->sanitize_label( $this->checkbox_text )
		);
		// @phpcs:enable
	}


	/**
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function is_checked(): string {
		return checked( $this->value, 'yes', false );
	}
}
