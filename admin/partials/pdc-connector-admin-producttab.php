<?php

/**
 * Admin product data tab.
 *
 * Renders the WooCommerce product data tab for connecting to Print.com.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

/**
 * Variables available in this file
 *
 * @global WP_Post $post   Global post object.
 * @var string $pdc_connector_sku
 * @var string $pdc_connector_preset_id
 * @var string $preset_input_name
 * @var array<PdcConnector\Admin\PrintDotCom\Product> $pdc_products
 * @var array<PdcConnector\Admin\PrintDotCom\Preset> $pdc_connector_presets_for_sku
 */
?>
<div id="pdc_product_data_tab" class="panel woocommerce_options_panel">
	<?php wp_nonce_field('pdc_connector_save_product', 'pdc_connector_nonce'); ?>
	<div class="options_group pdc_product_options" id="js-pdc-simple-options">
		<p class="form-field">
			<label for="pdc-products-label"><?php esc_html_e('Print.com SKU', 'pdc-connector'); ?></label>
			<select
				id="js-pdc-product-selector"
				data-testid="pdc-product-sku"
				name="<?php echo esc_attr($this->get_meta_key('product_sku')); ?>"
				value="<?php echo esc_attr((string) $pdc_connector_sku); ?>">
				<?php foreach ($pdc_products as $product) { ?>
					<option value="<?php echo esc_attr($product->sku); ?>" <?php selected($product->sku, $pdc_connector_sku); ?>><?php echo esc_attr($product->title); ?></option>
				<?php } ?>
			</select>
		</p>
		<p class="form-field">
			<label for="pdc-presets-label"><?php esc_html_e('Print.com Preset', 'pdc-connector'); ?></label>
			<span class="pdc-ac-preset-list">
				<select id="js-pdc-preset-list" class="pdc_preset_select" name="<?php echo esc_attr($preset_input_name); ?>" data-testid="pdc-preset-id" value="<?php echo esc_attr((string) $pdc_connector_preset_id); ?>">
					<?php include plugin_dir_path(__FILE__) . '/' . $this->plugin_name . '-admin-preset-select.php'; ?>
				</select>
			</span>
		</p>
		<?php
		/**
		 * Include the media upload input partial.
		 *
		 * @since 1.0.0
		 */
		require_once __DIR__ . '/html-input-mediaupload.php';
		?>
	</div>
</div>

<script type="text/javascript">
	(function($) {
		$(document).on('woocommerce_variations_loaded', showPresetForSku);
		$('#js-pdc-product-selector').on('change', showPresetForSku);

		async function showPresetForSku() {
			const sku = $('#js-pdc-product-selector').val();
			if (!sku) return;
			try {
				const presetOptionsHTML = await wp.ajax
					.post('pdc-list-presets', {
						sku,
					})
					.promise();
				document.getElementById('js-pdc-preset-list').innerHTML = presetOptionsHTML;

				const variationPresetInputs = document.querySelectorAll('.pdc_variation_preset_select');
				variationPresetInputs.forEach((selectInput) => {
					selectInput.innerHTML = presetOptionsHTML;
				});
			} catch (err) {
				console.error('err:', err);
			}
		}
	})(jQuery);
</script>