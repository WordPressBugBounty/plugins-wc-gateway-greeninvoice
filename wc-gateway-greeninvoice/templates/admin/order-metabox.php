<?php
/**
 * Morning WooCommerce Order Meta Box
 *
 * @package    Morning\WC
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.4.1
 * @since      1.0.0
 */

use Morning\WC\Enum\Setting;
use Morning\WC\Utilities\Settings;

$order_meta_labels = $order_meta_labels ?? [];
$order_meta        = $order_meta ?? [];

$document_view_link = Settings::is_enabled( Setting::SANDBOX ) ?
	"https://app.sandbox.d.greeninvoice.co.il/incomes/documents/${order_meta['id']}" :
	"https://app.greeninvoice.co.il/incomes/documents/${order_meta['id']}";

$transaction_view_link = Settings::is_enabled( Setting::SANDBOX ) ?
	"https://app.sandbox.d.greeninvoice.co.il/incomes/transactions/${order_meta['transaction_id']}" :
	"https://app.greeninvoice.co.il/incomes/transactions/${order_meta['transaction_id']}";
?>
<div class="morning-invoice-metabox">

	<?php foreach ( $order_meta_labels as $key => $label ) : ?>
		<p class="form-row">
			<label for="<?php echo esc_attr( MRN_WC_SLUG . '_' . $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<input type="text" class="input-text" value="<?php echo esc_attr( $order_meta[ $key ] ); ?>"
					id="<?php echo esc_attr( MRN_WC_SLUG . '_' . $key ); ?>" disabled>
		</p>
	<?php endforeach; ?>

	<p class="form-row actions-view">
		<?php if ( ! empty( $order_meta['id'] ) ) : ?>
			<a href="<?php echo esc_attr( $document_view_link ); ?>" target="_blank" class="button">
				<?php echo esc_html_x( 'View Invoice', 'Admin Meta Box', 'wc-gateway-greeninvoice' ); ?>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $order_meta['transaction_id'] ) ) : ?>
			<a href="<?php echo esc_attr( $transaction_view_link ); ?>" target="_blank" class="button">
				<?php echo esc_html_x( 'View Transaction', 'Admin Meta Box', 'wc-gateway-greeninvoice' ); ?>
			</a>
		<?php endif; ?>
	</p>
</div>
