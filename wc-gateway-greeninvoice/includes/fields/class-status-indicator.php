<?php
/**
 * Class Status_Indicator
 *
 * @package    Morning\WC\Fields
 * @subpackage Status_Indicator
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.2.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Abstracts\Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Status_Indicator
 *
 * @package Morning\WC\Fields
 */
final class Status_Indicator extends Settings_Field {
	/**
	 * Field statuses.
	 *
	 * @var string[]
	 *
	 * @since 1.2.0
	 */
	protected $statuses = [];


	/**
	 * Status_Indicator constructor.
	 *
	 * @param string $id Field id.
	 * @param string|null $value Field value.
	 * @param string|null $name Field name.
	 * @param array $options Field additional options.
	 *
	 * @since 1.2.0
	 */
	public function __construct( string $id, ?string $value = null, ?string $name = null, array $options = [] ) {
		$this->statuses    = $options['status_list'] ?? [];
		$this->type        = 'hidden';
		$this->css_classes = [ 'morning-status-indicator' ];

		if ( empty( $value ) ) {
			$value = 'empty';
		}

		parent::__construct( $id, $value, $name, $options );
	}


	/**
	 * @inheritDoc
	 */
	protected function html(): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<span class="morning-status-indicator %1$s"> %2$s</span><input type="%5$s" name="%3$s" value="%1$s" id="%4$s">',
			$this->sanitize_value( $this->value ),
			$this->get_status_label( $this->value ),
			$this->normalize_name( $this->name ),
			$this->normalize_id( $this->id ),
			$this->normalize_type( $this->type )
		);
		// @phpcs:enable
	}


	/**
	 * Retrieve status label text.
	 *
	 * @param string $status Status code.
	 *
	 * @return string|null
	 *
	 * @since 1.2.0
	 */
	protected function get_status_label( string $status ): ?string {
		return esc_html( $this->statuses[ $status ] ?? null );
	}
}
