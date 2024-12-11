<?php

use PdcConnector\Includes\Core;

$pdf_key = Core::get_meta_key('pdf_url');
$product_file_url = get_post_meta($product->get_id(), $pdf_key, true);
?>

<input type="hidden" name=" <?= $pdf_key; ?>" id="js-<?= $pdf_key; ?>" value="<?= $product_file_url; ?>" />