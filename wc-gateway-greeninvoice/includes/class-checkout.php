<?php
/**
 * Class Checkout
 *
 * @package    Morning\WC
 * @subpackage Checkout
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.2.1
 * @since      1.4.0
 */

namespace Morning\WC;

use Exception;
use Morning\WC\Config\Settings;
use Morning\WC\Formatters\Tax_ID_Formatter;
use WP_Error;

defined( 'ABSPATH' ) || exit;


/**
 * Class Checkout
 *
 * @package Morning\WC
 */
class Checkout {
	/**
	 * @var Settings
	 *
	 * @since 2.0.0
	 */
	private Settings $settings;

	/**
	 * @var string
	 *
	 * @since 2.2.1
	 */
	const TAX_ID_FIELD = MRN_WC_SLUG . '/billing_tax_id';


	/**
	 * Checkout constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 1.4.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function register_hooks(): void {
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'maybe_validate_israeli_tax_id' ], 10, 2 );
		add_action( 'woocommerce_init', [ $this, 'maybe_inject_block_tax_id_field' ] );
		add_action( 'woocommerce_blocks_validate_location_address_fields', [ $this, 'validate_tax_id_field' ], 10, 2 );

		add_filter( 'woocommerce_checkout_fields', [ $this, 'maybe_inject_tax_id_field' ] );
		add_filter( 'woocommerce_sanitize_additional_field', [ $this, 'sanitize_tax_id_field' ], 10, 2 );
	}


	/**
	 * @param array $fields List of registered checkout fields.
	 *
	 * @return array
	 *
	 * @since 1.4.0
	 */
	public function maybe_inject_tax_id_field( array $fields ): array {
		$options = $this->settings->get_options();

		if ( $options->is_show_tax_id_field() ) {
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

	/**
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.2.1
	 */
	public function maybe_inject_block_tax_id_field(): void {
		$options = $this->settings->get_options();

		if ( ! $options->is_show_tax_id_field() ) {
			return;
		}

		if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
			return;
		}

		woocommerce_register_additional_checkout_field(
			[
				'id'       => self::TAX_ID_FIELD,
				'label'    => esc_html__( 'Tax ID', 'wc-gateway-greeninvoice' ),
				'location' => 'address',
				'type'     => 'text',
				'required' => false,
			]
		);
	}


	/**
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

		if ( ! $this->validate_tax_id( $tax_id, $country ) ) {
			$errors->add( 'billing_tax_id_validation', esc_html__( 'Tax ID number is invalid.', 'wc-gateway-greeninvoice' ), [ 'id' => 'billing_tax_id' ] );
		}
	}

	/**
	 * @param WP_Error $errors Validation errors.
	 * @param array $fields Fields data.
	 *
	 * @return void
	 *
	 * @since 2.2.1
	 */
	public function validate_tax_id_field( WP_Error $errors, array $fields ): void {
		if ( empty( $fields[ self::TAX_ID_FIELD ] ) || empty( $fields['country'] ) ) {
			return;
		}

		if ( ! $this->validate_tax_id( $fields[ self::TAX_ID_FIELD ], $fields['country'] ) ) {
			$errors->add( self::TAX_ID_FIELD, esc_html__( 'Tax ID number is invalid.', 'wc-gateway-greeninvoice' ) );
		}
	}


	/**
	 * @param mixed $field_value Field value.
	 * @param string $field_key Field key.
	 *
	 * @return string
	 *
	 * @since 2.2.1
	 */
	public function sanitize_tax_id_field( $field_value, string $field_key ): string {
		if ( self::TAX_ID_FIELD === $field_key ) {
			return sanitize_text_field( $field_value );
		}

		return $field_value;
	}


	/**
	 * @param string $tax_id Tax id.
	 * @param string $country Country ISO code.
	 *
	 * @return bool
	 *
	 * @since 2.2.1
	 */
	private function validate_tax_id( string $tax_id, string $country ): bool {
		if ( 'IL' !== $country ) {
			return true;
		}

		if ( strlen( $tax_id ) < 5 || strlen( $tax_id ) > 9 ) {
			return false;
		}

		$tax_id = Tax_ID_Formatter::format( $tax_id, [ 'country' => $country ] );
		$agg    = 0;

		for ( $i = 0; $i < 9; $i ++ ) {
			$digit = (int) $tax_id[ $i ];
			$num   = ( $i % 2 + 1 ) * $digit;

			$agg += ( $num > 9 ) ? $num - 9 : $num;
		}

		return 0 === $agg % 10;
	}
}
