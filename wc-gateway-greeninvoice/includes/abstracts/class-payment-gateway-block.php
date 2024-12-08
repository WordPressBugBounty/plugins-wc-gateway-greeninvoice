<?php
/**
 * Class Payment_Gateway_Block
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Payment_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.6.1
 * @since      1.3.0
 */

namespace Morning\WC\Abstracts;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Morning\WC\Exceptions\Gateway_Exception;
use Morning\WC\Gateways\Payment_Gateway_Factory;

defined( 'ABSPATH' ) || exit;


/**
 * Class Payment_Gateway_Block
 *
 * @package Morning\WC\Abstracts
 */
abstract class Payment_Gateway_Block extends AbstractPaymentMethodType {
	/**
	 * Gateway Instance.
	 *
	 * @var Payment_Gateway
	 *
	 * @since 1.3.0
	 */
	protected $gateway;

	/**
	 * Gateway ID.
	 *
	 * @var string
	 *
	 * @since 1.3.0
	 */
	protected $name = '';

	/**
	 * Gateway settings from `wp_options` table.
	 *
	 * @var array
	 *
	 * @since 1.3.0
	 */
	protected $settings = [];

	/**
	 * Gateway block frontend scripts.
	 *
	 * @var array
	 *
	 * @since 1.3.0
	 */
	protected $block_scripts = [];


	/**
	 * Payment_Gateway_Block constructor.
	 *
	 * @throws Gateway_Exception
	 *
	 * @version 1.6.1
	 * @since 1.3.0
	 */
	public function __construct() {
		$gateways      = WC()->payment_gateways()->payment_gateways();
		$this->gateway = $gateways[ $this->name ] ?? Payment_Gateway_Factory::init( $this->name );

		if ( ! $this->gateway instanceof Payment_Gateway ) {
			throw new Gateway_Exception( 'Gateway Block [' . self::class . '] does not have a linked gateway.' );
		}
	}


	/**
	 * Initializes payment gateway method.
	 *
	 * @return void
	 *
	 * @since 1.3.0
	 */
	public function initialize(): void {
		$this->settings = get_option( "{$this->name}_settings", [] );
	}


	/**
	 * Retrieves payment method supported features.
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public function get_supported_features(): array {
		return $this->gateway->supports;
	}

	/**
	 * Registers gateway block frontend scripts.
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public function get_payment_method_script_handles(): array {
		$ids          = [];
		$default_deps = [
			'wc-blocks-registry',
			'wc-settings',
			'wp-element',
			'wp-html-entities',
			'wp-i18n',
		];

		foreach ( $this->block_scripts as $script ) {
			wp_register_script(
				$script['id'],
				$script['file'],
				! empty( $script['deps'] ) ? $script['deps'] : $default_deps,
				MRN_WC_VERSION,
				[ 'in_footer' => true ]
			);

			$ids[] = $script['id'];

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( $script['id'], 'wc-gateway-greeninvoice', MRN_WC_PATH . 'languages/' );
			}
		}

		return $ids;
	}

	/**
	 * Retrieves payment method data to frontend.
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public function get_payment_method_data(): array {
		return [
			'title'       => $this->gateway->title,
			'description' => $this->gateway->description,
			'supports'    => $this->get_supported_features(),
			'is_active'   => $this->is_active(),
		];
	}


	/**
	 * Returns whether this payment gateway should be available.
	 *
	 * @return bool
	 *
	 * @since 1.3.0
	 */
	public function is_active(): bool {
		return $this->gateway->is_available();
	}


	/**
	 * Retrieves gateway block dependencies.
	 *
	 * @param string $dependencies_file Path to block dependencies file.
	 *
	 * @return array|null
	 *
	 * @since 1.6.1
	 */
	public function get_block_dependencies( string $dependencies_file ): ?array {
		if ( ! is_readable( $dependencies_file ) ) {
			return null;
		}

		$dependencies = require $dependencies_file;

		return $dependencies['dependencies'] ?? [];
	}
}
