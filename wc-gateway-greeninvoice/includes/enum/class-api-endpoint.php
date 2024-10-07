<?php
/**
 * Class API_Endpoint
 *
 * @package    Morning\WC\Enum
 * @subpackage API_Endpoint
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class API_Endpoint
 *
 * @package Morning\WC\Enum
 */
class API_Endpoint {
	/**
	 * Shop connection authentication.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.5.0 Will be removed in future version.
	 */
	const CONNECT = '/api/v1/plugins/woocommerce/auth';

	/**
	 * Request new payment url.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.5.0 Will be removed in future version.
	 */
	const PAYMENT_URL = '/api/v1/plugins/woocommerce/pay/url';
}
