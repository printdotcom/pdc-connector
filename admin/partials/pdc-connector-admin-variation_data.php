<div>
    <?php woocommerce_wp_text_input(array(
        'id' => $this->plugin_name . '_sku',
        'label' => __('Connected SKU'),
        'value'         => get_post_meta($variation->ID, $this->plugin_name . '_sku', true),
    )); ?>
    <?php woocommerce_wp_text_input(array(
        'id' => $this->plugin_name . '_preset_id',
        'label' => __('Connected Preset'),
        'value'         => get_post_meta($variation->ID, $this->plugin_name . '_preset_id', true),
    )); ?>

    <?php
    $file_url = get_post_meta($variation->ID, $this->plugin_name . '_file_url', true);
    $button_field_id = $variation->ID . '_upload_id';
    $file_field_id = $variation->ID . '_file_url';
    ?>
    <p class="form-field _pdc_editable_field">
        <label for="_pdc_file_url">PDF</label>

        <a href="#" class="button" id="<?= $button_field_id; ?>" data-choose="<?php esc_attr_e('Choose file', 'woocommerce'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'woocommerce'); ?>"><?php echo esc_html__('Choose file', 'woocommerce'); ?></a>
        <input type="text" class="input_text" id="<?= $file_field_id; ?>" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="<?= $this->plugin_name; ?>_file_url" value="<?= esc_attr($file_url); ?>" />
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