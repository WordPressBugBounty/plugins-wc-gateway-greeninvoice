<?php
/**
 * Class Request_Flow
 *
 * @package    Morning\WC\Enum
 * @subpackage Request_Flow
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.3
 * @since      2.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Request_Flow
 *
 * @package Morning\WC\Enum
 */
class Request_Flow {
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const SINGLE_PAYMENT = 1;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CREATE_TOKEN = 2;
	/**
	 * @int int
	 *
	 * @since 2.0.0
	 */
	const CHARGE_TOKEN = 3;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	const CREATE_DOCUMENT = 4;
	/**
	 * @var int
	 *
	 * @since 2.0.3
	 */
	const CANCEL_DOCUMENT = 5;
}
