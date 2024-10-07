<?php
/**
 * Class Polylang_Integration
 *
 * @package    Morning\WC\Integrations
 * @subpackage Polylang_Integration
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.4.0
 */

namespace Morning\WC\Integrations;

defined( 'ABSPATH' ) || exit;


/**
 * Class Polylang_Integration
 *
 * @package Morning\WC\Integrations
 */
class Polylang_Integration {
	/**
	 * Polylang_Integration constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'morning/wc/get_gateway_url_params', [ $this, 'inject_lang_param' ] );
	}


	/**
	 * Inject language parameter to gateway return url.
	 *
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
}
