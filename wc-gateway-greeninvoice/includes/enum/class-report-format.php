<?php
/**
 * Class Report_Format
 *
 * @package    Morning\WC\Enum
 * @subpackage Report_Format
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Report_Format
 *
 * @package Morning\WC\Enum
 */
final class Report_Format {
	/**
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const MARKDOWN = 'markdown';
	/**
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const JSON = 'json';
}
