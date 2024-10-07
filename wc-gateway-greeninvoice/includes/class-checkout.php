<?php
/**
 * Class Compatibility
 *
 * @package    Morning\WC
 * @subpackage Checkout
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.0
 * @since      1.4.0
 */

namespace Morning\WC;

use Morning\WC\Enum\Setting;
use Morning\WC\Formatters\Tax_ID_Formatter;
use Morning\WC\Utilities\Settings;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class Checkout
 *
 * @package Morning\WC
 */
class Checkout {
	/**
	 * Checkout constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'maybe_validate_israeli_tax_id' ], 10, 2 );

		add_filter( 'woocommerce_checkout_fields', [ $this, 'maybe_inject_tax_id_field' ] );
	}


	/**
	 * Maybe validate tax id field value.
	 *
	 * @param array $data Posted checkout data.
	 * @param WP_Error $errors Validation errors object.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	public function maybe_validate_israeli_tax_id( array $data, WP_Error $errors ): void {
		if ( empty( $data['billing_tax_id'] ) || empty( $data['billing_country'] ) ) {
			return;
		}

		$country = trim( $data['billing_country'] );
		$tax_id  = trim( $data['billing_tax_id'] );

		if ( 'IL' !== $country ) {
			return;
		}

		if ( strlen( $tax_id ) < 5 || strlen( $tax_id ) > 9 ) {
			$valid = false;
		} else {
			$tax_id = Tax_ID_Formatter::format( $tax_id, [ 'country' => $country ] );
			$agg    = 0;

			for ( $i = 0; $i < 9; $i ++ ) {
				$digit = (int) $tax_id[ $i ];
				$num   = ( $i % 2 + 1 ) * $digit;

				$agg += ( $num > 9 ) ? $num - 9 : $num;
			}

			$valid = 0 === $agg % 10;
		}

		if ( ! $valid ) {
			$errors->add( 'billing_tax_id_validation', esc_html__( 'Tax ID number is invalid.', 'wc-gateway-greeninvoice' ), [ 'id' => 'billing_tax_id' ] );
		}
	}


	/**
	 * Maybe inject tax id field to billing checkout form.
	 *
	 * @param array $fields List of registered checkout fields.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function maybe_inject_tax_id_field( array $fields ): array {
		if ( Settings::is_enabled( Setting::SHOW_TAX_ID_FIELD ) ) {
			$fields['billing']['billing_tax_id'] = [
				'label'    => esc_html__( 'Tax ID', 'wc_gateway_greeninvoice' ),
				'priority' => 29,
				'required' => false,
				'class'    => 'form-row-wide',
				'validate' => [ 'israel_tax_id' ],
			];
		}

		return $fields;
	}
}
