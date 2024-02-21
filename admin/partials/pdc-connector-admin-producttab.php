<?php 

use Automattic\WooCommerce\Utilities\OrderUtil;
$sku = OrderUtil::get_post_or_object_meta( $post, $data, $this->plugin_name . '_sku', true );
$sku_title = OrderUtil::get_post_or_object_meta( $post, $data, $this->plugin_name . '_sku_title', true );
$preset_id = OrderUtil::get_post_or_object_meta( $post, $data, $this->plugin_name . '_preset_id', true );
$preset_name = OrderUtil::get_post_or_object_meta( $post, $data, $this->plugin_name . '_preset_name', true );
?>
<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
    <div class="options_group">
        <p class="form-field">
            <label><?php _e('Print.com SKU', $this->plugin_name); ?></label>
            <input type="text" value="<?= $sku_title  ?>" id="js-pdc-product-search" class="short" name="pdc-connector_sku_title" placeholder="Start typing.." />
            <input type="hidden" value="<?= $sku  ?>" id="js-pdc-product-sku" name="pdc-connector_sku" />
            <span id="js-pdc-product-search-spinner" class="spinner"></span>
        </p>
        <p class="form-field">
            <label><?php _e('Print.com Preset', $this->plugin_name); ?></label>
            <input type="text" value="<?= $preset_name; ?>" id="js-pdc-preset-search" class="short" <?= empty($sku) ? "disabled" : "" ?> name="pdc-connector_preset_name" placeholder="Start typing.." />
            <input type="hidden" value="<?= $preset_id; ?>" id="js-pdc-preset-id" name="pdc-connector_preset_id" />
            <span id="js-pdc-preset-search-spinner" class="spinner"></span>
        </p>

        <?php include __DIR__ . '/html-input-mediaupload.php'; ?>
    </div>
</div>