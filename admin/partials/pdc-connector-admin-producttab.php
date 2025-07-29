<?php
/** @var WP_Post $post */

$sku = get_post_meta($post->ID, $this->get_meta_key('product_sku'), true);
$sku_title = get_post_meta($post->ID, $this->get_meta_key('product_title'), true);
$preset_id = get_post_meta($post->ID, $this->get_meta_key('preset_id'), true);
$preset_title = get_post_meta($post->ID, $this->get_meta_key('preset_title'), true);
?>
<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
    <div class="options_group pdc_product_options" id="js-pdc-simple-options">
        <p class="form-field">
            <label id="pdc-products-label"><?php _e('Print.com SKU', 'pdc-connector'); ?></label>
            <span id="js-pdc-ac-product-list" class="pdc-ac-product-list"></span>
            <input data-testid="pdc-product-sku" type="hidden" value="<?php echo $sku  ?>" id="js-pdc-product-sku" name="<?php echo $this->get_meta_key('product_sku'); ?>" />
            <input data-testid="pdc-product-title" type="hidden" value="<?php echo $sku_title  ?>" id="js-pdc-product-title" name="<?php echo $this->get_meta_key('product_title'); ?>" />
            <span class="spinner" id="js-pdc-product-search-spinner"></span>
        </p>
        <p class="form-field">
            <label id="pdc-presets-label"><?php _e('Print.com Preset', 'pdc-connector'); ?></label>
            <span class="js-pdc-preset-search pdc-ac-preset-list"></span>
            <input data-testid="pdc-preset-id" type="hidden" value="<?php echo $preset_id; ?>" class="js-pdc-preset-id" name="<?php echo $this->get_meta_key('preset_id'); ?>" />
            <input data-testid="pdc-preset-title" type="hidden" value="<?php echo $preset_title; ?>" class="js-pdc-preset-title" name="<?php echo $this->get_meta_key('preset_title'); ?>" />
            <span id="js-pdc-preset-search-spinner" class="spinner"></span>
        </p>

        <?php include __DIR__ . '/html-input-mediaupload.php'; ?>
    </div>
</div>