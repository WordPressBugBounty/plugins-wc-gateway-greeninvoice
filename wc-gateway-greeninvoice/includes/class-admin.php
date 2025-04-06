<?php
/**
 * Class Admin
 *
 * @package    Morning\WC
 * @subpackage Admin
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.2.0
 */

namespace Morning\WC;

use Morning\WC\Config\Settings;
use Morning\WC\Enum\Document_Type;
use WC_Order;
use WP_Post;

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
		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );

		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ], 10, 2 );

		add_filter( 'plugin_action_links_' . plugin_basename( MRN_WC_FILE ), [ $this, 'register_plugin_links' ] );
	}


	/**
	 * @param string $screen Current screen id.
	 *
	 * @since 1.0.0
	 */
	public function register_assets( string $screen ): void {
		wp_register_style( MRN_WC_SLUG . '-backend', MRN_WC_URL . 'assets/css/backend.css', [], MRN_WC_VERSION );

		wp_register_script( MRN_WC_SLUG . '-backend', MRN_WC_URL . 'assets/js/backend.js', [ 'jquery' ], MRN_WC_VERSION, true );

		$this->localize_strings();
		$this->print_variables();

		if ( $this->is_order_screen( $screen ) || $this->is_plugin_settings_screen( $screen ) ) {
			wp_enqueue_style( MRN_WC_SLUG . '-backend' );
			wp_enqueue_script( MRN_WC_SLUG . '-backend' );
		}
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function register_settings_page(): void {
		add_menu_page(
			_x( 'Morning Settings', 'Settings Page Title', 'wc-gateway-greeninvoice' ),
			_x( 'Morning', 'Settings Page Menu Title', 'wc-gateway-greeninvoice' ),
			'manage_options',
			MRN_WC_SLUG,
			[ Settings::class, 'render_settings_page' ],
			'data:image/svg+xml;base64,PHN2ZyBkYXRhLXYtZGI3ZmQwNjg9IiIgZGF0YS12LTFjYzFkMGU1PSIiIGRhdGEtdi03N2JkZmRmMD0iIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgcm9sZT0icHJlc2VudGF0aW9uIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIGFyaWEtbGFiZWxsZWRieT0ibW9ybmluZyIgdHJhbnNmb3JtPSIiIGNsYXNzPSJpY29uIj48ZyBkYXRhLXYtZGI3ZmQwNjg9IiIgZmlsbD0iY3VycmVudENvbG9yIiBpZD0ibW9ybmluZyI+PHBhdGggZGF0YS12LTFjYzFkMGU1PSIiIGRhdGEtdi1kYjdmZDA2OD0iIiBkPSJNMTAuNjg2NyA2Ljg3MzMzTDYuNjI2NjcgMi44MTMzM0M2LjE0IDIuMzEzMzMgNS40NTMzMyAyIDQuNyAyQzMuMjA2NjcgMiAyIDMuMjA2NjcgMiA0LjdWOC44QzIgMTAuMjkzMyAzLjIwNjY3IDExLjUgNC43IDExLjVIOC44QzEwLjI5MzMgMTEuNSAxMS41IDEwLjI5MzMgMTEuNSA4LjhDMTEuNSA4LjA0NjY3IDExLjE4NjcgNy4zNiAxMC42ODY3IDYuODczMzNaTTEyLjUgOC44QzEyLjUgMTAuMjkzMyAxMy43MDY3IDExLjUgMTUuMiAxMS41SDE5LjNDMjAuNzkzMyAxMS41IDIyIDEwLjI5MzMgMjIgOC44VjQuN0MyMiAzLjIwNjY3IDIwLjc5MzMgMiAxOS4zIDJDMTguNTQ2NyAyIDE3Ljg2IDIuMzEzMzMgMTcuMzczMyAyLjgxMzMzTDEzLjMxMzMgNi44NzMzM0MxMi44MTMzIDcuMzYgMTIuNSA4LjA0NjY3IDEyLjUgOC44Wk04LjggMTIuNUg0LjdDMy4yMDY2NyAxMi41IDIgMTMuNzA2NyAyIDE1LjJWMTkuM0MyIDIwLjc5MzMgMy4yMDY2NyAyMiA0LjcgMjJIOC44QzEwLjI5MzMgMjIgMTEuNSAyMC43OTMzIDExLjUgMTkuM1YxNS4yQzExLjUgMTMuNzA2NyAxMC4yOTMzIDEyLjUgOC44IDEyLjVaTTE5LjMgMTIuNUgxNS4yQzEzLjcwNjcgMTIuNSAxMi41IDEzLjcwNjcgMTIuNSAxNS4yVjE5LjNDMTIuNSAyMC43OTMzIDEzLjcwNjcgMjIgMTUuMiAyMkgxOS4zQzIwLjc5MzMgMjIgMjIgMjAuNzkzMyAyMiAxOS4zVjE1LjJDMjIgMTMuNzA2NyAyMC43OTMzIDEyLjUgMTkuMyAxMi41WiI+PC9wYXRoPjwvZz48L3N2Zz4=',
			100
		);
	}

	/**
	 * @param string $post_type Post type.
	 * @param WP_Post|int $post_or_order Post id or order id.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register_meta_boxes( string $post_type, $post_or_order ): void {
		$order_id = ( $post_or_order instanceof WP_Post ) ? $post_or_order->ID : $post_or_order;
		$order    = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( empty( $order->get_meta( MRN_WC_SLUG . '_data' ) ) ) {
			return;
		}

		add_meta_box(
			MRN_WC_SLUG . '-invoice-metabox',
			esc_html_x( 'Morning Document Info', 'Admin Meta Box', 'wc-gateway-greeninvoice' ),
			[ $this, 'document_details_metabox_output' ],
			wc_get_page_screen_id( 'shop-order' ),
			'side'
		);
	}

	/**
	 * @param WP_Post|int $post_or_order Post id or order id.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function document_details_metabox_output( $post_or_order ): void {
		$order_id = ( $post_or_order instanceof WP_Post ) ? $post_or_order->ID : $post_or_order;
		$order    = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$order_meta = $order->get_meta( MRN_WC_SLUG . '_data' );

		if ( empty( $order_meta ) || empty( $order_meta['document_id'] ) ) {
			return;
		}

		$order_meta_labels = [
			'document_id' => esc_html__( 'Document Number', 'wc-gateway-greeninvoice' ),
			'type'        => esc_html__( 'Document Type', 'wc-gateway-greeninvoice' ),
			'id'          => esc_html__( 'Document ID', 'wc-gateway-greeninvoice' ),
		];

		$order_meta['type'] = sprintf( '%s (%d)', Document_Type::get_type( $order_meta['type'] ), $order_meta['type'] );

		require_once MRN_WC_PATH . 'templates/admin/order-metabox.php';
	}


	/**
	 * @param array $actions Plugin default links.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_links( array $actions ): array {
		$settings_page_link = admin_url( 'admin.php?page=' . MRN_WC_SLUG );
		$settings_link_text = esc_attr_x( 'Settings', 'Plugins Links', 'wc-gateway-greeninvoice' );

		$custom_actions = [ "<a href='{$settings_page_link}'>{$settings_link_text}</a>" ];

		return array_merge( $custom_actions, $actions );
	}


	/**
	 * @param string $notice Notice text.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function print_notice( string $notice ): void {
		$notice = wpautop( $notice );

		echo wp_kses_post( "<div class='morning-notice error'>{$notice}</div>" );
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function localize_strings(): void {
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
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function print_variables(): void {
		wp_localize_script(
			MRN_WC_SLUG . '-backend',
			MRN_WC_SLUG . '_vars',
			[
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'sync_nonce'                => wp_create_nonce( 'morning-sync-gateways' ),
				'download_debug_file_nonce' => wp_create_nonce( 'morning-download-debug-file' ),
			]
		);
	}


	/**
	 * @param string $screen Admin screen id.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	private function is_order_screen( string $screen ): bool {
		return 'woocommerce_page_wc-orders--shop-order' === $screen || 'shop-order' === $screen;
	}

	/**
	 * @param string $screen Admin screen id.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	private function is_plugin_settings_screen( string $screen ): bool {
		return false !== strpos( $screen, MRN_WC_SLUG );
	}
}
