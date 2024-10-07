<?php
/**
 * Morning Settings Page
 *
 * @package    Morning\WC
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.0.0
 */

use Morning\WC\Utilities\Settings;

if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

/* phpcs:ignore */
if ( isset( $_GET['settings-updated'] ) ) {

	add_settings_error(
		Settings::OPTIONS_KEY,
		MRN_WC_SLUG . '_notice',
		esc_html__( 'Changes were saved.', 'wc-gateway-greeninvoice' ),
		'updated'
	);

}

settings_errors( MRN_WC_SLUG . '_options' );
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">

		<?php
		settings_fields( MRN_WC_SLUG );

		do_settings_sections( MRN_WC_SLUG );

		submit_button( __( 'Save Settings' ) );
		?>

	</form>
</div>
