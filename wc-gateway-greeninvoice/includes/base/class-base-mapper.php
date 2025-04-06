<?php
/**
 * Interface Base_Mapper
 *
 * @package    Morning\WC\Mappers
 * @subpackage Base_Mapper
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Base;

use WC_Order;

defined( 'ABSPATH' ) || exit;


/**
 * Interface Base_Mapper
 *
 * @package Morning\WC\Mappers
 */
interface Base_Mapper {
	/**
	 * @param WC_Order $order Order details.
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public static function map( WC_Order $order ): array;
}
