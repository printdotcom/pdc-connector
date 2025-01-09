<?php
$file_url = get_post_meta($thepostid, $this->plugin_name . '_file_url', true);
$meta_key_pdf_url = $this->get_meta_key('pdf_url');
?>
<p class="form-field _pdc_editable_field">
    <label for="_pdc_file_url">PDF</label>
    <span class="form-flex-box">
        <input type="text" class="input_text pdc_input_pdf" id="_pdc-file_url" placeholder="<?php esc_attr_e('http://', 'pdc-connector'); ?>" name="<?= $meta_key_pdf_url; ?>" value="<?= esc_attr($file_url); ?>" />
        <a href="#" class="button button-select-pdf-file" id="pdc-product-file-upload" data-choose="<?php esc_attr_e('Choose file', 'pdc-connector'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'pdc-connector'); ?>"><?php echo esc_html__('Choose file', 'pdc-connector'); ?></a>
    </span>
</p>
<script>
    jQuery(document).ready(function($) {
        // Upload file button click event
        $('#pdc-product-file-upload').on('click', function(e) {
            e.preventDefault();
            const mediaUploadModal = wp.media({
                title: 'Select or Upload a Custom PDF',
                button: {
                    text: 'Select File',
                },
                library: {
                    type: 'document',
                    post_mime_type: ['application/pdf']
                },
                multiple: false,
            });

            mediaUploadModal.on('select', function() {
                const attachment = mediaUploadModal.state().get('selection').first().toJSON();
                $('#_pdc_file_id').val(attachment.id);
                $('#_pdc-file_url').val(attachment.url);
            });

            mediaUploadModal.open();
        });
    });
</script>