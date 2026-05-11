<?php
/**
 * Class Notices_Manager
 *
 * @package    Morning\WC\Notices
 * @subpackage Notices_Manager
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.0
 * @since      2.4.0
 */

namespace Morning\WC\Notices;

use Morning\WC\Base\Base_Notice;
use Morning\WC\Config\Settings;
use Morning\WC\Enum\Notice_Type;
use Morning\WC\Exceptions\Container_Exception;

defined( 'ABSPATH' ) || exit;


/**
 * Class Notices_Manager
 *
 * @package Morning\WC\Notices
 */
final class Notices_Manager {
	/**
	 * @var Settings
	 *
	 * @since 2.4.0
	 */
	private Settings $settings;


	/**
	 * Notices_Manager constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 2.4.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.4.0
	 */
	private function register_hooks(): void {
		add_action( 'admin_init', [ $this, 'load_notices' ] );
		add_action( 'morning/wc/settings_page/before_form', [ $this, 'render' ] );

		add_action( 'wp_ajax_morning_dismiss_notice', [ $this, 'handle_dismiss' ] );
	}


	/**
	 * @return void
	 *
	 * @throws Container_Exception
	 *
	 * @since 2.4.0
	 */
	public function load_notices(): void {
		$container = mrn_get_container();

		$container->get( License_Notice::class );
		$container->get( Sandbox_Notice::class );
		$container->get( Tax_Authority_Notice::class );
	}


	/**
	 * @return void
	 *
	 * @since 2.4.0
	 */
	public function render(): void {
		$options = $this->settings->get_options();
		$notices = apply_filters( 'morning/wc/registered_notices', [] );

		foreach ( $notices as $notice ) {
			if ( ! $notice instanceof Base_Notice ) {
				continue;
			}

			if ( ! $notice->should_display() ) {
				continue;
			}

			if ( $notice->is_dismissible() && $options->is_notice_dismissed( $notice->get_id() ) ) {
				continue;
			}

			$this->render_notice( $notice );
		}
	}


	/**
	 * @return void
	 *
	 * @since 2.4.0
	 */
	public function handle_dismiss(): void {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'morning-dismiss-notice' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce.', 'wc-gateway-greeninvoice' ) ], 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'You are not allowed to perform this action.', 'wc-gateway-greeninvoice' ) ], 403 );
		}

		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_key( wp_unslash( $_POST['notice_id'] ) ) : '';

		if ( '' === $notice_id ) {
			wp_send_json_error( [ 'message' => __( 'Missing notice id.', 'wc-gateway-greeninvoice' ) ], 400 );
		}

		$options = $this->settings->get_options();
		$options->add_dismissed_notice( $notice_id );
		$options->save();

		wp_send_json_success( [ 'notice_id' => $notice_id ] );
	}


	/**
	 * @return string
	 *
	 * @since 2.4.0
	 */
	public static function get_dismiss_nonce(): string {
		return wp_create_nonce( 'morning-dismiss-notice' );
	}


	/**
	 * @param Base_Notice $notice
	 *
	 * @return void
	 *
	 * @since 2.4.0
	 */
	private function render_notice( Base_Notice $notice ): void {
		$classes = [
			'morning-notice',
			'morning-notice--' . $notice->get_type(),
		];

		if ( $notice->is_dismissible() ) {
			$classes[] = 'is-dismissible';
		}

		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<div class="%1$s" data-notice-id="%2$s"><img class="morning-notice__icon" src="%3$s" alt="" aria-hidden="true" /><div class="morning-notice__content">%4$s</div>%5$s</div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $notice->get_id() ),
			esc_url( $this->icon_url( $notice->get_type() ) ),
			wp_kses_post( $notice->get_body() ),
			$notice->is_dismissible() ? $this->dismiss_button_html( $notice->get_id() ) : ''
		);
		// @phpcs:enable
	}


	/**
	 * @param string $notice_id
	 *
	 * @return string
	 *
	 * @since 2.4.0
	 */
	private function dismiss_button_html( string $notice_id ): string {
		return sprintf(
			'<button type="button" class="morning-notice__dismiss" data-action="morning_dismiss_notice" data-notice-id="%1$s" aria-label="%2$s"><span class="screen-reader-text">%2$s</span></button>',
			esc_attr( $notice_id ),
			esc_attr__( 'Dismiss this notice', 'wc-gateway-greeninvoice' )
		);
	}


	/**
	 * @param string $type
	 *
	 * @return string
	 *
	 * @since 2.4.0
	 */
	private function icon_url( string $type ): string {
		$type = Notice_Type::is_valid( $type ) ? $type : Notice_Type::INFO;

		return MRN_WC_URL . "assets/images/icons/notice-{$type}.svg";
	}
}
