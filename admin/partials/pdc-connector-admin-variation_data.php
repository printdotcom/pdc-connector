<?php
$variantion_ID = $variation->ID;
$parent_ID = $variation->post_parent;

$sku = get_post_meta($parent_ID, $this->plugin_name . '_product_sku', true);
$preset_id = get_post_meta($variation->ID, $this->plugin_name . '_preset_id', true);
$preset_title = get_post_meta($variation->ID, $this->plugin_name . '_preset_title', true);
?>
<?php if (!empty($sku)) { ?>
    <div>
        <div class="options_group pdc_product_options" id="js-pdc-variant-<?= $variation->ID; ?>">
            <p class="form-field">
                <label><?php _e('Print.com Preset', $this->plugin_name); ?></label>
                <span class="pdc-ac-preset-list"></span>
                <input type="hidden" id="<?= $variantion_ID; ?>_pdc-connector_preset_id" value="<?= $preset_id; ?>" class="js-pdc-preset-id" name="pdc-connector_preset_id[<?= $index; ?>]" />
                <input type="hidden" id="<?= $variantion_ID; ?>_pdc-connector_preset_title" value="<?= $preset_title; ?>" class="js-pdc-preset-title" name="pdc-connector_preset_title[<?= $index; ?>]" />
            </p>

            <?php
            $file_url = get_post_meta($variation->ID, $this->plugin_name . '_file_url', true);
            $button_field_id = $variation->ID . '_upload_id';
            $file_field_id = $variation->ID . '_file_url';
            ?>
            <p class="form-field _pdc_editable_field">
                <label for="_pdc_file_url">PDF</label>

                <a href="#" class="button" id="<?= $button_field_id; ?>" data-choose="<?php esc_attr_e('Choose file', 'woocommerce'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'woocommerce'); ?>"><?php echo esc_html__('Choose file', 'woocommerce'); ?></a>
                <input type="text" class="input_text" id="<?= $file_field_id; ?>" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="<?= $this->plugin_name; ?>_file_url[<?= $index; ?>]" value="<?= esc_attr($file_url); ?>" />
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
                        });

                        frame.open();
                    });
                });
            </script>
        </div>
    <?php } else { ?>
        <div>
            <p>Please connect a Print.com product to this WooCommerce product first.</p>
        </div>
    <?php } ?>