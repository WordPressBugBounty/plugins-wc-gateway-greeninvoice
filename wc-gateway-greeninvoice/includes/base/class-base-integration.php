<?php
/**
 * Class Base_Integration
 *
 * @package    Morning\WC\Base
 * @subpackage Base_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Base;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Integration
 *
 * @package Morning\WC\Base
 */
abstract class Base_Integration {
	/**
	 * @var bool
	 *
	 * @since 2.0.0
	 */
	private bool $loaded = false;


	/**
	 * Base_Integration constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->loaded = $this->is_compatible();

		add_filter(
			'morning/wc/registered_integrations',
			function ( array $integrations ) {
				$integrations[] = $this->get_integration_name();

				return $integrations;
			}
		);

		if ( $this->is_loaded() ) {
			$this->register_hooks();
		}
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function register_hooks(): void {
		add_filter(
			'morning/wc/loaded_integrations',
			function ( array $integrations ) {
				$integrations[] = $this->get_integration_name();

				return $integrations;
			}
		);
	}

	/**
	 * @param string $plugin Plugin directory & file.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_plugin_active( string $plugin ): bool {
		return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_loaded(): bool {
		return $this->loaded;
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	abstract protected function is_compatible(): bool;

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	abstract protected function get_integration_name(): string;
}
