<?php
/**
 * Class Section
 *
 * @package    Morning\WC\Fields
 * @subpackage Section
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Fields;

use Morning\WC\Base\Base_Settings_Field;

defined( 'ABSPATH' ) || exit;


/**
 * Class Section
 *
 * @package Morning\WC\Fields
 */
final class Section extends Base_Settings_Field {
	/**
	 * Section constructor.
	 *
	 * @param string $id Field id.
	 * @param string $label Field label.
	 *
	 * @since 2.0.0
	 */
	public function __construct( string $id, string $label ) {
		$this->type = 'section';

		parent::__construct( $id, $id, $label );
	}
}
