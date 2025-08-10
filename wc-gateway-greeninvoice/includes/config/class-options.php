<?php
/**
 * Class Options
 *
 * @package    Morning\WC\Config
 * @subpackage Options
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.2.0
 * @since      2.0.0
 */

namespace Morning\WC\Config;

use Morning\WC\Enum\Plan_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Options
 *
 * @package Morning\WC\Config
 */
class Options {
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $activated = 'no';
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $license_key = '';
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $gateways = [];
	/**
	 * @var bool
	 *
	 * @since 2.0.0
	 */
	private bool $show_tax_id_field = false;
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $order_status = 'processing';
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	private int $plan = Plan_Type::BASIC;
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $invoicing_order_status = 'processing';
	/**
	 * @var string[]
	 *
	 * @since 2.2.0
	 */
	private array $invoicing_allowed_gateways = [];
	/**
	 * @var bool
	 *
	 * @since 2.0.0
	 */
	private bool $sandbox_mode = false;

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	const OPTIONS_KEY = MRN_WC_SLUG . '_options';


	/**
	 * Options constructor.
	 *
	 * @param array|null $options Plugin options.
	 *
	 * @since 2.0.0
	 */
	public function __construct( array $options = null ) {
		if ( is_array( $options ) ) {
			$this->parse( $options );
		} else {
			$this->fetch();
		}
	}


	/**
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function fetch(): void {
		$options = get_option( self::OPTIONS_KEY );

		if ( is_array( $options ) ) {
			$this->parse( $options );
		}
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function save(): bool {
		return update_option(
			self::OPTIONS_KEY,
			[
				'activated'                  => $this->get_activated(),
				'license_key'                => $this->get_license_key(),
				'gateways'                   => $this->get_gateways(),
				'show_tax_id_field'          => $this->is_show_tax_id_field(),
				'order_status'               => $this->get_order_status(),
				'plan'                       => $this->get_plan(),
				'invoicing_order_status'     => $this->get_invoicing_order_status(),
				'invoicing_allowed_gateways' => $this->get_invoicing_allowed_gateways(),
				'sandbox_mode'               => $this->is_sandbox_mode() ? 'yes' : 'no',
			]
		);
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_license_valid(): bool {
		return 'yes' === $this->get_activated();
	}

	/**
	 * @param int $gateway_id Payment gateway id.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_payment_gateway_enabled( int $gateway_id ): bool {
		$gateways = $this->get_gateways();

		return 'yes' === ( $gateways[ $gateway_id ] ?? 'no' );
	}

	/**
	 * @param string $gateway_id Payment gateway id.
	 *
	 * @return bool
	 *
	 * @since 2.2.0
	 */
	public function is_payment_gateway_allowed( string $gateway_id ): bool {
		$gateways = $this->get_invoicing_allowed_gateways();

		return 'yes' === ( $gateways[ $gateway_id ] ?? 'no' );
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_basic_mode(): bool {
		return Plan_Type::BASIC === $this->plan;
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_clearing_mode(): bool {
		return Plan_Type::CLEARING === $this->plan;
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_invoicing_mode(): bool {
		return Plan_Type::INVOICING === $this->plan;
	}


	/**
	 * @param array $options Plugin options.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function parse( array $options ) {
		if ( isset( $options['activated'] ) ) {
			$this->set_activated( $options['activated'] );
		}

		if ( isset( $options['license_key'] ) ) {
			$this->set_license_key( $options['license_key'] );
		}

		if ( isset( $options['gateways'] ) ) {
			$this->set_gateways( $options['gateways'] );
		}

		if ( isset( $options['show_tax_id_field'] ) ) {
			$this->set_show_tax_id_field( 'yes' === $options['show_tax_id_field'] );
		}

		if ( isset( $options['order_status'] ) ) {
			$this->set_order_status( $options['order_status'] );
		}

		if ( isset( $options['plan'] ) ) {
			$this->set_plan( $options['plan'] );
		}

		if ( isset( $options['invoicing_order_status'] ) ) {
			$this->set_invoicing_order_status( $options['invoicing_order_status'] );
		}

		if ( isset( $options['invoicing_allowed_gateways'] ) ) {
			$this->set_invoicing_allowed_gateways( $options['invoicing_allowed_gateways'] );
		}

		if ( isset( $options['sandbox_mode'] ) ) {
			$this->set_sandbox_mode( 'yes' === $options['sandbox_mode'] );
		}
	}


	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_activated(): string {
		return $this->activated;
	}

	/**
	 * @param string $activated
	 *
	 * @since 2.0.0
	 */
	public function set_activated( string $activated ): void {
		$this->activated = $activated;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_license_key(): string {
		return $this->license_key;
	}

	/**
	 * @param string $license_key
	 *
	 * @since 2.0.0
	 */
	public function set_license_key( string $license_key ): void {
		$this->license_key = $license_key;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_gateways(): array {
		return $this->gateways;
	}

	/**
	 * @param array $gateways
	 *
	 * @since 2.0.0
	 */
	public function set_gateways( array $gateways ): void {
		$this->gateways = $gateways;
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_show_tax_id_field(): bool {
		return $this->show_tax_id_field;
	}

	/**
	 * @param bool $show_tax_id_field
	 *
	 * @since 2.0.0
	 */
	public function set_show_tax_id_field( bool $show_tax_id_field ): void {
		$this->show_tax_id_field = $show_tax_id_field;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_order_status(): string {
		return $this->order_status;
	}

	/**
	 * @param string $order_status
	 *
	 * @since 2.0.0
	 */
	public function set_order_status( string $order_status ): void {
		$this->order_status = $order_status;
	}

	/**
	 * @return int
	 *
	 * @since 2.0.0
	 */
	public function get_plan(): int {
		return $this->plan;
	}

	/**
	 * @param int $plan
	 *
	 * @since 2.0.0
	 */
	public function set_plan( int $plan ): void {
		$this->plan = $plan;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_invoicing_order_status(): string {
		return $this->invoicing_order_status;
	}

	/**
	 * @param string $invoicing_order_status
	 *
	 * @since 2.0.0
	 */
	public function set_invoicing_order_status( string $invoicing_order_status ): void {
		$this->invoicing_order_status = $invoicing_order_status;
	}

	/**
	 * @return string[]
	 *
	 * @since 2.2.0
	 */
	public function get_invoicing_allowed_gateways(): array {
		return $this->invoicing_allowed_gateways;
	}

	/**
	 * @param string[] $invoicing_allowed_gateways
	 *
	 * @since 2.2.0
	 */
	public function set_invoicing_allowed_gateways( array $invoicing_allowed_gateways ): void {
		$this->invoicing_allowed_gateways = $invoicing_allowed_gateways;
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_sandbox_mode(): bool {
		return $this->sandbox_mode;
	}

	/**
	 * @param bool $sandbox_mode
	 *
	 * @since 2.0.0
	 */
	public function set_sandbox_mode( bool $sandbox_mode ): void {
		$this->sandbox_mode = $sandbox_mode;
	}
}
