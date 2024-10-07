<?php
/**
 * Class Settings
 *
 * @package    Morning\WC\Utilities
 * @subpackage Settings
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.0.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Fields\Button;
use Morning\WC\Fields\Checkbox;
use Morning\WC\Fields\Gateways_Sync;
use Morning\WC\Fields\Select;
use Morning\WC\Fields\Status_Indicator;
use Morning\WC\Fields\Text_Input;
use Morning\WC\Enum\Setting;

defined( 'ABSPATH' ) || exit;


/**
 * Class Settings
 *
 * @package Morning\WC\Utilities
 */
class Settings {
	/**
	 * Options database key.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const OPTIONS_KEY = MRN_WC_SLUG . '_options';


	/**
	 * Settings constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_plugin_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_plugin_settings' ] );

		add_action( 'add_option_' . self::OPTIONS_KEY, [ $this, 'maybe_check_license' ], 10, 2 );
		add_action( 'update_option_' . self::OPTIONS_KEY, [ $this, 'maybe_check_license' ], 10, 2 );

		add_filter( 'pre_update_option_' . self::OPTIONS_KEY, [ $this, 'maybe_change_status' ] );

		$options = self::get_options();

		if ( is_array( $options ) && ! empty( $options[ Setting::SANDBOX ] ) ) {
			add_action( 'admin_notices', '\Morning\WC\Compatibility::sandbox_active' );
		}
	}


	/**
	 * Register plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_settings_page(): void {
		add_menu_page(
			_x( 'Morning Settings', 'Settings Page Title', 'wc-gateway-greeninvoice' ),
			_x( 'Morning', 'Settings Page Menu Title', 'wc-gateway-greeninvoice' ),
			'manage_options',
			MRN_WC_SLUG,
			[ $this, 'render_page' ],
			'data:image/svg+xml;base64,PHN2ZyBkYXRhLXYtZGI3ZmQwNjg9IiIgZGF0YS12LTFjYzFkMGU1PSIiIGRhdGEtdi03N2JkZmRmMD0iIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgcm9sZT0icHJlc2VudGF0aW9uIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIGFyaWEtbGFiZWxsZWRieT0ibW9ybmluZyIgdHJhbnNmb3JtPSIiIGNsYXNzPSJpY29uIj48ZyBkYXRhLXYtZGI3ZmQwNjg9IiIgZmlsbD0iY3VycmVudENvbG9yIiBpZD0ibW9ybmluZyI+PHBhdGggZGF0YS12LTFjYzFkMGU1PSIiIGRhdGEtdi1kYjdmZDA2OD0iIiBkPSJNMTAuNjg2NyA2Ljg3MzMzTDYuNjI2NjcgMi44MTMzM0M2LjE0IDIuMzEzMzMgNS40NTMzMyAyIDQuNyAyQzMuMjA2NjcgMiAyIDMuMjA2NjcgMiA0LjdWOC44QzIgMTAuMjkzMyAzLjIwNjY3IDExLjUgNC43IDExLjVIOC44QzEwLjI5MzMgMTEuNSAxMS41IDEwLjI5MzMgMTEuNSA4LjhDMTEuNSA4LjA0NjY3IDExLjE4NjcgNy4zNiAxMC42ODY3IDYuODczMzNaTTEyLjUgOC44QzEyLjUgMTAuMjkzMyAxMy43MDY3IDExLjUgMTUuMiAxMS41SDE5LjNDMjAuNzkzMyAxMS41IDIyIDEwLjI5MzMgMjIgOC44VjQuN0MyMiAzLjIwNjY3IDIwLjc5MzMgMiAxOS4zIDJDMTguNTQ2NyAyIDE3Ljg2IDIuMzEzMzMgMTcuMzczMyAyLjgxMzMzTDEzLjMxMzMgNi44NzMzM0MxMi44MTMzIDcuMzYgMTIuNSA4LjA0NjY3IDEyLjUgOC44Wk04LjggMTIuNUg0LjdDMy4yMDY2NyAxMi41IDIgMTMuNzA2NyAyIDE1LjJWMTkuM0MyIDIwLjc5MzMgMy4yMDY2NyAyMiA0LjcgMjJIOC44QzEwLjI5MzMgMjIgMTEuNSAyMC43OTMzIDExLjUgMTkuM1YxNS4yQzExLjUgMTMuNzA2NyAxMC4yOTMzIDEyLjUgOC44IDEyLjVaTTE5LjMgMTIuNUgxNS4yQzEzLjcwNjcgMTIuNSAxMi41IDEzLjcwNjcgMTIuNSAxNS4yVjE5LjNDMTIuNSAyMC43OTMzIDEzLjcwNjcgMjIgMTUuMiAyMkgxOS4zQzIwLjc5MzMgMjIgMjIgMjAuNzkzMyAyMiAxOS4zVjE1LjJDMjIgMTMuNzA2NyAyMC43OTMzIDEyLjUgMTkuMyAxMi41WiI+PC9wYXRoPjwvZz48L3N2Zz4=',
			100
		);
	}

	/**
	 * Declare plugin settings sections and fields.
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_settings(): void {
		register_setting( MRN_WC_SLUG, self::OPTIONS_KEY );

		// Licensing Section.
		add_settings_section(
			MRN_WC_SLUG . '_licensing',
			esc_html__( 'Licensing', 'wc-gateway-greeninvoice' ),
			'__return_null',
			MRN_WC_SLUG
		);

		add_settings_field(
			Setting::LICENSE_KEY,
			esc_html__( 'License Key', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_licensing',
			[
				'label_for' => Setting::LICENSE_KEY,
				'id'        => Setting::LICENSE_KEY,
				'type'      => 'text',
			]
		);

		add_settings_field(
			Setting::ACTIVATED,
			esc_html__( 'License Status', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_licensing',
			[
				'id'          => Setting::ACTIVATED,
				'type'        => 'status',
				'status_list' => [
					'empty' => __( 'Inactive', 'wc-gateway-greeninvoice' ),
					'yes'   => __( 'Active', 'wc-gateway-greeninvoice' ),
					'no'    => __( 'Activation Error', 'wc-gateway-greeninvoice' ),
				],
			]
		);

		add_settings_field(
			Setting::GATEWAYS,
			esc_html__( 'Allowed Gateways', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_general',
			[
				'id'          => Setting::GATEWAYS,
				'type'        => 'gateways_sync',
				'description' => __( 'If you changed your settings in WooCommerce on morning, you need to sync the changes.', 'wc-gateway-greeninvoice' ),
			]
		);

		// General Section.
		add_settings_section(
			MRN_WC_SLUG . '_general',
			esc_html__( 'General Options', 'wc-gateway-greeninvoice' ),
			'__return_null',
			MRN_WC_SLUG
		);

		add_settings_field(
			Setting::ORDER_STATUS,
			esc_html__( 'Order Status', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_general',
			[
				'id'          => Setting::ORDER_STATUS,
				'type'        => 'select',
				'description' => __( 'Set order status after a notification of successful payment received.', 'wc-gateway-greeninvoice' ),
				'values'      => [
					'processing' => __( 'Processing', 'wc-gateway-greeninvoice' ),
					'completed'  => __( 'Completed', 'wc-gateway-greeninvoice' ),
				],
			]
		);

		add_settings_field(
			Setting::SHOW_TAX_ID_FIELD,
			esc_html__( 'Tax ID Number', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_general',
			[
				'id'    => Setting::SHOW_TAX_ID_FIELD,
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable Tax ID number field in checkout', 'wc-gateway-greeninvoice' ),
			]
		);

		// Debugging Section.
		add_settings_section(
			MRN_WC_SLUG . '_debugging',
			esc_html__( 'Debugging Options', 'wc-gateway-greeninvoice' ),
			'__return_null',
			MRN_WC_SLUG
		);

		add_settings_field(
			Setting::DEBUGGING,
			esc_html__( 'Debugging', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_debugging',
			[
				'id'    => Setting::DEBUGGING,
				'type'  => 'checkbox',
				'label' => __( 'Enable logs', 'wc-gateway-greeninvoice' ),
			]
		);

		add_settings_field(
			Setting::SANDBOX,
			esc_html__( 'Sandbox', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_debugging',
			[
				'id'          => Setting::SANDBOX,
				'type'        => 'checkbox',
				'label'       => __( 'Enable sandbox', 'wc-gateway-greeninvoice' ),
				/* translators: %s Sandbox account */
				'description' => sprintf( __( 'Check this to enable test mode. %s and license key are required.', 'wc-gateway-greeninvoice' ), '<a href="https://app.sandbox.d.greeninvoice.co.il/market/plugin/woocommerce" target="_blank">' . __( 'Sandbox account', 'wc-gateway-greeninvoice' ) . '</a>' ),
			]
		);

		add_settings_field(
			Setting::DOWNLOAD_DEBUG_DATA,
			esc_html__( 'Debug Data', 'wc-gateway-greeninvoice' ),
			[ $this, 'render_field' ],
			MRN_WC_SLUG,
			MRN_WC_SLUG . '_debugging',
			[
				'id'          => Setting::DOWNLOAD_DEBUG_DATA,
				'type'        => 'button',
				'label'       => __( 'Generate File', 'wc-gateway-greeninvoice' ),
				'description' => __( 'Generate a debug file which includes logs and WordPress environment information.', 'wc-gateway-greeninvoice' ),
				'action'      => MRN_WC_SLUG . '_generate_debug_file',
			]
		);
	}


	/**
	 * Check if we need to authenticate using API key.
	 *
	 * @param array|string $options Current options values.
	 * @param array $new_options New options values.
	 *
	 * @since 1.0.0
	 */
	public function maybe_check_license( $options, array $new_options ): void {
		$api = API::get_instance();

		if ( ! is_array( $options ) ) {
			if ( ! empty( $new_options[ Setting::LICENSE_KEY ] ) ) {
				$api->set_license_key( $new_options[ Setting::LICENSE_KEY ] );
				$api->connect_store();
			}
		} elseif ( ! hash_equals( $options[ Setting::LICENSE_KEY ], $new_options[ Setting::LICENSE_KEY ] ) ) {
			$api->set_license_key( $new_options[ Setting::LICENSE_KEY ] );
			$api->connect_store();
		}
	}

	/**
	 * Check if we need to change the status of activation.
	 *
	 * @param array $options Options to save.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function maybe_change_status( array $options ): array {
		if ( empty( $options[ Setting::LICENSE_KEY ] ) ) {
			$options[ Setting::ACTIVATED ] = '';
			$options[ Setting::GATEWAYS ]  = null;
		}

		return $options;
	}


	/**
	 * Output settings page html.
	 *
	 * @since 1.0.0
	 */
	public function render_page(): void {
		require_once MRN_WC_PATH . 'templates/admin/settings-page.php';
	}

	/**
	 * Output settings field html.
	 *
	 * @param array $args Field arguments.
	 *
	 * @since 1.0.0
	 */
	public function render_field( array $args ): void {
		$values       = self::get_options();
		$option_key   = self::OPTIONS_KEY . "[{$args['id']}]";
		$option_value = ( ! empty( $values[ $args['id'] ] ) ) ? $values[ $args['id'] ] : null;
		$options      = [
			'css_classes' => [ "morning-field-{$args['type']}" ],
		];

		if ( isset( $args['disabled'] ) ) {
			$options['disabled'] = $args['disabled'];
		}

		if ( isset( $args['readonly'] ) ) {
			$options['readonly'] = $args['readonly'];
		}

		if ( isset( $args['action'] ) ) {
			$options['action'] = $args['action'];
		}

		switch ( strtolower( $args['type'] ) ) {
			case 'text':
				new Text_Input(
					$args['label_for'],
					$args['type'],
					$option_value,
					$option_key,
					$options
				);
				break;

			case 'checkbox':
				new Checkbox(
					$args['id'],
					$args['label'],
					$option_value,
					$option_key,
					$options
				);
				break;

			case 'status':
				$options['status_list'] = $args['status_list'];

				new Status_Indicator(
					$args['id'],
					$option_value,
					$option_key,
					$options
				);
				break;

			case 'select':
				$options['values'] = $args['values'];

				new Select(
					$args['id'],
					$option_value,
					$option_key,
					$options
				);
				break;

			case 'gateways_sync':
				new Gateways_Sync(
					$args['id'],
					$option_value,
					$option_key,
					$options
				);
				break;

			case 'button':
				new Button(
					$args['id'],
					$args['label'],
					null,
					$option_key,
					$options
				);
				break;
		}

		if ( ! empty( $args['description'] ) ) {
			echo wp_kses_post( $this->render_description( $args['description'] ) );
		}
	}


	/**
	 * Output settings field description.
	 *
	 * @param string|null $text Field description.
	 *
	 * @return string
	 */
	public function render_description( ?string $text ): string {
		return "<p class='description'>{$text}</p>";
	}


	/**
	 * Retrieve options data.
	 *
	 * @return bool|array
	 *
	 * @since 1.0.0
	 */
	public static function get_options() {
		return get_option( self::OPTIONS_KEY );
	}

	/**
	 * Returns whether an option is enabled.
	 *
	 * @param string $setting_key Setting key.
	 *
	 * @return bool
	 *
	 * @since 1.4.0
	 */
	public static function is_enabled( string $setting_key ): bool {
		$settings = self::get_options();

		return ! empty( $settings[ $setting_key ] );
	}
}
