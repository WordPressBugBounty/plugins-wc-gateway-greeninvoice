<?php
/**
 * Class Polylang_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage Polylang_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      1.4.0
 */

namespace Morning\WC\Integrations;

use Morning\WC\Base\Base_Integration;

defined( 'ABSPATH' ) || exit;


/**
 * Class Polylang_Integration
 *
 * @package Morning\WC\Integrations
 */
final class Polylang_Integration extends Base_Integration {
	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function register_hooks(): void {
		parent::register_hooks();

		add_filter( 'morning/wc/get_gateway_url_params', [ $this, 'inject_lang_param' ] );
	}


	/**
	 * @param array $params List of parameters.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function inject_lang_param( array $params ): array {
		if ( function_exists( 'pll_current_language' ) ) {
			$params['lang'] = pll_current_language();
		}

		return $params;
	}


	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function is_compatible(): bool {
		return $this->is_plugin_active( 'polylang-pro/polylang.php' );
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function get_integration_name(): string {
		return 'Polylang';
	}
}
