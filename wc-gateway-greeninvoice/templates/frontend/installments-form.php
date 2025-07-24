<?php
/**
 * Morning WooCommerce Split Payments Form
 *
 * @package    Morning\WC
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.1.0
 * @since      1.1.0
 */

$gateway_id   = $this->id ?? '';
$installments = $this->settings['installments'] ?? 12;
?>
<div class="greeninvoice-installments-wrapper morning-installments-wrapper">
	<label for="<?php echo esc_attr( "{$gateway_id}_installments" ); ?>"
			class="greeninvoice-installments-label morning-installments-label">
		<?php echo esc_html_x( 'Installments', 'Installments Form', 'wc-gateway-greeninvoice' ); ?>
	</label>
	<select name="<?php echo esc_attr( "{$gateway_id}_installments" ); ?>"
			id="<?php echo esc_attr( "{$gateway_id}_installments" ); ?>"
			class="greeninvoice-installments-field morning-installments-field">

		<?php for ( $i = 1; $i <= $installments; $i ++ ) : ?>
			<option value="<?php echo esc_attr( (string) $i ); ?>"><?php echo esc_html( (string) $i ); ?></option>
		<?php endfor; ?>

	</select>
</div>
