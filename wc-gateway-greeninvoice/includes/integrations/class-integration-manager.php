<?php
/**
 * Class Integration_Manager
 *
 * @package    Morning\WC\Integrations
 * @subpackage Integration_Manager
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.4
 * @since      2.0.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Exceptions\Container_Exception;

defined( 'ABSPATH' ) || exit;


/**
 * Class Integration_Manager
 *
 * @package Morning\WC\Integrations
 */
final class Integration_Manager {
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $registered = [];
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $loaded = [];


	/**
	 * Integration_Manager constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks() {
		add_action( 'plugins_loaded', [ $this, 'load_integrations' ] );
	}


	/**
	 * @return void
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.0.0
	 */
	public function load_integrations() {
		$container = mrn_get_container();

		$container->get( Polylang_Integration::class );
		$container->get( PW_Gift_Cards_Integration::class );
		$container->get( Woo_Subscriptions_Integration::class );
		$container->get( PayPlus_Integration::class );

		$this->registered = apply_filters( 'morning/wc/registered_integrations', [] );
		$this->loaded     = apply_filters( 'morning/wc/loaded_integrations', [] );
	}


	/**
	 * @return array
	 */
	public function get_registered_integrations(): array {
		return $this->registered;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_loaded_integrations(): array {
		return $this->loaded;
	}
}
