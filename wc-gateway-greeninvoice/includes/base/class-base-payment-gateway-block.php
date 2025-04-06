<?php
/**
 * Class Base_Payment_Gateway_Block
 *
 * @package    Morning\WC\Abstracts
 * @subpackage Base_Payment_Gateway_Block
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.3.0
 */

namespace Morning\WC\Base;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Morning\WC\Exceptions\Gateway_Exception;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Payment_Gateway_Block
 *
 * @package Morning\WC\Abstracts
 */
abstract class Base_Payment_Gateway_Block extends AbstractPaymentMethodType {
	/**
	 * @var Base_Payment_Gateway
	 *
	 * @since 1.3.0
	 */
	protected $gateway;
	/**
	 * @var string
	 *
	 * @since 1.3.0
	 */
	protected $name = '';
	/**
	 * @var array
	 *
	 * @since 1.3.0
	 */
	protected $settings = [];
	/**
	 * @var array
	 *
	 * @since 1.3.0
	 */
	protected array $block_scripts = [];


	/**
	 * Base_Payment_Gateway_Block constructor.
	 *
	 * @throws Gateway_Exception
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		$gateways = WC()->payment_gateways()->payment_gateways();

		if ( ! empty( $gateways[ $this->name ] ) ) {
			$this->gateway = $gateways[ $this->name ];
		}

		if ( ! $this->gateway instanceof Base_Payment_Gateway ) {
			throw new Gateway_Exception( 'Gateway Block [' . self::class . '] does not have a linked gateway.' );
		}
	}


	/**
	 * @return void
	 *
	 * @since 1.3.0
	 */
	public function initialize(): void {
		$this->settings = get_option( "{$this->name}_settings", [] );
	}


	/**
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public function get_supported_features(): array {
		return $this->gateway->supports;
	}

	/**
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
	 * @return bool
	 *
	 * @since 1.3.0
	 */
	public function is_active(): bool {
		return $this->gateway->is_available();
	}


	/**
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
