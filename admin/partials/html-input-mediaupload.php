<?php
$file_url = get_post_meta($thepostid, $this->plugin_name . '_file_url', true);
?>
<p class="form-field _pdc_editable_field">
    <label for="_pdc_file_url">Static File</label>

    <a href="#" class="button upload_file_button" id="pdc-file-upload" data-choose="<?php esc_attr_e('Choose file', 'woocommerce'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'woocommerce'); ?>"><?php echo esc_html__('Choose file', 'woocommerce'); ?></a>
    <input type="text" class="input_text" id="_pdc-file_url" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="<?= $this->plugin_name; ?>_file_url" value="<?= esc_attr($file_url); ?>" />
</p>
<script>
    jQuery(document).ready(function($) {
        // Upload file button click event
        $('#pdc-file-upload').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Select or Upload a Custom File',
                button: {
                    text: 'Use this file'
                },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $('#_pdc_file_id').val(attachment.id);
                $('#_pdc-file_url').val(attachment.url);
            });

            frame.open();
        });
    });
</script>