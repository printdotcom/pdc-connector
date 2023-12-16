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
		wp_enqueue_style('pdc-app-style', plugin_dir_url(__FILE__) . 'app/index.css', array(), $this->version, 'all');
		wp_enqueue_script('pdc-app-script', plugin_dir_url(__FILE__) . 'app/index.js', array('jquery'), $this->version, true);
		wp_enqueue_script('pdc-app-trigger', plugin_dir_url(__FILE__) . 'js/pdc-connector-trigger.js', array('pdc-app-script'), $this->version, true);
	}


	/**
	 * Adds additional values to the cart item
	 *
	 * @since    1.0.0
	 */
	public function capture_cart_item_data($cart_item_data, $product_id)
	{
		
		if (isset($_REQUEST[$this->plugin_name . '_pdf_url'])) {
			$cart_item_data[$this->plugin_name . '_pdf_url'] = sanitize_text_field($_REQUEST[$this->plugin_name . '_pdf_url']);
		}
		return $cart_item_data;
	}

	public function display_pdc_values_checkout($item_data, $cart_item)
	{
		if (isset($cart_item[$this->plugin_name . '_pdf_url'])) {
			$item_data[] = array(
				'key' => __('File', $this->plugin_name),
				'value' => basename($cart_item[$this->plugin_name . '_pdf_url'])
			);
		}
		return $item_data;
	}

	public function save_pdc_values_order_meta(WC_Order_Item_Product $item, $cart_item_key, $values, WC_Order $order)
	{
		$product_id = $values['product_id'];
		$pdc_preset_id = get_post_meta($product_id, $this->plugin_name . '_preset_id', true);
		$pdf_in_request = $values[$this->plugin_name . '_pdf_url'];
		if (isset($pdf_in_request)) {
			$item->add_meta_data($this->plugin_name . '_pdf_url', $values[$this->plugin_name . "_pdf_url"]);
		} else {
			$pdc_pdf_url = get_post_meta($product_id, $this->plugin_name . '_file_url', true);
			$item->add_meta_data($this->plugin_name . '_pdf_url', $pdc_pdf_url);
		}
		
		if ($pdc_preset_id) {
			$item->add_meta_data($this->plugin_name . '_preset_id', $pdc_preset_id);
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
