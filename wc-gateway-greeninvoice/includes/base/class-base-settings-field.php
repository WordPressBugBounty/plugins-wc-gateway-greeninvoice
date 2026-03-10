<?php
/**
 * Class Base_Settings_Field
 *
 * @package    Morning\WC\Fields
 * @subpackage Base_Settings_Field
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.0
 * @since      1.2.0
 */

namespace Morning\WC\Base;

use Morning\WC\Config\Options;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Settings_Field
 *
 * @package Morning\WC\Fields
 */
abstract class Base_Settings_Field {
	/**
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected string $type;
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected string $section_id;
	/**
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected string $id;
	/**
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected string $name;
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected ?string $label;
	/**
	 * @var string|array|null
	 *
	 * @since 1.2.0
	 */
	protected $value;
	/**
	 * @var bool
	 *
	 * @since 1.2.0
	 */
	protected bool $disabled = false;
	/**
	 * @var bool
	 *
	 * @since 1.2.2
	 */
	protected bool $readonly = false;
	/**
	 * @var array
	 *
	 * @since 1.2.0
	 */
	protected array $css_classes = [];
	/**
	 * @var string|null
	 *
	 * @since 2.0.0
	 */
	protected ?string $description = null;
	/**
	 * @var array|null
	 *
	 * @since 2.3.0
	 */
	protected ?array $options = null;


	/**
	 * Settings_Field constructor.
	 *
	 * @param string $section_id Section id.
	 * @param string $id Field id.
	 * @param string|null $label Field label.
	 * @param mixed $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $section_id, string $id, ?string $label, $value = null, ?string $name = null, array $options = [] ) {
		$this->section_id = $section_id;
		$this->id         = $id;
		$this->label      = $label;
		$this->value      = $value;
		$this->name       = true === ( $options['override_name'] ?? false ) ? $name : Options::OPTIONS_KEY . "[{$name}]";
		$this->disabled   = true === ( $options['disabled'] ?? false );
		$this->readonly   = true === ( $options['readonly'] ?? false );
		$this->options    = $options;

		if ( ! empty( $options['description'] ) ) {
			$this->description = $options['description'];
		}

		$this->css_classes[] = 'morning-field';
		$this->css_classes[] = "morning-field-{$this->type}";

		array_unshift( $this->css_classes, ...$options['css_classes'] ?? [] );
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function render_field(): void {
		do_action( "morning/wc/before_{$this->type}_field_output" );

		$this->html();

		if ( ! empty( $this->get_description() ) ) {
			echo wp_kses_post( "<p class='description'>{$this->get_description()}</p>" );
		}

		do_action( "morning/wc/after_{$this->type}_field_output" );
	}


	/**
	 * @return void
	 *
	 * @since 1.2.0
	 */
	abstract protected function html(): void;


	/**
	 * @param string $id Field id to normalize.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function normalize_id( string $id ): string {
		return esc_attr( sanitize_text_field( $id ) );
	}

	/**
	 * @param string $name Field name to normalize.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function normalize_name( string $name ): string {
		return $this->normalize_id( $name );
	}

	/**
	 * @param string $type Field type to normalize.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function normalize_type( string $type ): string {
		return $this->normalize_id( $type );
	}

	/**
	 * @param array $classes Field CSS classes.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function normalize_css_classes( array $classes ): string {
		return esc_attr( implode( ' ', $classes ) );
	}

	/**
	 * @param string|null $value Field value.
	 *
	 * @return string|null
	 *
	 * @since 1.2.0
	 */
	protected function sanitize_value( ?string $value = null ): ?string {
		return esc_attr( $value );
	}

	/**
	 * @param string $label Field label.
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function sanitize_label( string $label ): string {
		return esc_html( $label );
	}

	/**
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function is_disabled(): string {
		return $this->disabled ? ' disabled="disabled"' : '';
	}

	/**
	 * @return string
	 *
	 * @since 1.2.2
	 */
	protected function is_readonly(): string {
		return $this->readonly ? ' readonly="readonly"' : '';
	}


	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @since 2.0.0
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_section_id(): string {
		return $this->section_id;
	}

	/**
	 * @param string $section_id
	 *
	 * @since 2.0.0
	 */
	public function set_section_id( string $section_id ): void {
		$this->section_id = $section_id;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @since 2.0.0
	 */
	public function set_id( string $id ): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @since 2.0.0
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
	}

	/**
	 * @return string|null
	 *
	 * @since 2.0.0
	 */
	public function get_label(): ?string {
		return $this->label;
	}

	/**
	 * @param string|null $label
	 *
	 * @since 2.0.0
	 */
	public function set_label( ?string $label ): void {
		$this->label = $label;
	}

	/**
	 * @return array|string|null
	 *
	 * @since 2.0.0
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param array|string|null $value
	 *
	 * @since 2.0.0
	 */
	public function set_value( $value ): void {
		$this->value = $value;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_css_classes(): array {
		return $this->css_classes;
	}

	/**
	 * @param array $css_classes
	 *
	 * @since 2.0.0
	 */
	public function set_css_classes( array $css_classes ): void {
		$this->css_classes = $css_classes;
	}

	/**
	 * @return string|null
	 *
	 * @since 2.0.0
	 */
	public function get_description(): ?string {
		return $this->description;
	}

	/**
	 * @param string|null $description
	 *
	 * @since 2.0.0
	 */
	public function set_description( ?string $description ): void {
		$this->description = $description;
	}
}
