<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
    <div class="options_group">
        <?php woocommerce_wp_text_input(array(
            'id' => $this->plugin_name . '_sku',
            'label' => __('Connected SKU'),
            'required'      => true
        )); ?>
        <?php woocommerce_wp_text_input(array(
            'id' => $this->plugin_name . '_preset_id',
            'label' => __('Connected Preset'),
            'required'      => true
        )); ?>

        <?php include __DIR__ . '/html-input-mediaupload.php'; ?>
    </div>
</div>