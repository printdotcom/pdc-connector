<?php
/**
 * Admin product data tab
 *
 * Renders the WooCommerce product data tab for connecting to Print.com.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

/** @var WP_Post $post */

$pdc_sku          = get_post_meta( $post->ID, $this->get_meta_key( 'product_sku' ), true );
$pdc_sku_title    = get_post_meta( $post->ID, $this->get_meta_key( 'product_title' ), true );
$pdc_preset_id    = get_post_meta( $post->ID, $this->get_meta_key( 'preset_id' ), true );
$pdc_preset_title = get_post_meta( $post->ID, $this->get_meta_key( 'preset_title' ), true );
?>
<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
	<div class="options_group pdc_product_options" id="js-pdc-simple-options">
		<p class="form-field">
			<label for="pdc-products-label"><?php _e( 'Print.com SKU', 'pdc-connector' ); ?></label>
			<span id="js-pdc-ac-product-list" class="pdc-ac-product-list"></span>
			<input data-testid="pdc-product-sku" type="hidden" value="<?php echo $pdc_sku; ?>" id="js-pdc-product-sku" name="<?php echo $this->get_meta_key( 'product_sku' ); ?>" />
			<input data-testid="pdc-product-title" type="hidden" value="<?php echo $pdc_sku_title; ?>" id="js-pdc-product-title" name="<?php echo $this->get_meta_key( 'product_title' ); ?>" />
			<span class="spinner" id="js-pdc-product-search-spinner"></span>
		</p>
		<p class="form-field">
			<label for="pdc-presets-label"><?php _e( 'Print.com Preset', 'pdc-connector' ); ?></label>
			<span class="js-pdc-preset-search pdc-ac-preset-list"></span>
			<input data-testid="pdc-preset-id" type="hidden" value="<?php echo $pdc_preset_id; ?>" class="js-pdc-preset-id" name="<?php echo $this->get_meta_key( 'preset_id' ); ?>" />
			<input data-testid="pdc-preset-title" type="hidden" value="<?php echo $pdc_preset_title; ?>" class="js-pdc-preset-title" name="<?php echo $this->get_meta_key( 'preset_title' ); ?>" />
			<span id="js-pdc-preset-search-spinner" class="spinner"></span>
		</p>

		<?php require __DIR__ . '/html-input-mediaupload.php'; ?>
	</div>
</div>