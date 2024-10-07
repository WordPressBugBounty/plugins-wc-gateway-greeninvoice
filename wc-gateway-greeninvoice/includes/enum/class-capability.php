<?php
/**
 * Class Capability
 *
 * @package    Morning\WC\Enum
 * @subpackage Capability
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.0
 * @since      1.2.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Capability
 *
 * @package Morning\WC\Enum
 */
class Capability {
	/**
	 * @since 1.2.0
	 */
	const INSTALLMENTS = 'installments';

	/**
	 * @since 1.2.0
	 */
	const IFRAME_FORM = 'iframe_form';

	/**
	 * @since 1.6.0
	 */
	const TOKENIZATION = 'tokenization';
}
