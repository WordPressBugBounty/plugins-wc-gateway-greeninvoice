<?php
/**
 * Class Auth
 *
 * @package    Morning\WC\Utilities
 * @subpackage Auth
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Admin;
use Morning\WC\Config\Options;
use Morning\WC\Config\Settings;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Formatters\Gateways_Response_Formatter;
use Morning\WC\Http\HTTP_Client;
use Morning\WC\Http\Http_Request;
use Morning\WC\Http\Http_Response;

defined( 'ABSPATH' ) || exit;


/**
 * Class Auth
 *
 * @package Morning\WC\Utilities
 */
class Auth {
	/**
	 * @var HTTP_Client
	 *
	 * @since 2.0.0
	 */
	private HTTP_Client $client;
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;
	/**
	 * @var Admin
	 *
	 * @since 2.0.0
	 */
	private Admin $admin;


	/**
	 * Auth constructor.
	 *
	 * @param HTTP_Client $client
	 * @param Settings $settings
	 * @param Admin $admin
	 *
	 * @since 2.0.0
	 */
	public function __construct( HTTP_Client $client, Settings $settings, Admin $admin ) {
		$this->client   = $client;
		$this->settings = $settings;
		$this->admin    = $admin;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'add_option_' . Options::OPTIONS_KEY, [ $this, 'maybe_check_license' ], 10, 2 );
		add_action( 'update_option_' . Options::OPTIONS_KEY, [ $this, 'maybe_update_license_status' ], 10, 2 );

		add_action( 'init', [ $this, 'maybe_display_activation_notice' ] );
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function remove_hooks(): void {
		remove_action( 'add_option_' . Options::OPTIONS_KEY, [ $this, 'maybe_check_license' ], 10 );
		remove_action( 'update_option_' . Options::OPTIONS_KEY, [ $this, 'maybe_update_license_status' ], 10 );
	}


	/**
	 * @param string $option_name Option name.
	 * @param array $option_values Option values.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function maybe_check_license( string $option_name, array $option_values ): void {
		if ( ! empty( $option_values['license_key'] ) ) {
			$this->settings->set_options( new Options( $option_values ) );

			$this->authorize_store();
		}
	}

	/**
	 * @param array $old_options Pre-save option values.
	 * @param array $new_options Post-save option values.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function maybe_update_license_status( array $old_options, array $new_options ): void {
		if ( empty( $new_options['license_key'] ) ) {
			return;
		}

		if ( ! hash_equals( $old_options['license_key'] ?? '', $new_options['license_key'] ) ) {
			$this->settings->set_options( new Options( $new_options ) );

			$this->authorize_store();
		}
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function maybe_display_activation_notice(): void {
		$options = $this->settings->get_options();

		if ( 'yes' !== $options->get_activated() ) {
			add_action( 'admin_notices', [ $this, 'plugin_license_notice' ] );
		}
	}

	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function plugin_license_notice(): void {
		/* translators: %s Plugin Name */
		$notice = sprintf( __( 'Please activate the plugin by entering your license key for %s.', 'wc-gateway-greeninvoice' ), '<a href="admin.php?page=greeninvoice">' . __( 'Morning for WooCommerce', 'wc-gateway-greeninvoice' ) . '</a>' );

		$this->admin->print_notice( $notice );
	}


	/**
	 * @return Http_Response
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	public function authorize_store(): Http_Response {
		$options = $this->settings->get_options();

		$url = Api::get_request_url( '/api/v1/plugins/woocommerce/auth' );

		$request = new Http_Request( $url );
		$request->set_headers( [ 'Authorization' => $this->get_authorization_token() ] );

		$response = $this->client->post( $request );

		if ( $response->is_ok() ) {
			$body = $response->json_body();

			$options->set_activated( 'yes' );
			$options->set_gateways( Gateways_Response_Formatter::format( $body['gateways'] ?? [] ) );
			$options->set_plan( (int) $body['plan'] );
		} else {
			$options->set_activated( 'error' );
			$options->set_gateways( [] );
		}

		$this->remove_hooks();

		$options->save();

		$this->register_hooks();

		return $response;
	}


	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_authorization_token(): string {
		$shop_url    = str_replace( [ 'https://', 'http://' ], '', untrailingslashit( site_url() ) );
		$license_key = ( $this->settings->get_options() )->get_license_key();

		// @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return 'Basic ' . base64_encode( "{$shop_url}:{$license_key}" );
		// @phpcs:enable
	}
}
