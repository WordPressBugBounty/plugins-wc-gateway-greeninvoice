<?php
/**
 * Deprecated Plugin Constants.
 */

if ( ! defined( 'GIWC_DEFAULT_COUNTRY' ) ) {
	/**
	 * @deprecated 1.2.0 Use `MRN_WC_DEFAULT_COUNTRY` instead.
	 */
	define( 'GIWC_DEFAULT_COUNTRY', 'IL' );
}

if ( ! defined( 'GIWC_IPN_COMPLETED' ) ) {
	/**
	 * @deprecated 1.2.0 Use `MRN_WC_IPN_COMPLETED` instead.
	 */
	define( 'GIWC_IPN_COMPLETED', false );
}

if ( ! defined( 'MRN_WC_IPN_COMPLETED' ) ) {
	/**
	 * @deprecated 1.2.2 Migrated to plugin setting.
	 */
	define( 'MRN_WC_IPN_COMPLETED', false );
}


// Backward Compatability.
if ( defined( 'GIWC_DEFAULT_COUNTRY' ) ) {
	define( 'MRN_WC_DEFAULT_COUNTRY', GIWC_DEFAULT_COUNTRY );
}
