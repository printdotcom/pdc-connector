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
        <?php woocommerce_wp_textarea_input(array(
            'id' => $this->plugin_name . '_template_json',
            'label' => __('The template for the product', 'pdc_connector'),
            'rows' => 10,
            'cols' => 40,
            'required'      => true
        )); ?>
        <?php woocommerce_wp_checkbox(array(
            'id'         => $this->plugin_name . '_editable',
            'label'     => __('Show editor', 'pdc_connector'),
            'disabled' => true,
        )); ?>

        <?php include __DIR__ . '/html-input-mediaupload.php'; ?>
    </div>
</div>