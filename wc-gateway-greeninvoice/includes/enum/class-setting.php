<?php
/**
 * Class Setting
 *
 * @package    Morning\WC\Enum
 * @subpackage Setting
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.0.0
 */

namespace Morning\WC\Enum;

defined( 'ABSPATH' ) || exit;


/**
 * Class Setting
 *
 * @package Morning\WC\Enum
 */
class Setting {
	/**
	 * Database option key for plugin activation status.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const ACTIVATED = MRN_WC_SLUG . '_activated';

	/**
	 * Database option key for license key setting.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const LICENSE_KEY = MRN_WC_SLUG . '_license_key';

	/**
	 * Database option key for sync setting.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	const GATEWAYS = MRN_WC_SLUG . '_gateways';

	/**
	 * Database option key for debugging setting.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const DEBUGGING = MRN_WC_SLUG . '_debugging';

	/**
	 * Database option key whether to display TAX ID field.
	 *
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const SHOW_TAX_ID_FIELD = MRN_WC_SLUG . '_tax_id_field';

	/**
	 * Database option key for sandbox setting.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const SANDBOX = MRN_WC_SLUG . '_sandbox';

	/**
	 * Order meta key for IPN data.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const ORDER_META = MRN_WC_SLUG . '_data';

	/**
	 * Order meta key for refund data.
	 *
	 * @var string
	 *
	 * @since 1.5.0
	 */
	const ORDER_REFUND_META = MRN_WC_SLUG . '_refund_data';

	/**
	 * Subscription meta key for token id.
	 *
	 * @var string
	 *
	 * @since 1.6.0
	 */
	const SUBSCRIPTION_TOKEN_ID = MRN_WC_SLUG . '_subscription_token_id';

	/**
	 * Order status after successful payment.
	 *
	 * @var string
	 *
	 * @since 1.2.2
	 */
	const ORDER_STATUS = MRN_WC_SLUG . '_order_status';


	/**
	 * Download debugging data.
	 *
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const DOWNLOAD_DEBUG_DATA = MRN_WC_SLUG . '_download_debug_data';


	/**
	 * Plugin database version.
	 *
	 * @var string
	 *
	 * @since 1.2.0
	 */
	const DB_VERSION = MRN_WC_SLUG . '_db_version';


	/**
	 * Database option key for plugin activation status.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.2.0 Simplified settings page.
	 */
	const ACTIVATED_SB = MRN_WC_SLUG . '_activated_sandbox';

	/**
	 * Database option key for license key setting.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.2.0 Simplified settings page.
	 */
	const LICENSE_KEY_SB = MRN_WC_SLUG . '_license_key_sandbox';
}
