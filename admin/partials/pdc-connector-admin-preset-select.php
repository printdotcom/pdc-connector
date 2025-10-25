<?php
/**
 * Admin preset list.
 *
 * Renders the WooCommerce product data tab for connecting to Print.com.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

/**
 * Variables available in this file
 *
 * @var array<PdcConnector\Admin\PrintDotCom\Preset> $pdc_connector_presets_for_sku
 * @var string $pdc_connector_preset_id
 */
?>

<option value><?php esc_html_e( 'Select a preset', 'pdc-connector' ); ?></option>
<?php foreach ( $pdc_connector_presets_for_sku as $pdc_connector_preset ) { ?>
	<option value="<?php echo esc_attr( $pdc_connector_preset->id ); ?>" <?php selected( $pdc_connector_preset->id, $pdc_connector_preset_id ); ?>><?php echo esc_html( $pdc_connector_preset->title ); ?></option>
<?php } ?>