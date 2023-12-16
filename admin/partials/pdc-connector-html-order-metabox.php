<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th>Information</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($post->get_items() as $order_item_product) {
            $order_item_id = $order_item_product->get_id();
            $pdc_order_item_number = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_order_item_number', true);
            $pdc_order_item_grand_total = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_order_item_grand_total', true);
            $pdc_purchase_date = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_purchase_date', true);
            $pdc_image_url = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_image_url', true);
            $pdc_pdf_url = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_pdf_url', true);
            $pdc_order_item_status = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_order_item_status', true);
            $pdc_preset_name = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_preset_name', true);
            $pdc_preset_id = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_preset_id', true);

            $has_file = $pdc_pdf_url ? true : false;
            $has_preset = $pdc_preset_id ? true : false;

            $filename = basename($pdc_pdf_url);

        ?>
            <tr>
                <td>
                    <?php if ($pdc_order_item_number) { ?>
                        <span><strong>Order item number</strong> #<?= $pdc_order_item_number; ?></span><br>
                        <span><strong>Purchase Date</strong> <?= $pdc_purchase_date; ?></span><br>
                        <span><strong>Item Status</strong> <?= $pdc_order_item_status; ?></span><br>
                        <span><strong>Price</strong> <?= wc_price($pdc_order_item_grand_total); ?></span><br><br>
                    <?php } ?>
                    <?php if ($pdc_pdf_url) { ?>
                        <a target="_blank" href="<?= $pdc_pdf_url; ?>"><?= $filename; ?></a>
                    <?php } ?>
                    <div class="notifications">
                        <? if (!$has_file && !$pdc_order_item_number) { ?><p>Missing file. Upload one to purchase.</p> <? } ?>
                        <? if (!$has_preset) { ?><p> Missing preset. You need a connected preset on the product to purchase. </p><? } ?>
                    </div>
                </td>
                <td>
                    <div class="actions">
                        <input type="text" class="hidden" id="_pdc_pdf_url" placeholder="<?php esc_attr_e('http://', 'woocommerce'); ?>" name="_pdc_pdf_url" value="<?= esc_attr($pdc_pdf_url); ?>" />
                        <? if (!$pdc_order_item_number) { ?><a href="#" id="pdc-file-upload" data-order-item-id="<?= $order_item_id; ?>" class="button button-secondary">Upload File</a><? } ?>
                        <? if (!$pdc_order_item_number) { ?><a href="#" class="button button-secondary">Get Price</a> <? } ?>
                        <? if (!$pdc_order_item_number && $has_preset) { ?> <a id="pdc-order" data-order-item-id="<?= $order_item_id; ?>" href="#" class="button button-primary"> Purchase</a> <? } ?>
                        <span class="spinner" id="js-pdc-action-spinner"></span>
                        <div class="notice-warning"><span id="js-pdc-request-response"></span></div>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>