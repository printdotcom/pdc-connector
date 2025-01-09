<?php
$variantion_ID = $variation->ID;
$parent_ID = $variation->post_parent;

$meta_key_pdf_url = $this->get_meta_key('pdf_url');
$meta_key_sku = $this->get_meta_key('product_sku');
$meta_key_preset_id = $this->get_meta_key('preset_id');
$meta_key_preset_title = $this->get_meta_key('preset_title');

$sku = get_post_meta($parent_ID, $meta_key_sku, true);
$preset_id = get_post_meta($variation->ID, $meta_key_preset_id, true);
$preset_title = get_post_meta($variation->ID, $meta_key_preset_title, true);
?>
<?php if (!empty($sku)) { ?>
    <div class="form-row">
        <div class="options_group pdc_product_options" id="js-pdc-variant-<?= $variation->ID; ?>">
            <p class="form-row form-field">
                <label><?php _e('Print.com Preset', $this->plugin_name); ?></label>
                <span class="woocommerce-help-tip" tabindex="0" aria-label="Select a preset for this variant. When no preset is selected, it will use the default preset of this product."></span>
                <span class="pdc-ac-preset-list"></span>
                <input type="hidden" id="<?= $variantion_ID; ?>_<?= $meta_key_preset_id; ?>" value="<?= $preset_id; ?>" class="js-pdc-preset-id" name="<?= $meta_key_preset_id; ?>[<?= $index; ?>]" />
                <input type="hidden" id="<?= $variantion_ID; ?>_<?= $meta_key_preset_title; ?>" value="<?= $preset_title; ?>" class="js-pdc-preset-title" name="<?= $meta_key_preset_title; ?>[<?= $index; ?>]" />
            </p>

            <?php
            $pdf_url = get_post_meta($variation->ID, $meta_key_pdf_url, true);
            $button_field_id = $variation->ID . '_upload_id';
            $file_field_id = $variation->ID . '_pdf_url';
            ?>
            <p class="form-row form-field _pdc_editable_field">
                <label for="<?= $file_field_id; ?>">PDF</label>
                <span class="woocommerce-help-tip" tabindex="0" aria-label="Enter a URL or select a file which belongs to this variant. This file will be the design which the customer will order."></span>
                <span class="form-flex-box">
                    <input type="text" class="input_text" id="<?= $file_field_id; ?>" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="<?= $meta_key_pdf_url; ?>[<?= $index; ?>]" value="<?= esc_attr($pdf_url); ?>" />
                    <a href="#" class="button button-select-pdf-file" id="<?= $button_field_id; ?>" data-choose="<?php esc_attr_e('Choose file', 'woocommerce'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'woocommerce'); ?>"><?php echo esc_html__('Choose file', 'woocommerce'); ?></a>
                </span>
            </p>
            <script>
                jQuery(document).ready(function($) {
                    // Upload file button click event
                    $('#<?= $button_field_id; ?>').on('click', function(e) {
                        e.preventDefault();
                        const frame = wp.media({
                            title: 'Select or Upload a Custom File',
                            button: {
                                text: 'Use this file'
                            },
                            library: {
                                type: 'document',
                                post_mime_type: ['application/pdf']
                            },
                            multiple: false
                        });

                        frame.on('select', function() {
                            const attachment = frame.state().get('selection').first().toJSON();
                            $("#<?= $file_field_id; ?>").val(attachment.url);

                            $('.woocommerce_variation').addClass('variation-needs-update');
                            $('button.cancel-variation-changes, button.save-variation-changes').prop('disabled', false);
                            $('#variable_product_options').trigger('woocommerce_variations_input_changed');
                        });

                        frame.open();
                    });

                    $('.woocommerce_variations .woocommerce-help-tip')
                        .tipTip({
                            attribute: 'data-tip',
                            fadeIn: 50,
                            fadeOut: 50,
                            delay: 200,
                        });
                });
            </script>
        </div>
    <?php } else { ?>
        <div>
            <p>Please connect a Print.com product to this WooCommerce product first.</p>
        </div>
    <?php } ?>