<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/public
 * @author     Tijmen <tijmen@print.com>
 */
class Pdc_Connector_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
	 * Enqueue all required assets for the editor
	 *
	 * @since    1.0.0
	 */
	public function enqueue_app_assets()
	{
	}


	/**
	 * Adds additional values to the cart item
	 * Filter cart item data for add to cart requests.
	 * Hooks into woocommerce_add_cart_item_data
	 *
	 * @since    1.0.0
	 */
	public function capture_cart_item_data($cart_item_data, $product_id)
	{
		$cart_item_data[$this->plugin_name . '_pdf_url'] = $this->capture_cart_item_pdf_url();
		return $cart_item_data;
	}

	private function capture_cart_item_pdf_url()
	{
		$pdc_pdf_url = $_REQUEST[$this->plugin_name . '_pdf_url'];
		if (isset($pdc_pdf_url) && !empty($pdc_pdf_url)) {
			// When a static PDC file is configured, we just use that.
			return $pdc_pdf_url;
		}

		// This was the old method of getting pitch print data
		$pitchprint_data = $_REQUEST['_w2p_set_option'];
		if (isset($pitchprint_data) && !empty($pitchprint_data)) {
			// When a file is configured via pitch print, we use that.
			$decoded_data = json_decode(urldecode($pitchprint_data));
			return "https://pdf.pitchprint.com/" . $decoded_data->projectId;
		}

		return "";
	}

	/**
	 * Saves the PDC values on the order item
	 * Action hook to adjust item before save. 
	 * Hooks into woocommerce_checkout_create_order_line_item
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function save_pdc_values_order_meta(WC_Order_Item_Product $order_item, $cart_item_key, $values, WC_Order $order)
	{
		$product_id = $values['product_id'];

		// Check if the cart item contains a pdf url
		$pdc_pdf_url = $values[$this->plugin_name . '_pdf_url'];

		// if not, check if product has a preconfigured pdf url
		if (empty($pdc_pdf_url)) {
			$pdc_pdf_url = get_post_meta($product_id, $this->plugin_name . '_file_url', true);
		}

		// if not, check if the cart item contains pitch print data
		if (empty($pdc_pdf_url)) {
			$cart_item = WC()->cart->get_cart_item($cart_item_key);
			$pitchprint_data = $cart_item['_pda_w2p_set_option'];
			$decoded_data = json_decode(urldecode($pitchprint_data));
			$pdc_pdf_url = "https://pdf.print.app/" . $decoded_data->projectId;
		}
		$order_item->add_meta_data('_' . $this->plugin_name . '_pdf_url', $pdc_pdf_url);

		$pdc_preset_id = get_post_meta($product_id, $this->plugin_name . '_preset_id', true);
		if ($pdc_preset_id) {
			$order_item->add_meta_data('_' . $this->plugin_name . '_preset_id', $pdc_preset_id);
		}
	}

	/**
	 * Renders the editor on the product-single page
	 */
	public function render_canvas()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-public-editor.php');
	}

	/**
	 * Renders the pdf input on the product-single page
	 */
	public function set_pdf_input()
	{
		global $product;
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-html-pdf-url-input.php');
	}
}
