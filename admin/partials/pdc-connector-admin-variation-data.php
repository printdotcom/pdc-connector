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

/**
 * Variables available in this file
 *
 * @global array           variation_data
 * @global int             $index
 * @global WP_Post $post   Global post object.
 */
global $post;

$pdc_connector_variation_id = isset( $variation->ID ) ? intval( $variation->ID ) : 0;
$pdc_connector_parent_id    = isset( $variation->post_parent ) ? intval( $variation->post_parent ) : 0;

$pdc_connector_meta_key_pdf_url      = $this->get_meta_key( 'pdf_url' );
$pdc_connector_meta_key_sku          = $this->get_meta_key( 'product_sku' );
$pdc_connector_meta_key_preset_id    = $this->get_meta_key( 'preset_id' );
$pdc_connector_meta_key_preset_title = $this->get_meta_key( 'preset_title' );

$pdc_connector_index = isset( $index ) ? intval( $index ) : 0;

$pdc_connector_sku          = get_post_meta( $pdc_connector_parent_id, $pdc_connector_meta_key_sku, true );
$pdc_connector_preset_id    = get_post_meta( $pdc_connector_variation_id, $pdc_connector_meta_key_preset_id, true );
$pdc_connector_preset_title = get_post_meta( $pdc_connector_variation_id, $pdc_connector_meta_key_preset_title, true );
?>
<?php if ( ! empty( $pdc_connector_sku ) ) { ?>
	<div class="form-row">
		<div class="options_group pdc_product_options" id="js-pdc-variant-<?php echo esc_attr( $pdc_connector_variation_id ); ?>">
			<p class="form-row form-field">
				<label><?php esc_html_e( 'Print.com Preset', 'pdc-connector' ); ?></label>
				<span class="woocommerce-help-tip" tabindex="0" aria-label="<?php echo esc_attr__( 'Select a preset for this variant. When no preset is selected, it will use the default preset of this product.', 'pdc-connector' ); ?>"></span>
				<span class="pdc-ac-preset-list"></span>
				<input type="hidden" id="<?php echo esc_attr( $pdc_connector_variation_id . '_' . $pdc_connector_meta_key_preset_id ); ?>" value="<?php echo esc_attr( $pdc_connector_preset_id ); ?>" class="js-pdc-preset-id" name="<?php echo esc_attr( $pdc_connector_meta_key_preset_id ); ?>[<?php echo esc_attr( $pdc_connector_index ); ?>]" />
				<input type="hidden" id="<?php echo esc_attr( $pdc_connector_variation_id . '_' . $pdc_connector_meta_key_preset_title ); ?>" value="<?php echo esc_attr( $pdc_connector_preset_title ); ?>" class="js-pdc-preset-title" name="<?php echo esc_attr( $pdc_connector_meta_key_preset_title ); ?>[<?php echo esc_attr( $pdc_connector_index ); ?>]" />
			</p>

			<?php
			$pdc_connector_pdf_url         = get_post_meta( $pdc_connector_variation_id, $pdc_connector_meta_key_pdf_url, true );
			$pdc_connector_button_field_id = $pdc_connector_variation_id . '_upload_id';
			$pdc_connector_file_field_id   = $pdc_connector_variation_id . '_pdf_url';
			?>
			<p class="form-row form-field _pdc_editable_field">
				<label for="<?php echo esc_attr( $pdc_connector_file_field_id ); ?>"><?php esc_html_e( 'PDF', 'pdc-connector' ); ?></label>
				<span class="woocommerce-help-tip" tabindex="0" aria-label="<?php echo esc_attr__( 'Enter a URL or select a file which belongs to this variant. This file will be the design which the customer will order.', 'pdc-connector' ); ?>"></span>
				<span class="form-flex-box">
					<input type="text" class="input_text" id="<?php echo esc_attr( $pdc_connector_file_field_id ); ?>" placeholder="<?php esc_attr_e( 'http://', 'pdc-connector' ); ?>" name="<?php echo esc_attr( $pdc_connector_meta_key_pdf_url ); ?>[<?php echo esc_attr( $pdc_connector_index ); ?>]" value="<?php echo esc_attr( $pdc_connector_pdf_url ); ?>" />
					<a href="#" class="button" id="<?php echo esc_attr( $pdc_connector_button_field_id ); ?>" data-choose="<?php esc_attr_e( 'Choose file', 'pdc-connector' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'pdc-connector' ); ?>"><?php echo esc_html__( 'Choose file', 'pdc-connector' ); ?></a>
				</span>
			</p>
			<script>
				jQuery(document).ready(function($) {
					// Upload file button click event.
					$('#<?php echo esc_js( $pdc_connector_button_field_id ); ?>').on('click', function(e) {
						e.preventDefault();
						const frame = wp.media({
							title: 'Select or Upload a Custom File',
							button: { text: 'Use this file' },
							library: { type: 'document', post_mime_type: ['application/pdf'] },
							multiple: false
						});
						frame.on('select', function() {
							const attachment = frame.state().get('selection').first().toJSON();
							$("#<?php echo esc_js( $pdc_connector_file_field_id ); ?>").val(attachment.url);
							$('.woocommerce_variation').addClass('variation-needs-update');
							$('button.cancel-variation-changes, button.save-variation-changes').prop('disabled', false);
							$('#variable_product_options').trigger('woocommerce_variations_input_changed');
						});
						frame.open();
					});
					$('.woocommerce_variations .woocommerce-help-tip').tipTip({ attribute: 'data-tip', fadeIn: 50, fadeOut: 50, delay: 200 });
				});
			</script>
		</div>
	<?php } else { ?>
		<div>
			<p><?php esc_html_e( 'Please connect a Print.com product to this WooCommerce product first.', 'pdc-connector' ); ?></p>
		</div>
	<?php } ?>
