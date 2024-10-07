<?php
/**
 * Morning WooCommerce Split Payments Form
 *
 * @package    Morning\WC
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.2.0
 * @since      1.1.0
 */

$installments = $installments ?? 1;
?>
<div id="<?php echo esc_attr( MRN_WC_SLUG . '_installments_form' ); ?>"
		class="greeninvoice-installments-wrapper morning-installments-wrapper">
	<form action="" method="post">
		<h2 class="greeninvoice-installments-title morning-installments-title"><?php echo esc_html_x( 'Select Number of Installments', 'Installments Form', 'wc-gateway-greeninvoice' ); ?></h2>
		<label for="<?php echo esc_attr( MRN_WC_SLUG . '_installments' ); ?>"
				class="greeninvoice-installments-label morning-installments-label">
			<?php echo esc_html_x( 'Installments', 'Installments Form', 'wc-gateway-greeninvoice' ); ?>
		</label>
		<select name="<?php echo esc_attr( MRN_WC_SLUG . '_installments' ); ?>"
				class="greeninvoice-installments-field morning-installments-field">

			<?php for ( $i = 1; $i <= $installments; $i ++ ) : ?>
				<option value="<?php echo esc_attr( (string) $i ); ?>"><?php echo esc_html( (string) $i ); ?></option>
			<?php endfor; ?>

		</select>
		<button type="submit"
				class="greeninvoice-installments-submit morning-installments-submit"><?php echo esc_html_x( 'Proceed to Payment', 'Installments Form', 'wc-gateway-greeninvoice' ); ?></button>
	</form>
</div>
