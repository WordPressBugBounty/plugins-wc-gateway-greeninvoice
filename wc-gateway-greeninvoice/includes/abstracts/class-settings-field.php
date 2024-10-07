<?php
/**
 * Class Settings_Field
 *
 * @package    Morning\WC\Fields
 * @subpackage Settings_Field
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.2
 * @since      1.2.0
 */

namespace Morning\WC\Abstracts;

defined( 'ABSPATH' ) || exit;


/**
 * Class Settings_Field
 *
 * @package Morning\WC\Fields
 */
abstract class Settings_Field {
	/**
	 * Field id.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected $id;

	/**
	 * Field name.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected $name;

	/**
	 * Field type.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	protected $type;

	/**
	 * Field value.
	 *
	 * @var string|null
	 *
	 * @since 1.2.0
	 */
	protected $value;

	/**
	 * Should field be disabled?
	 *
	 * @var bool
	 *
	 * @since 1.2.0
	 */
	protected $disabled = false;

	/**
	 * Should field be readonly?
	 *
	 * @var bool
	 *
	 * @since 1.2.2
	 */
	protected $readonly = false;

	/**
	 * Field CSS classes.
	 *
	 * @var array
	 *
	 * @since 1.2.0
	 */
	protected $css_classes = [];


	/**
	 * Settings_Field constructor.
	 *
	 * @param string $id Field id.
	 * @param string|array|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $id, $value = null, ?string $name = null, array $options = [] ) {
		$this->id       = $id;
		$this->name     = $name ?? $id;
		$this->value    = $value;
		$this->disabled = ! empty( $options['disabled'] ) && true === $options['disabled'];
		$this->readonly = ! empty( $options['readonly'] ) && true === $options['readonly'];

		array_unshift( $this->css_classes, ...$options['css_classes'] ?? [] );

		$this->print();
	}


	/**
	 * Render HTML tag for field.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public function print(): void {
		do_action( "morning/wc/before_{$this->type}_field_output" );

		$this->html();

		do_action( "morning/wc/after_{$this->type}_field_output" );
	}


	/**
	 * Build field HTML tag.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 */
	protected function html(): void {
		// Field HTML tag format.
	}


	/**
	 * Normalize and sanitize field id attribute value.
	 *
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
	 * Normalize and sanitize field name attribute value.
	 *
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
	 * Normalize and sanitize field type attribute value.
	 *
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
	 * Normalize and sanitize field CSS classes.
	 *
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
	 * Sanitize field value.
	 *
	 * @param string|null $value
	 *
	 * @return string|null
	 *
	 * @since 1.2.0
	 */
	protected function sanitize_value( ?string $value = null ): ?string {
		return esc_attr( $value );
	}

	/**
	 * Print `disabled` attribute in case field is disabled.
	 *
	 * @return string
	 *
	 * @since 1.2.0
	 */
	protected function is_disabled(): string {
		return $this->disabled ? ' disabled="disabled"' : '';
	}

	/**
	 * Print `readonly` attribute in case field is disabled.
	 *
	 * @return string
	 *
	 * @since 1.2.2
	 */
	protected function is_readonly(): string {
		return $this->readonly ? ' readonly="readonly"' : '';
	}
}
