<?php
$product_file_url = get_post_meta($product->get_id(), $this->plugin_name . '_file_url', true);
?>

<input type="hidden" name=" <?= $this->plugin_name; ?>_pdf_url" id="<?= $this->plugin_name; ?>_pdf_url" value="<?= $product_file_url; ?>" />