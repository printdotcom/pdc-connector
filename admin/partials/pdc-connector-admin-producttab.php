<?php

use Automattic\WooCommerce\Utilities\OrderUtil;

$sku = OrderUtil::get_post_or_object_meta($post, null, $this->get_meta_key('product_sku'), true);
$sku_title = OrderUtil::get_post_or_object_meta($post, null, $this->get_meta_key('product_title'), true);
$preset_id = OrderUtil::get_post_or_object_meta($post, null, $this->get_meta_key('preset_id'), true);
$preset_title = OrderUtil::get_post_or_object_meta($post, null, $this->get_meta_key('preset_title'), true);
?>
<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
    <div class="options_group pdc_product_options" id="js-pdc-simple-options">
        <p class="form-field">
            <label id="pdc-products-label"><?php _e('Print.com SKU', $this->plugin_name); ?></label>
            <span id="js-pdc-ac-product-list" class="pdc-ac-product-list"></span>
            <input type="hidden" value="<?= $sku  ?>" id="js-pdc-product-sku" name="<?= $this->get_meta_key('product_sku'); ?>" />
            <input type="hidden" value="<?= $sku_title  ?>" id="js-pdc-product-title" name="<?= $this->get_meta_key('product_title'); ?>" />
            <span class="spinner" id="js-pdc-product-search-spinner"></span>
        </p>
        <p class="form-field">
            <label id="pdc-products-label"><?php _e('Print.com Preset', $this->plugin_name); ?></label>
            <span class="js-pdc-preset-search pdc-ac-preset-list"></span>
            <input type="hidden" value="<?= $preset_id; ?>" class="js-pdc-preset-id" name="<?= $this->get_meta_key('preset_id'); ?>" />
            <input type="hidden" value="<?= $preset_title; ?>" class="js-pdc-preset-title" name="<?= $this->get_meta_key('preset_title'); ?>" />
            <span id="js-pdc-preset-search-spinner" class="spinner"></span>
        </p>

        <?php include __DIR__ . '/html-input-mediaupload.php'; ?>
    </div>
</div>