<?php
/**
 * Admin HTML partial: media upload input
 *
 * Provides a reusable input with media uploader for selecting a PDF URL.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

$file_url_meta_key = $this->get_meta_key( 'pdf_url' );
$file_url          = get_post_meta( $thepostid, $file_url_meta_key, true );
?>
<p class="form-field _pdc_editable_field">
	<label for="_pdc_file_url">PDF</label>
	<span class="form-flex-box">
		<input data-testid="pdc-file-upload" type="text" class="input_text pdc_input_pdf" id="_pdc-file_url" placeholder="<?php esc_attr_e( 'http://', 'pdc-connector' ); ?>" name="<?php echo $file_url_meta_key; ?>" value="<?php echo esc_attr( $file_url ); ?>" />
		<a href="#" class="button button-select-pdf-file" id="pdc-product-file-upload" data-choose="<?php esc_attr_e( 'Choose file', 'pdc-connector' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'pdc-connector' ); ?>"><?php echo esc_html__( 'Choose file', 'pdc-connector' ); ?></a>
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