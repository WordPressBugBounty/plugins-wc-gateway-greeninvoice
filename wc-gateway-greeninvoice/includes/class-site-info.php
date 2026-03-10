<?php
/**
 * Class Site_Info
 *
 * @package    Morning\WC
 * @subpackage Site_Info
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.8
 * @since      1.4.0
 */

namespace Morning\WC;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Enum\Plan_Type;
use Morning\WC\Enum\Report_Format;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Integrations\Integration_Manager;
use WC_Payment_Gateway;
use WC_Tax;
use WP_Site_Health;

defined( 'ABSPATH' ) || exit;


/**
 * Class Site_Info
 *
 * @package Morning\WC
 */
final class Site_Info {
	/**
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const VERSION = '1.1.1';

	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;

	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $wp = [];
	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $wc = [];
	/**
	 * @var array
	 *
	 * @since 1.5.0
	 */
	protected array $wc_taxes = [];
	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $themes = [];
	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $plugins = [];
	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $plugin_settings = [];
	/**
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected array $environment = [];


	/**
	 * Site_Info constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 1.4.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		$this->wp              = $this->gather_wordpress_data();
		$this->wc              = $this->gather_woocommerce_data();
		$this->wc_taxes        = $this->gather_woocommerce_tax_data();
		$this->themes          = $this->gather_themes_data();
		$this->plugins         = $this->gather_plugins_data();
		$this->plugin_settings = $this->gather_plugin_settings();
		$this->environment     = $this->gather_environment_data();
	}


	/**
	 * @param string $format Report format.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public function output( string $format ): string {
		switch ( $format ) {
			case Report_Format::MARKDOWN:
				return $this->output_as_markdown();

			case Report_Format::JSON:
				return $this->output_as_json();

			default:
				return '';
		}
	}


	/**
	 * @return string
	 *
	 * @since  1.4.0
	 */
	public function output_as_markdown(): string {
		$output = '';

		$output .= '# Site Info Report v' . self::VERSION . PHP_EOL;
		$output .= '## WordPress' . PHP_EOL;
		$output .= '| Label | Value |' . PHP_EOL;
		$output .= '|:------|:------|' . PHP_EOL;

		foreach ( $this->wp as $row ) {
			if ( is_array( $row['value'] ) ) {
				$row['value'] = implode( '<br>', $row['value'] );
			}

			$output .= "| {$row['label']} | {$row['value']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## WooCommerce' . PHP_EOL;
		$output .= '| Label | Value |' . PHP_EOL;
		$output .= '|:------|:------|' . PHP_EOL;

		foreach ( $this->wc as $row ) {
			if ( is_array( $row['value'] ) ) {
				$row['value'] = implode( '<br>', $row['value'] );
			}

			$output .= "| {$row['label']} | {$row['value']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## WooCommerce Tax Settings' . PHP_EOL;
		$output .= '| Label | Value |' . PHP_EOL;
		$output .= '|:------|:------|' . PHP_EOL;

		foreach ( $this->wc_taxes as $row ) {
			if ( is_array( $row['value'] ) ) {
				$row['value'] = implode( '<br>------------------------------<br>', $row['value'] );
			}

			$output .= "| {$row['label']} | {$row['value']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## Plugin Settings' . PHP_EOL;
		$output .= '| Setting | Value |' . PHP_EOL;
		$output .= '|:--------|:------|' . PHP_EOL;

		foreach ( $this->plugin_settings as $row ) {
			if ( is_array( $row['value'] ) ) {
				$row['value'] = implode( '<br>', $row['value'] );
			}

			$output .= "| {$row['label']} | {$row['value']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## Themes' . PHP_EOL;
		$output .= '| Theme | Author | Active |' . PHP_EOL;
		$output .= '|:------|:-------|:-------|' . PHP_EOL;

		foreach ( $this->themes as $row ) {
			$output .= "| [{$row['name']}]({$row['uri']}) v{$row['version']} | [{$row['author']}](${$row['author_uri']}) | {$row['active']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## Plugins' . PHP_EOL;
		$output .= '| Plugin | Author | Active |' . PHP_EOL;
		$output .= '|:-------|:-------|:-------|' . PHP_EOL;

		foreach ( $this->plugins as $row ) {
			$output .= "| [{$row['name']}]({$row['uri']}) v{$row['version']} | [{$row['author']}](${$row['author_uri']}) | {$row['active']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '## Environment' . PHP_EOL;
		$output .= '| Label | Value |' . PHP_EOL;
		$output .= '|:------|:------|' . PHP_EOL;

		foreach ( $this->environment as $row ) {
			$output .= "| {$row['label']} | {$row['value']} |" . PHP_EOL;
		}

		$output .= '---' . PHP_EOL;
		$output .= '_Generated on ' . gmdate( 'd-m-Y, H:i:s' ) . '_' . PHP_EOL;

		return $output;
	}

	/**
	 * Output report data as JSON.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public function output_as_json(): string {
		return wp_json_encode(
			[
				'header'          => [
					'title'   => 'Site Info Report',
					'version' => 'v' . self::VERSION,
				],
				'wordpress'       => $this->wp,
				'woocommerce'     => $this->wc,
				'woocommerce_tax' => $this->wc_taxes,
				'plugin_settings' => $this->plugin_settings,
				'themes'          => $this->themes,
				'plugins'         => $this->plugins,
				'environment'     => $this->environment,
			],
			JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION
		);
	}


	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_wordpress_data(): array {
		return [
			[
				'label' => 'Version',
				'value' => get_bloginfo( 'version' ),
			],
			[
				'label' => 'Environment',
				'value' => wp_get_environment_type(),
			],
			[
				'label' => 'Language',
				'value' => get_locale(),
			],
			[
				'label' => 'Home URL',
				'value' => get_bloginfo( 'url' ),
			],
			[
				'label' => 'Site URL',
				'value' => get_bloginfo( 'wpurl' ),
			],
			[
				'label' => 'HTTPS',
				'value' => is_ssl() ? 'Yes' : 'No',
			],
			[
				'label' => 'Permalink',
				'value' => get_option( 'permalink_structure' ) ? 'Yes' : 'No',
			],
			[
				'label' => 'Multisite',
				'value' => is_multisite() ? 'Yes' : 'No',
			],
			[
				'label' => 'Memory Limit',
				'value' => WP_MEMORY_LIMIT,
			],
			[
				'label' => 'Debug',
				'value' => WP_DEBUG ? 'Enabled' : 'Disabled',
			],
		];
	}

	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_woocommerce_data(): array {
		$wc_pages = [
			'Shop'               => wc_get_page_id( 'shop' ),
			'Cart'               => wc_get_page_id( 'cart' ),
			'Checkout'           => wc_get_page_id( 'checkout' ),
			'My Account'         => wc_get_page_id( 'myaccount' ),
			'Terms & Conditions' => wc_get_page_id( 'terms' ),
		];

		foreach ( $wc_pages as $wc_page => $wc_page_id ) {
			$pages[] = [
				'label' => "{$wc_page} Page",
				'value' => - 1 !== $wc_page_id ? get_permalink( $wc_page_id ) : 'Not Set',
			];
		}

		$currency        = get_woocommerce_currency();
		$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );

		return [
			[
				'label' => 'Version',
				'value' => WC()->version,
			],
			[
				'label' => 'Currency',
				'value' => "{$currency} ({$currency_symbol})",
			],
			[
				'label' => 'Number of Decimals',
				'value' => wc_get_price_decimals(),
			],
			[
				'label' => 'HPOS',
				'value' => OrderUtil::custom_orders_table_usage_is_enabled() ? 'Enabled' : 'Disabled',
			],
			[
				'label' => 'Enabled Payment Gateways',
				'value' => $this->parse_payment_methods( WC()->payment_gateways()->payment_gateways() ),
			],
			...$pages,
		];
	}

	/**
	 * @return array
	 *
	 * @since 1.5.0
	 */
	public function gather_woocommerce_tax_data(): array {
		$tax_calculation_value = get_option( 'woocommerce_tax_based_on' );

		switch ( $tax_calculation_value ) {
			case 'shipping':
				$tax_calculation = 'Based on Shipping Address';
				break;
			case 'billing':
				$tax_calculation = 'Based on Billing Address';
				break;
			case 'base':
				$tax_calculation = 'Based on Store Location';
				break;
			default:
				$tax_calculation = "Unknown Value: '{$tax_calculation_value}'";
				break;
		}

		$taxes = WC_Tax::get_rates_for_tax_class( 'standard' );

		return [
			[
				'label' => 'Taxes',
				'value' => wc_tax_enabled() ? 'Enabled' : 'Disabled',
			],
			[
				'label' => 'Prices',
				'value' => wc_prices_include_tax() ? 'Inclusive of Tax' : 'Exclusive of Tax',
			],
			[
				'label' => 'Tax Calculation',
				'value' => $tax_calculation,
			],
			[
				'label' => 'Tax Rounding',
				'value' => 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ? 'Enabled' : 'Disabled',
			],
			[
				'label' => 'Tax Rates',
				'value' => $this->parse_tax_rates( $taxes ),
			],
		];
	}

	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_themes_data(): array {
		$active_theme = wp_get_theme();

		$themes = [];
		foreach ( wp_get_themes() as $theme ) {
			$themes[] = [
				'name'       => $theme->get( 'Name' ),
				'uri'        => $theme->get( 'Theme URI' ),
				'author'     => $theme->get( 'Author' ),
				'author_uri' => $theme->get( 'Author URI' ),
				'version'    => $theme->get( 'Version' ),
				'active'     => $active_theme->get( 'Name' ) === $theme->get( 'Name' ) ? 'Yes' : 'No',
			];
		}

		return $themes;
	}

	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_plugins_data(): array {
		$plugins = [];
		foreach ( get_plugins() as $plugin_path => $plugin ) {
			$plugins[] = [
				'name'       => $plugin['Name'],
				'uri'        => $plugin['PluginURI'],
				'author'     => $plugin['Author'],
				'author_uri' => $plugin['AuthorURI'],
				'version'    => $plugin['Version'],
				'active'     => is_plugin_active( $plugin_path ) ? 'Yes' : 'No',
			];
		}

		return $plugins;
	}

	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_plugin_settings(): array {
		$options = $this->settings->get_options();

		return [
			[
				'label' => 'License Key',
				'value' => $options->get_license_key(),
			],
			[
				'label' => 'License Status',
				'value' => $this->parse_status( $options->get_activated() ),
			],
			[
				'label' => 'License Plan',
				'value' => Plan_Type::get_label( $options->get_plan() ),
			],
			[
				'label' => 'Allowed Gateways',
				'value' => $options->is_invoicing_mode() ? $this->parse_invoicing_gateways( $options->get_invoicing_allowed_gateways() ) : $this->parse_gateways( $options->get_gateways() ),
			],
			[
				'label' => 'Order Status',
				'value' => $options->is_invoicing_mode() ? ucfirst( $options->get_invoicing_order_status() ) : ucfirst( $options->get_order_status() ),
			],
			[
				'label' => 'Tax ID Number Field',
				'value' => $options->is_show_tax_id_field() ? 'Active' : 'Not Active',
			],
			[
				'label' => 'Sandbox Mode',
				'value' => $options->is_sandbox_mode() ? 'Active' : 'Not Active',
			],
			[
				'label' => 'Database Version',
				'value' => get_option( Updater::DB_VERSION ),
			],
			[
				'label' => 'Loaded Integrations',
				'value' => $this->gather_plugin_integrations(),
			],
		];
	}

	/**
	 * @return string[]
	 *
	 * @since 2.0.0
	 */
	public function gather_plugin_integrations(): array {
		try {
			return ( mrn_get_container()->get( Integration_Manager::class ) )->get_loaded_integrations();
		} catch ( Container_Exception $e ) {
			return [];
		}
	}

	/**
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_environment_data(): array {
		global $wpdb;

		return [
			[
				'label' => 'Webserver',
				'value' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unavailable',
			],
			[
				'label' => 'PHP Version',
				'value' => PHP_VERSION,
			],
			[
				'label' => 'PHP Memory Limit',
				'value' => WP_Site_Health::get_instance()->php_memory_limit,
			],
			[
				'label' => 'PHP Max Execution Time',
				'value' => ini_get( 'max_execution_time' ),
			],
			[
				'label' => 'Database Engine',
				'value' => $wpdb->get_var( 'SELECT VERSION()' ),
			],
			[
				'label' => 'Database Charset',
				'value' => $wpdb->charset,
			],
		];
	}


	/**
	 * @param string|null $status License activation status.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public function parse_status( ?string $status ): string {
		switch ( $status ) {
			case 'no':
				return 'Inactive';

			case 'yes':
				return 'Active';

			case 'error':
				return 'Activation Error';

			default:
				return 'Not Set';
		}
	}

	/**
	 * @param array|null $gateways List of gateways.
	 *
	 * @return array
	 *
	 * @since 2.3.8
	 */
	private function parse_invoicing_gateways( ?array $gateways ): array {
		if ( empty( $gateways ) || ! is_array( $gateways ) ) {
			return [];
		}

		$output = [];
		foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $gateway ) {
			$output[] = sprintf(
				'%s: %s',
				$gateway->get_title(),
				( $gateways[ $gateway->id ] ?? null ) === 'yes' ? 'Allowed' : 'Not Allowed'
			);
		}

		return $output;
	}

	/**
	 * @param array|null $gateways List of gateways.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function parse_gateways( ?array $gateways ): array {
		if ( empty( $gateways ) || ! is_array( $gateways ) ) {
			return [];
		}

		$output = [];
		foreach ( Payment_Type::get_all() as $gateway_id ) {
			$output[] = sprintf(
				'%s: %s',
				Payment_Type::get_label( $gateway_id ),
				( $gateways[ $gateway_id ] ?? null ) === 'yes' ? 'Available' : 'Unavailable'
			);
		}

		return $output;
	}

	/**
	 * @param WC_Payment_Gateway[]|null $payment_gateways
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function parse_payment_methods( ?array $payment_gateways ): array {
		if ( empty( $payment_gateways ) || ! is_array( $payment_gateways ) ) {
			return [];
		}

		$output = [];
		foreach ( $payment_gateways as $payment_gateway ) {
			$output[] = sprintf(
				'%s: %s',
				$payment_gateway->get_title(),
				'yes' === $payment_gateway->enabled ? 'Enabled' : 'Disabled'
			);
		}

		return $output;
	}

	/**
	 * @param array|null $tax_rates
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 */
	public function parse_tax_rates( ?array $tax_rates ): array {
		if ( ! $tax_rates ) {
			return [];
		}

		$output = [];
		foreach ( $tax_rates as $tax_rate ) {
			$output[] = sprintf(
				'Name: %s<br> Rate: %f<br> Country: %s<br>State: %s<br> City: %s<br>Postcode: %s<br>Priority: %d<br>Compound: %s<br>Shipping: %s',
				$tax_rate->tax_rate_name,
				$tax_rate->tax_rate,
				$tax_rate->tax_rate_country,
				$tax_rate->tax_rate_state,
				$tax_rate->city_count,
				$tax_rate->postcode_count,
				$tax_rate->tax_rate_priority,
				$tax_rate->tax_rate_compound ? 'Yes' : 'No',
				$tax_rate->tax_rate_shipping ? 'Yes' : 'No'
			);
		}

		return $output;
	}
}
