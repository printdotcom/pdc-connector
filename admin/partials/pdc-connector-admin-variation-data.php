<?php
/**
 * Admin variation data fields
 *
 * Renders additional variation fields used to connect variations to Print.com.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Variables available in this file
 *
 * @global array           variation_data
 * @global int             $index
 * @global WP_Post $post   Global post object.
 */
$pdc_connector_preset_input_name = $pdc_connector_meta_key_preset_id . '[' . $pdc_connector_index . ']';
?>
<?php if ( ! empty( $pdc_connector_sku ) ) { ?>
	<div class="form-row">
		<div class="options_group pdc_product_options" id="js-pdc-variant-<?php echo esc_attr( $pdc_connector_variation_id ); ?>">
			<p class="form-row form-field">
				<label><?php esc_html_e( 'Print.com Preset', 'pdc-connector' ); ?></label>
				<span class="woocommerce-help-tip" tabindex="0" aria-label="<?php echo esc_attr__( 'Select a preset for this variant. When no preset is selected, it will use the default preset of this product.', 'pdc-connector' ); ?>"></span>
				<span class="pdc-ac-preset-list">
					<select class="pdc_variation_preset_select" name="<?php echo esc_attr( $pdc_connector_preset_input_name ); ?>" value="<?php echo esc_attr( $pdc_connector_preset_id ); ?>">
						<?php include plugin_dir_path( __FILE__ ) . '/' . PDC_CONNECTOR_NAME . '-admin-preset-select.php'; ?>
					</select>
				</span>
			</p>

			<?php
			$pdc_connector_pdf_url         = get_post_meta( $pdc_connector_variation_id, $pdc_connector_meta_key_pdf_url, true );
			$pdc_connector_button_field_id = PDC_CONNECTOR_NAME . '_' . $pdc_connector_variation_id . '_upload_id';
			$pdc_connector_file_field_id   = PDC_CONNECTOR_NAME . '_' . $pdc_connector_variation_id . '_pdf_url';
			?>
			<p class="form-row form-field _pdc_editable_field">
				<label for="<?php echo esc_attr( $pdc_connector_file_field_id ); ?>"><?php esc_html_e( 'PDF', 'pdc-connector' ); ?></label>
				<span class="woocommerce-help-tip" tabindex="0" aria-label="<?php echo esc_attr__( 'Enter a URL or select a file which belongs to this variant. This file will be the design which the customer will order.', 'pdc-connector' ); ?>"></span>
				<span class="form-flex-box">
					<input type="text" class="input_text" id="<?php echo esc_attr( $pdc_connector_file_field_id ); ?>" placeholder="<?php esc_attr_e( 'http://', 'pdc-connector' ); ?>" name="<?php echo esc_attr( $pdc_connector_meta_key_pdf_url ); ?>[<?php echo esc_attr( $pdc_connector_index ); ?>]" value="<?php echo esc_attr( $pdc_connector_pdf_url ); ?>" />
					<a
						href="#"
						data-pdc-variation-file-field="<?php echo esc_attr( $pdc_connector_file_field_id ); ?>"
						data-choose="<?php esc_attr_e( 'Choose file', 'pdc-connector' ); ?>"
						data-update="<?php esc_attr_e( 'Insert file URL', 'pdc-connector' ); ?>"
						class="button pdc-connector-js-upload-custom-file-btn"
						id="<?php echo esc_attr( $pdc_connector_button_field_id ); ?>">
						<?php echo esc_html__( 'Choose file', 'pdc-connector' ); ?>
					</a>
				</span>
			</p>
		</div>
	</div>
<?php } else { ?>
	<div>
		<p><?php esc_html_e( 'Please connect a Print.com product to this WooCommerce product first.', 'pdc-connector' ); ?></p>
	</div>
<?php } ?>