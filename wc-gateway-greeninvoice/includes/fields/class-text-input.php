<?php
/**
 * Class Text_Input
 *
 * @package    Morning\WC\Fields
 * @subpackage Text_Input
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.2.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Text_Input
 *
 * @package Morning\WC\Fields
 */
final class Text_Input extends Base_Settings_Field {
	/**
	 * Text_Input constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string $label Field label.
	 * @param string $type Field type.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $section_id, string $id, string $label, string $type = 'text', ?string $value = null, ?string $name = null, array $options = [] ) {
		if ( $this->is_valid_type( $type ) ) {
			$this->type = $type;
		}

		$this->css_classes[] = 'regular-text';

		parent::__construct( $section_id, $id, $label, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	public function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<input type="%1$s" name="%2$s" id="%3$s" value="%4$s" class="%5$s">',
			$this->normalize_type( $this->type ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->sanitize_value( $this->value ),
			$this->normalize_css_classes( $this->css_classes )
		);
		// @phpcs:enable
	}


	/**
	 * @param string $type Input type.
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	private function is_valid_type( string $type ): bool {
		return in_array( $type, [ 'text', 'password', 'email', 'tel', 'url', 'search', 'date', 'color' ], true );
	}
}
