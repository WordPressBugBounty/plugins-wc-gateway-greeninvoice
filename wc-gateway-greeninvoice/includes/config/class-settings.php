<?php
/**
 * Class Settings
 *
 * @package    Morning\WC\Config
 * @subpackage Settings
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.0
 * @since      1.0.0
 */

namespace Morning\WC\Config;

use Morning\WC\Base\Base_Settings_Field;
use Morning\WC\Fields\Button;
use Morning\WC\Fields\Checkbox;
use Morning\WC\Fields\Gateway_Selection;
use Morning\WC\Fields\Gateways_Sync;
use Morning\WC\Fields\Installments_Table;
use Morning\WC\Fields\Plan_Indicator;
use Morning\WC\Fields\Section;
use Morning\WC\Fields\Select;
use Morning\WC\Fields\Status_Indicator;
use Morning\WC\Fields\Text_Input;

defined( 'ABSPATH' ) || exit;


/**
 * Class Settings
 *
 * @package Morning\WC\Config
 */
class Settings {
	/**
	 * @var Options
	 *
	 * @since 2.0.0
	 */
	private Options $options;
	/**
	 * @var Base_Settings_Field[]
	 *
	 * @since 2.0.0
	 */
	private array $settings = [];


	/**
	 * Settings constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->options = new Options();

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'admin_init', [ $this, 'register_fields' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );

		add_filter( 'pre_update_option_' . Options::OPTIONS_KEY, [ $this, 'maybe_change_license_status' ] );
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function register_fields(): void {
		$this->register_licensing_fields();

		if ( $this->options->is_license_valid() ) {
			$this->settings[] = new Plan_Indicator( MRN_WC_SLUG . '_licensing', 'plan', esc_html__( 'Plugin Plan', 'wc-gateway-greeninvoice' ), $this->options->get_plan() );

			if ( $this->options->is_clearing_mode() || $this->options->is_basic_mode() ) {
				$this->register_clearing_mode_fields();
			} elseif ( $this->options->is_invoicing_mode() ) {
				$this->register_invoicing_mode_fields();
			}

			$this->settings[] = new Checkbox(
				MRN_WC_SLUG . '_general',
				'show_tax_id_field',
				esc_html__( 'Tax ID Number', 'wc-gateway-greeninvoice' ),
				$this->options->is_show_tax_id_field() ? 'yes' : 'no',
				'show_tax_id_field',
				[
					'checkbox_text' => esc_html__( 'Enable Tax ID number field in checkout', 'wc-gateway-greeninvoice' ),
				]
			);
		}

		$this->register_advanced_fields();
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_licensing_fields(): void {
		$this->settings = array_merge(
			$this->settings,
			[
				new Section( MRN_WC_SLUG . '_licensing', esc_html__( 'Licensing', 'wc-gateway-greeninvoice' ) ),
				new Text_Input(
					MRN_WC_SLUG . '_licensing',
					'license_key',
					esc_html__( 'License Key', 'wc-gateway-greeninvoice' ),
					'text',
					$this->options->get_license_key(),
					'license_key',
					[
						'css_classes' => [ 'regular-text' ],
					]
				),
				new Status_Indicator(
					MRN_WC_SLUG . '_licensing',
					'activated',
					esc_html__( 'License Status', 'wc-gateway-greeninvoice' ),
					$this->options->get_activated(),
					'activated',
					[
						'status_list' => [
							'no'    => __( 'Inactive', 'wc-gateway-greeninvoice' ),
							'yes'   => __( 'Active', 'wc-gateway-greeninvoice' ),
							'error' => __( 'Activation Error', 'wc-gateway-greeninvoice' ),
						],
					]
				),
			]
		);
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_clearing_mode_fields(): void {
		$this->settings = array_merge(
			$this->settings,
			[
				new Section( MRN_WC_SLUG . '_general', esc_html__( 'General Settings', 'wc-gateway-greeninvoice' ) ),
				new Gateways_Sync(
					MRN_WC_SLUG . '_general',
					'gateways',
					esc_html__( 'Allowed Gateways', 'wc-gateway-greeninvoice' ),
					$this->options->get_gateways(),
					'gateways',
					[
						'description' => __( 'If you changed your settings in WooCommerce on morning, you need to sync the changes.', 'wc-gateway-greeninvoice' ),
					]
				),
				new Select(
					MRN_WC_SLUG . '_general',
					'order_status',
					esc_html__( 'Order Status', 'wc-gateway-greeninvoice' ),
					$this->options->get_order_status(),
					'order_status',
					[
						'description' => __( 'Set order status after a notification of successful payment received.', 'wc-gateway-greeninvoice' ),
						'values'      => [
							'processing' => __( 'Processing', 'wc-gateway-greeninvoice' ),
							'completed'  => __( 'Completed', 'wc-gateway-greeninvoice' ),
						],
					]
				),
				new Installments_Table(
					MRN_WC_SLUG . '_general',
					'installments',
					esc_html__( 'Installments', 'wc-gateway-greeninvoice' ),
					$this->options->get_installments(),
					'installments',
				),
			]
		);
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_invoicing_mode_fields(): void {
		$this->settings = array_merge(
			$this->settings,
			[
				new Section( MRN_WC_SLUG . '_general', esc_html__( 'General Settings', 'wc-gateway-greeninvoice' ) ),
				new Gateway_Selection(
					MRN_WC_SLUG . '_general',
					'invoicing_allowed_gateways',
					esc_html__( 'Allowed Gateways', 'wc-gateway-greeninvoice' ),
					$this->options->get_invoicing_allowed_gateways(),
					'invoicing_allowed_gateways',
					[
						'description' => __( 'Select which gateway to automatically issue an invoice upon payment.', 'wc-gateway-greeninvoice' ),
					]
				),
				new Select(
					MRN_WC_SLUG . '_general',
					'invoicing_order_status',
					esc_html__( 'Order Status', 'wc-gateway-greeninvoice' ),
					$this->options->get_invoicing_order_status(),
					'invoicing_order_status',
					[
						'description' => __( 'Set order status to issue an invoice creation.', 'wc-gateway-greeninvoice' ),
						'values'      => [
							'processing' => __( 'Processing', 'wc-gateway-greeninvoice' ),
							'completed'  => __( 'Completed', 'wc-gateway-greeninvoice' ),
						],
					]
				),
			]
		);
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_advanced_fields(): void {
		$this->settings = array_merge(
			$this->settings,
			[
				// Advanced
				new Section( MRN_WC_SLUG . '_advanced', esc_html__( 'Advanced Options', 'wc-gateway-greeninvoice' ) ),
				new Checkbox(
					MRN_WC_SLUG . '_advanced',
					'sandbox_mode',
					esc_html__( 'Sandbox Mode', 'wc-gateway-greeninvoice' ),
					$this->options->is_sandbox_mode() ? 'yes' : 'no',
					'sandbox_mode',
					[
						'checkbox_text' => esc_html__( 'Enable sandbox', 'wc-gateway-greeninvoice' ),
						/* translators: %s Sandbox account */
						'description'   => sprintf( __( 'Check this to enable test mode. %s and license key are required.', 'wc-gateway-greeninvoice' ), '<a href="https://app.sandbox.d.greeninvoice.co.il/market/plugin/woocommerce" target="_blank">' . __( 'Sandbox account', 'wc-gateway-greeninvoice' ) . '</a>' ),
					]
				),
				new Button(
					MRN_WC_SLUG . '_advanced',
					'download_debug_data',
					esc_html__( 'Site Info Report', 'wc-gateway-greeninvoice' ),
					null,
					'download_debug_data',
					[
						'label'       => __( 'Generate File', 'wc-gateway-greeninvoice' ),
						'description' => __( 'Generate a debug file which includes logs and WordPress environment information.', 'wc-gateway-greeninvoice' ),
						'action'      => MRN_WC_SLUG . '_generate_debug_file',
					]
				),
				new Button(
					MRN_WC_SLUG . '_advanced',
					'view_logs',
					esc_html__( 'Logs', 'wc-gateway-greeninvoice' ),
					null,
					'view_logs',
					[
						'label'       => __( 'View Logs', 'wc-gateway-greeninvoice' ),
						'description' => __( 'View the logs of the plugin.', 'wc-gateway-greeninvoice' ),
						'action'      => MRN_WC_SLUG . '_view_logs',
					]
				),
			]
		);
	}


	/**
	 * @param array $options Raw options.
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function maybe_change_license_status( array $options ): array {
		if ( empty( $options['license_key'] ) ) {
			$options['activated'] = 'no';
			$options['gateways']  = [];
		}

		return $options;
	}


	/**
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting( MRN_WC_SLUG, Options::OPTIONS_KEY );

		foreach ( $this->settings as $field ) {
			if ( $field instanceof Section ) {
				add_settings_section( $field->get_id(), $field->get_label(), '__return_false', MRN_WC_SLUG );
			} else {
				add_settings_field(
					$field->get_id(),
					$field->get_label(),
					[ $field, 'render_field' ],
					MRN_WC_SLUG,
					$field->get_section_id()
				);
			}
		}
	}


	/**
	 * Output settings page html.
	 *
	 * @since 1.0.0
	 */
	public static function render_settings_page(): void {
		require_once MRN_WC_PATH . 'templates/admin/settings-page.php';
	}


	/**
	 * @return Options
	 *
	 * @since 2.0.0
	 */
	public function get_options(): Options {
		return $this->options;
	}

	/**
	 * @param Options $options
	 *
	 * @since 2.0.0
	 */
	public function set_options( Options $options ): void {
		$this->options = $options;
	}
}
