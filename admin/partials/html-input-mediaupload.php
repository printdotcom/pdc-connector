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

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$pdc_connector_file_url_meta_key = $this->get_meta_key( 'pdf_url' );
$pdc_connector_file_url          = get_post_meta( $thepostid, $pdc_connector_file_url_meta_key, true );
?>
<p class="form-field _pdc_editable_field">
	<label for="_pdc_file_url">PDF</label>
	<span class="form-flex-box">
		<input data-testid="pdc-file-upload" type="text" class="input_text pdc_input_pdf" id="_pdc-file_url" placeholder="<?php esc_attr_e( 'http://', 'pdc-connector' ); ?>" name="<?php echo esc_attr( (string) $pdc_connector_file_url_meta_key ); ?>" value="<?php echo esc_attr( (string) $pdc_connector_file_url ); ?>" />
		<a href="#" class="button button-select-pdf-file" id="pdc-product-file-upload" data-choose="<?php esc_attr_e( 'Choose file', 'pdc-connector' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'pdc-connector' ); ?>"><?php esc_html_e( 'Choose file', 'pdc-connector' ); ?></a>
	</span>
</p>