<?php
/**
 * Credentials settings section partial
 *
 * Renders the settings fields for API credentials and environment.
 *
 * @package Pdc_Pod
 * @subpackage Admin\Partials
 * @since 1.0.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$pdc_pod_purchasing_settings     = get_option( PDC_POD_NAME . '-purchasing' );
$pdc_pod_purchasing_payment = isset($pdc_pod_purchasing_settings['purchase-payment']) ? $pdc_pod_purchasing_settings['purchase-payment'] : 'banktransfer';
$pdc_pod_purchasing_auto_purchase = isset($pdc_pod_purchasing_settings['auto-purchase']) ? $pdc_pod_purchasing_settings['auto-purchase'] : 'manual';
?>

<p>
	<?php esc_html_e( 'Settings related to placing orders at Print.com.', 'pdc-pod' ); ?>
</p>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="pdc_pod_auto_purchase"><?php esc_html_e( 'Automatic Purchasing', 'pdc-pod' ); ?></label>
				<span><?php esc_html_e( 'Available soon.', 'pdc-pod' ); ?></span>
			</th>
			<td>
				<select id="pdc_pod_auto_purchase" name="<?php echo esc_attr( PDC_POD_NAME ); ?>-purchasing[auto-purchase]" disabled>
					<option value="manual" <?php selected( $pdc_pod_purchasing_auto_purchase, 'manual' ); ?>><?php esc_html_e( 'Manually', 'pdc-pod' ); ?></option>
					<option value="automatic" <?php selected( $pdc_pod_purchasing_auto_purchase, 'automatic' ); ?>><?php esc_html_e( 'Automatically', 'pdc-pod' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="pdc_pod_purchase_payment"><?php esc_html_e( 'Payment Method', 'pdc-pod' ); ?></label>
				<span><?php esc_html_e( 'Select the preferred payment method for orders at Print.com.', 'pdc-pod' ); ?></span>
			</th>
			<td>
				<select id="pdc_pod_purchase_payment" name="<?php echo esc_attr( PDC_POD_NAME ); ?>-purchasing[purchase-payment]" >
					<option value="banktransfer" <?php selected( $pdc_pod_purchasing_payment, 'banktransfer' ); ?>><?php esc_html_e( 'Invoice', 'pdc-pod' ); ?></option>
					<option value="psp" <?php selected( $pdc_pod_purchasing_payment, 'psp' ); ?>><?php esc_html_e( 'Payment Link', 'pdc-pod' ); ?></option>
					<option value="directdebit" <?php selected( $pdc_pod_purchasing_payment, 'directdebit' ); ?>><?php esc_html_e( 'SEPA Direct Debit', 'pdc-pod' ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
