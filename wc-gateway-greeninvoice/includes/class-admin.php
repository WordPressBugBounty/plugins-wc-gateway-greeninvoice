<?php
/**
 * Class Admin
 *
 * @package    Morning\WC
 * @subpackage Admin
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.2.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;


/**
 * Class Admin
 *
 * @package Morning\WC;
 */
class Admin {
	/**
	 * Admin constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );

		add_filter( 'plugin_action_links_' . plugin_basename( MRN_WC_FILE ), [ $this, 'register_plugin_links' ] );
	}


	/**
	 * Register backend stylesheets and scripts.
	 *
	 * @param string $screen Current screen id.
	 *
	 * @since 1.0.0
	 * @version 1.2.0
	 */
	public function register_assets( string $screen ): void {
		global $post_id;

		wp_register_style( MRN_WC_SLUG . '-backend', MRN_WC_URL . 'assets/css/backend.min.css', [], MRN_WC_VERSION );
		wp_register_script( MRN_WC_SLUG . '-backend', MRN_WC_URL . 'assets/js/backend.min.js', [ 'jquery' ], MRN_WC_VERSION, true );

		wp_localize_script(
			MRN_WC_SLUG . '-backend',
			MRN_WC_SLUG . '_i18n',
			[
				'syncing'        => esc_html__( 'Syncing', 'wc-gateway-greeninvoice' ),
				'synced'         => esc_html__( 'Synced', 'wc-gateway-greeninvoice' ),
				'sync_error'     => esc_html__( 'Unable to Sync', 'wc-gateway-greeninvoice' ),
				'status_success' => esc_html__( 'Active', 'wc-gateway-greeninvoice' ),
				'status_error'   => esc_html__( 'Activation Error', 'wc-gateway-greeninvoice' ),
			]
		);

		wp_localize_script(
			MRN_WC_SLUG . '-backend',
			MRN_WC_SLUG . '_vars',
			[
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'sync_nonce'                => wp_create_nonce( 'morning-sync-gateways' ),
				'download_debug_file_nonce' => wp_create_nonce( 'morning-download-debug-file' ),
			]
		);

		if (
			false !== strpos( $screen, MRN_WC_SLUG ) ||
			OrderUtil::is_order( $post_id ) ||
			wc_get_page_screen_id( 'shop-order' ) === $screen
		) {
			wp_enqueue_style( MRN_WC_SLUG . '-backend' );
			wp_enqueue_script( MRN_WC_SLUG . '-backend' );
		}
	}


	/**
	 * Register plugin essential links.
	 *
	 * @param array $actions Plugin default links.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 * @version 1.2.0
	 */
	public function register_plugin_links( array $actions ): array {
		$custom_actions = [];

		$custom_actions[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'admin.php?page=' . MRN_WC_SLUG ),
			esc_attr_x( 'Settings', 'Plugins Links', 'wc-gateway-greeninvoice' )
		);

		return array_merge( $custom_actions, $actions );
	}
}
