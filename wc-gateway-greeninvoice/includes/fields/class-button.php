<?php
/**
 * Class Settings
 *
 * @package    Morning\WC\Fields
 * @subpackage Button
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Abstracts\Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Button
 *
 * @package Morning\WC\Fields
 */
class Button extends Settings_Field {
	/**
	 * Button label text.
	 *
	 * @var string
	 *
	 * @since 1.4.0
	 */
	protected $label;

	/**
	 * Button action type.
	 *
	 * @var string|null
	 *
	 * @since 1.4.0
	 */
	protected $action;


	/**
	 * Button constructor.
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
		$this->type        = 'button';
		$this->label       = $label;
		$this->action      = $options['action'] ?? '';
		$this->css_classes = [ 'button', 'button-secondary' ];

		parent::__construct( $id, $value, $name, $options );
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
			$this->sanitize_label( $this->label ),
			$this->is_disabled() . $this->is_readonly(),
			$this->normalize_css_classes( $this->css_classes ),
			$this->sanitize_action( $this->action )
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
	 * @since 1.4.0
	 */
	protected function sanitize_label( string $label ): string {
		return esc_html( $label );
	}

	/**
	 * Sanitize action type.
	 *
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
