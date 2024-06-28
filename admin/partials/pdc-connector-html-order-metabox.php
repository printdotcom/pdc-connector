<div class="wp-list-table pdc-table widefat fixed striped posts">
    <div class="table-head">
        <div class="table-head-col">
            <strong>Information</strong>
        </div>
        <div class="table-head-col">
            <strong>Actions</strong>
        </div>
    </div>
    <div class="table-body">
        <?php
        foreach ($post->get_items() as $order_item_product) {
            $order_item_id = $order_item_product->get_id();
            $pdc_order_item_number = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_order_item_number', true);
            $pdc_order_item_grand_total = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_order_item_grand_total', true);
            $pdc_purchase_date = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_purchase_date', true);
            $pdc_image_url = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_image_url', true);
            $pdc_pdf_url = wc_get_order_item_meta($order_item_id, "_{$this->plugin_name}_pdf_url", true);
            $pdc_order_item_status = wc_get_order_item_meta($order_item_id, "{$this->plugin_name}_order_item_status", true);
            $pdc_preset_name = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_preset_name', true);
            $pdc_preset_id = wc_get_order_item_meta($order_item_id, "_{$this->plugin_name}_preset_id", true);
            $pdc_tnt_url = wc_get_order_item_meta($order_item_id, "{$this->plugin_name}_order_item_tnt_url", true);

            $has_file = $pdc_pdf_url ? true : false;
            $has_preset = $pdc_preset_id ? true : false;

            $filename = basename($pdc_pdf_url);

        ?>
            <div class="table-row" id="pdc_order_item_<?= $order_item_id; ?>">
                <div class="table-row-contents" id="pdc_order_item_<?= $order_item_id; ?>_inner">
                    <div class="table-cell">
                        <?php if ($pdc_order_item_number) { ?>
                            <span><strong>Order item number</strong> #<?= $pdc_order_item_number; ?></span><br>
                            <span><strong>Purchase Date</strong> <?= $pdc_purchase_date; ?></span><br>
                            <span><strong>Item Status</strong> <?= $pdc_order_item_status; ?></span><br>
                            <span><strong>Price</strong> <?= wc_price($pdc_order_item_grand_total); ?></span><br>
                            <span><strong>Track & Trace</strong> <a href="<?= $pdc_tnt_url; ?>"><?=$pdc_tnt_url; ?></a></span><br>
                        <?php } ?>
                        <?php if ($pdc_pdf_url) { ?>
                            <span><strong>File</strong> <a target="_blank" href="<?= $pdc_pdf_url; ?>"><?= $filename; ?></span></a><br>
                        <?php } ?>
                        <div class="notifications">
                            <? if (!$has_file && !$pdc_order_item_number) { ?><p>Missing file. Upload one to purchase.</p> <? } ?>
                            <? if (!$has_preset) { ?><p> Missing preset. You need a connected preset on the product to purchase. </p><? } ?>
                        </div>
                    </div>
                    <div class="table-cell">
                        <div class="actions">
                            <input type="text" class="hidden" id="_pdc_pdf_url" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="_pdc_pdf_url" value="<?= esc_attr($pdc_pdf_url); ?>" />
                            <? if (!$pdc_order_item_number && empty($pdc_pdf_url)) { ?><a href="#" id="pdc-file-upload" data-order-item-id="<?= $order_item_id; ?>" class="button button-secondary">Upload PDF</a><? } ?>
                            <? if (!$pdc_order_item_number && $pdc_pdf_url) { ?><a href="#" id="pdc-file-upload" data-order-item-id="<?= $order_item_id; ?>" class="button button-secondary">Replace PDF</a><? } ?>
                            <? if (!$pdc_order_item_number && $has_preset) { ?> <a id="pdc-order" data-order-item-id="<?= $order_item_id; ?>" href="#" class="button button-primary"> Purchase</a> <? } ?>
                            <span class="spinner" id="js-pdc-action-spinner"></span>
                            <div class="notice-warning"><span id="js-pdc-request-response"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>