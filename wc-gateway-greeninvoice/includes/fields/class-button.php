<?php
/**
 * Class Button
 *
 * @package    Morning\WC\Fields
 * @subpackage Button
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.4.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Button
 *
 * @package Morning\WC\Fields
 */
class Button extends Base_Settings_Field {
	/**
	 * @var string|null
	 *
	 * @since 1.4.0
	 */
	protected ?string $action;
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected string $button_label;


	/**
	 * Button constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string $label Field label text.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $section_id, string $id, string $label, ?string $value = null, ?string $name = null, array $options = [] ) {
		$this->type         = 'button';
		$this->action       = $options['action'] ?? '';
		$this->button_label = $options['button_label'] ?? $label;

		$this->css_classes[] = 'button';
		$this->css_classes[] = 'morning-button';

		parent::__construct( $section_id, $id, $label, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<button type="%1$s" id="%2$s" class="%5$s" data-action="%6$s"%4$s>%3$s</button>',
			$this->normalize_type( $this->type ),
			$this->normalize_id( $this->id ),
			$this->sanitize_label( $this->button_label ),
			$this->is_disabled() . $this->is_readonly(),
			$this->normalize_css_classes( $this->css_classes ),
			$this->sanitize_action( $this->action )
		);
		// @phpcs:enable
	}


	/**
	 * @param string $action Action type.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	protected function sanitize_action( string $action ): string {
		return esc_html( $action );
	}
}
