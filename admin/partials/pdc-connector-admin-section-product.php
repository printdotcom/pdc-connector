<?php
/**
 * Admin section: Product configuration
 *
 * Renders the product configuration section on the Print.com settings page.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

$pdc_connector_product_config = get_option( $this->plugin_name . '-product' );

// Handle case where option doesn't exist yet or use_preset_copies is not set.
$pdc_connector_use_preset_copies = isset( $pdc_connector_product_config['use_preset_copies'] ) ? $pdc_connector_product_config['use_preset_copies'] : false;
?>

<?php esc_html_e( 'Configure how to set up the product connection with Print.com.', 'pdc-connector' ); ?>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="pdc_use_preset_copies"><?php _e( 'Presets', 'pdc-connector' ); ?></label></th>
			<td>
				<label for="pdc_use_preset_copies">
					<input type="checkbox" id="pdc_use_preset_copies" data-testid="pdc-use_preset_copies" name="<?php echo $this->plugin_name; ?>-product[use_preset_copies]" value="1" <?php checked( $pdc_connector_use_preset_copies, true ); ?> />
					<?php _e( 'Use preset copies', 'pdc-connector' ); ?>
				</label>
				<p class="description"><?php _e( 'When enabled, the number of copies from the preset will be used instead of allowing customers to choose their own quantity.', 'pdc-connector' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>