<?php

namespace PdcConnector\Front;

use PdcConnector\Includes\Core;

/**
 * The user-facing functionality of the plugin.
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/front
 */

/**
 * The user-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the user-facing stylesheet and JavaScript.
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/front
 * @author     Tijmen <tijmen@print.com>
 */
class FrontCore
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
	 * Adds additional values to the cart item
	 * Filter cart item data for add to cart requests.
	 * Hooks into woocommerce_add_cart_item_data
	 *
	 * @since    1.0.0
	 */
	public function capture_cart_item_data($cart_item_data, $product_id)
	{
		$cart_item_data[Core::get_meta_key('pdf_url')] = $this->capture_cart_item_pdf_url();
		return $cart_item_data;
	}

	private function capture_cart_item_pdf_url()
	{
		$pdc_pdf_url = $_REQUEST[Core::get_meta_key('pdf_url')];
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
	 * Hooks into woocommerce_checkout_create_order_line_item
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function save_pdc_values_order_meta(\WC_Order_Item_Product $order_item, $cart_item_key, $values, \WC_Order $order)
	{
		$product_id = $values['product_id'];
		$variation_id = $order_item->get_variation_id();

		$pdc_pdf_url = isset($values[Core::get_meta_key('pdf_url')]) ? $values[Core::get_meta_key('pdf_url')] : null;
		$pdc_preset_id = isset($values[Core::get_meta_key('preset_id')]) ? $values[Core::get_meta_key('preset_id')] : null;

		if (empty($pdc_pdf_url)) {
			// there is no preconfigured pdf on the cart item
			if ($variation_id) {
				$variation_pdf_url = get_post_meta($variation_id, Core::get_meta_key('pdf_url'), true);
				if (!empty($variation_pdf_url)) {
					$pdc_pdf_url = $variation_pdf_url;
				}
			}

			// if variant did not set the pdc_pdf_url, get it from product
			if (empty($pdc_pdf_url)) {
				$pdc_pdf_url = get_post_meta($product_id, Core::get_meta_key('pdf_url'), true);
			}
		}

		if (empty($pdc_preset_id)) {
			// there is no preconfigured preset on the cart item
			if ($variation_id) {
				$variation_preset_id = get_post_meta($variation_id, Core::get_meta_key('preset_id'), true);
				if (!empty($variation_preset_id)) {
					$pdc_preset_id = $variation_preset_id;
				}

				if (empty($pdc_preset_id)) {
					// variation did not have a preset id so get it from product
					$pdc_preset_id = get_post_meta($product_id, Core::get_meta_key('preset_id'), true);
				}
			}
		}

		// check if we have pitch print 
		$cart_item = WC()->cart->get_cart_item($cart_item_key);
		$pitchprint_data = $cart_item['_pda_w2p_set_option'];
		if (!empty($pitchprint_data)) {
			$decoded_data = json_decode(urldecode($pitchprint_data));
			$pdc_pdf_url = "https://pdf.print.app/" . $decoded_data->projectId;
		}

		$order_item->add_meta_data(Core::get_meta_key('pdf_url'), $pdc_pdf_url);
		$order_item->add_meta_data(Core::get_meta_key('preset_id'), $pdc_preset_id);
	}
}
