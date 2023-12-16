<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/public/partials
 */


global $product;

$template_json = $product->get_meta('_pdc_template_json');
$editable = $product->get_meta('_pdc_editable') === 'yes';
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if ($editable) { ?>
    <div id="pdc-js-editor" class="pdc-editor" data-pdc-template="<?= base64_encode($template_json); ?>"></div>
<?php } ?>