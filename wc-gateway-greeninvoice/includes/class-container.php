<?php
/**
 * Class Container
 *
 * @package    Morning\WC
 * @subpackage Container
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.3.6
 * @since      2.0.0
 */

namespace Morning\WC;

use Morning\WC\Config\Settings;
use Morning\WC\Exceptions\Container_Exception;
use Morning\WC\Gateways\Apple_Pay_Gateway;
use Morning\WC\Gateways\Bit_Gateway;
use Morning\WC\Gateways\Blocks\Apple_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\Bit_Gateway_Block;
use Morning\WC\Gateways\Blocks\Credit_Card_Gateway_Block;
use Morning\WC\Gateways\Blocks\Google_Pay_Gateway_Block;
use Morning\WC\Gateways\Blocks\PayPal_Gateway_Block;
use Morning\WC\Gateways\Credit_Card_Gateway;
use Morning\WC\Gateways\Google_Pay_Gateway;
use Morning\WC\Gateways\Payment_Gateway_Manager;
use Morning\WC\Gateways\PayPal_Gateway;
use Morning\WC\Http\HTTP_Client;
use Morning\WC\Integrations\Integration_Manager;
use Morning\WC\Integrations\PayPlus_Integration;
use Morning\WC\Integrations\Polylang_Integration;
use Morning\WC\Integrations\PW_Gift_Cards_Integration;
use Morning\WC\Integrations\Woo_Subscriptions_Integration;
use Morning\WC\Utilities\Api;
use Morning\WC\Utilities\Auth;
use Morning\WC\Utilities\Exporter;
use Morning\WC\Utilities\IPN_Handler;
use WP_Http;

defined( 'ABSPATH' ) || exit;


/**
 * Class Container
 *
 * @package Morning\WC
 */
class Container {
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $container = [];
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $registry = [];


	/**
	 * Container constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->add( Settings::class );

		// UI
		$this->add( Admin::class, [ Settings::class ] );
		$this->add( Frontend::class, [ Settings::class ] );

		// HTTP
		$this->add( WP_Http::class );
		$this->add( Http_Client::class, [ Wp_Http::class ] );
		$this->add( Auth::class, [ HTTP_Client::class, Settings::class, Admin::class ] );
		$this->add( Api::class, [ HTTP_Client::class, Auth::class ] );

		// Core
		$this->add( Updater::class, [ Settings::class, Auth::class ] );
		$this->add( Compatibility::class, [ Admin::class ] );
		$this->add( Site_Info::class, [ Settings::class ] );
		$this->add( Exporter::class );
		$this->add( Ajax::class, [ Auth::class, Exporter::class ] );
		$this->add( Checkout::class, [ Settings::class ] );
		$this->add( Invoicing::class, [ Settings::class, Api::class ] );
		$this->add( IPN_Handler::class );

		// Payment Gateways
		$this->add( Credit_Card_Gateway::class, [ Api::class, Settings::class ] );
		$this->add( PayPal_Gateway::class, [ Api::class, Settings::class ] );
		$this->add( Bit_Gateway::class, [ Api::class, Settings::class ] );
		$this->add( Google_Pay_Gateway::class, [ Api::class, Settings::class ] );
		$this->add( Apple_Pay_Gateway::class, [ Api::class, Settings::class ] );

		$this->add( Credit_Card_Gateway_Block::class );
		$this->add( PayPal_Gateway_Block::class );
		$this->add( Bit_Gateway_Block::class );
		$this->add( Google_Pay_Gateway_Block::class );
		$this->add( Apple_Pay_Gateway_Block::class );

		$this->add( Payment_Gateway_Manager::class, [ Settings::class ] );

		// Integrations
		$this->add( Polylang_Integration::class );
		$this->add( PW_Gift_Cards_Integration::class );
		$this->add( Woo_Subscriptions_Integration::class );
		$this->add( PayPlus_Integration::class );

		$this->add( Integration_Manager::class );

		// Plugin
		$this->add( Plugin::class, [ Compatibility::class, Admin::class, Settings::class ] );
	}


	/**
	 * @param string $class_id Class name and namespace.
	 * @param array $dependencies Class dependencies.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function add( string $class_id, array $dependencies = [] ): void {
		$this->registry[ $class_id ] = $dependencies;
	}

	/**
	 * @param string $class_id Class name and namespace.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function has( string $class_id ): bool {
		return array_key_exists( $class_id, $this->registry );
	}

	/**
	 * @param string $class_id
	 *
	 * @return mixed
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	public function get( string $class_id ) {
		if ( ! $this->has( $class_id ) ) {
			// @phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new Container_Exception( "Class `{$class_id}` not found in registry" );
		}

		if ( empty( $this->container[ $class_id ] ) ) {
			$this->init( $class_id );
		}

		return $this->container[ $class_id ];
	}


	/**
	 * @param string $class_id Class name and namespace.
	 *
	 * @return void
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	private function init( string $class_id ) {
		$deps = [];

		foreach ( $this->registry[ $class_id ] as $dependency ) {
			$deps[] = $this->get( $dependency );
		}

		$this->container[ $class_id ] = new $class_id( ...$deps );
	}


	/**
	 * @return Plugin
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	public function get_plugin(): Plugin {
		return $this->get( Plugin::class );
	}

	/**
	 * @return Settings
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	public function get_settings(): Settings {
		return $this->get( Settings::class );
	}
}
