<?php
/**
 * Class Site_Info
 *
 * @package    Morning\WC\Utilities
 * @subpackage Site_Info
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.1
 * @since      1.4.0
 */

namespace Morning\WC\Utilities;

use Automattic\WooCommerce\Utilities\RestApiUtil;
use Morning\WC\Enum\Payment_Type;
use Morning\WC\Enum\Report_Format;
use Morning\WC\Enum\Setting;
use WC_API;
use WC_Payment_Gateway;
use WP_Site_Health;

defined( 'ABSPATH' ) || exit;


/**
 * Class Site_Info
 *
 * @package Morning\WC\Utilities
 */
final class Site_Info {
	/**
	 * Site report version.
	 *
	 * @var string
	 *
	 * @since 1.4.0
	 */
	const VERSION = '1.0';

	/**
	 * WordPress data.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $wp = [];

	/**
	 * WooCommerce data.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $wc = [];

	/**
	 * WooCommerce tax settings data.
	 *
	 * @var array
	 *
	 * @since 1.5.0
	 */
	protected $wc_taxes = [];

	/**
	 * Installed themes.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $themes = [];

	/**
	 * Installed plugins.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $plugins = [];

	/**
	 * Our plugin's data.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $plugin_settings = [];

	/**
	 * Environment data.
	 *
	 * @var array
	 *
	 * @since 1.4.0
	 */
	protected $environment = [];


	/**
	 * Site_Info constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		$this->wp              = $this->gather_wordpress_data();
		$this->wc              = $this->gather_woocommerce_data();
		$this->wc_taxes        = $this->gather_woocommerce_tax_data();
		$this->themes          = $this->gather_themes_data();
		$this->plugins         = $this->gather_plugins_data();
		$this->plugin_settings = $this->gather_plugin_settings();
		$this->environment     = $this->gather_environment_data();
	}

	/**
	 * Output site info report in a desired format.
	 *
	 * @param string $format
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
	 * Output report data as Markdown.
	 *
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
	 * Gather WordPress installation data.
	 *
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
	 * Gather WooCommerce installation data.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_woocommerce_data(): array {
		$data = $this->get_wc_rest_api()->get_endpoint_data( '/wc/v3/system_status' );

		$pages = [];

		if ( ! empty( $data['pages'] ) ) {
			foreach ( $data['pages'] as $page ) {
				$pages[] = [
					'label' => "Page: {$page['page_name']}",
					'value' => get_permalink( $page['page_id'] ),
				];
			}
		}

		return [
			[
				'label' => 'Version',
				'value' => WC_VERSION,
			],
			[
				'label' => 'Currency',
				'value' => "{$data['settings']['currency']} ({$data['settings']['currency_symbol']})",
			],
			[
				'label' => 'Number of Decimals',
				'value' => $data['settings']['number_of_decimals'],
			],
			[
				'label' => 'HPOS',
				'value' => $data['settings']['HPOS_enabled'] ? 'Enabled' : 'Disabled',
			],
			[
				'label' => 'Enabled Payment Gateways',
				'value' => $this->parse_payment_methods( WC()->payment_gateways()->payment_gateways() ),
			],
			...$pages
		];
	}

	/**
	 * Gather WooCommerce tax settings data.
	 *
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

		$data = $this->get_wc_rest_api()->get_endpoint_data( '/wc/v3/taxes' );

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
				'value' => $this->parse_tax_rates( $data ),
			],
		];
	}

	/**
	 * Gather WordPress themes data.
	 *
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
	 * Gather WordPress plugins data.
	 *
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
	 * Gather plugin settings.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function gather_plugin_settings(): array {
		$settings = Settings::get_options();

		if ( ! $settings ) {
			return [];
		}

		return [
			[
				'label' => 'License Key',
				'value' => $settings[ Setting::LICENSE_KEY ],
			],
			[
				'label' => 'License Status',
				'value' => $this->parse_status( $settings[ Setting::ACTIVATED ] ),
			],
			[
				'label' => 'Allowed Gateways',
				'value' => $this->parse_gateways( $settings[ Setting::GATEWAYS ] ),
			],
			[
				'label' => 'Order Status',
				'value' => ucfirst( $settings[ Setting::ORDER_STATUS ] ),
			],
			[
				'label' => 'Tax ID Number Field',
				'value' => $settings[ Setting::SHOW_TAX_ID_FIELD ] ? 'Active' : 'Not Active',
			],
			[
				'label' => 'Debugging',
				'value' => $settings[ Setting::DEBUGGING ] ? 'Active' : 'Not Active',
			],
			[
				'label' => 'Sandbox',
				'value' => $settings[ Setting::SANDBOX ] ? 'Active' : 'Not Active',
			],
			[
				'label' => 'Database Version',
				'value' => get_option( Setting::DB_VERSION ),
			],
		];
	}

	/**
	 * Gather environment data.
	 *
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
	 * Parse license activation status.
	 *
	 * @param string|null $status License activation status.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	public function parse_status( ?string $status ): string {
		switch ( $status ) {
			case 'empty':
				return 'Inactive';

			case 'yes':
				return 'Active';

			case 'no':
				return 'Activation Error';

			default:
				return 'Not Set';
		}
	}

	/**
	 * Parse gateways statuses.
	 *
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
				( $gateways[ $gateway_id ] ?? null ) === '1' ? 'Available' : 'Unavailable'
			);
		}

		return $output;
	}

	/**
	 * Parse WooCommerce registered payment methods.
	 *
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
	 * Parse WooCommerce registered tax rates.
	 *
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

		if ( isset( $tax_rates['code'] ) || 'woocommerce_rest_cannot_view' === $tax_rates['code'] ) {
			return [];
		}

		$output = [];
		foreach ( $tax_rates as $tax_rate ) {
			$output[] = sprintf(
				'Name: %s<br> Rate: %f<br> Country: %s<br>State: %s<br> City: %s<br>Postcode: %s<br>Priority: %d<br>Compound: %s<br>Shipping: %s',
				$tax_rate['name'],
				$tax_rate['rate'],
				$tax_rate['country'],
				$tax_rate['state'],
				$tax_rate['city'],
				$tax_rate['postcode'],
				$tax_rate['priority'],
				$tax_rate['compound'] ? 'Yes' : 'No',
				$tax_rate['shipping'] ? 'Yes' : 'No'
			);
		}

		return $output;
	}


	/**
	 * Retrieve REST API class instance.
	 *
	 * @return WC_API|RestApiUtil
	 *
	 * @since 1.5.1
	 */
	public function get_wc_rest_api() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\RestApiUtil' ) ) {
			return wc_get_container()->get( RestApiUtil::class );
		}

		return wc()->api;
	}
}
