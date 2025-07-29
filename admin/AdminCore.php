<?php

namespace PdcConnector\Admin;

use PdcConnector\Admin\PrintDotCom\APIClient;
use PdcConnector\Includes\Core;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/admin
 */

/**
 * Admin-specific functionality of the plugin applied to hooks.
 *
 * @package    PdcConnectorAdmin
 * @subpackage Pdc_Connector/admin
 * @author     Tijmen <tijmen@print.com>
 */
class AdminCore
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

	private APIClient $pdc_client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->pdc_client = new APIClient($plugin_name);
	}

	/**
	 * Retrieves the meta key for the given key.
	 * Plug-in meta keys should not be shown to the public so are always prefixed
	 * with an underscore. They are also namespaced by using the plug-in name.
	 *
	 * @since    1.0.1
	 * @param      string    $key    The meta key, ex. 'pdf_url'
	 */
	private function get_meta_key($key)
	{
		return Core::get_meta_key($key);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pdc_Connector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pdc_Connector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/pdc-connector-admin.css', array(), time(), 'all');
		wp_enqueue_style(
			'accessible-autocomplete',
			plugin_dir_url(__FILE__) . 'css/accessible-autocomplete.min.css',
			[],
			"3.0.1",
			"all",
		);
		// wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/pdc-connector-admin.css', array(), time(), 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pdc_Connector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pdc_Connector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Make sure we can use the media file uploader
		wp_enqueue_media();

		// Register admin JS scripts
		wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/pdc-connector-admin.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name . '-admin', 'pdcAdminApi', array(
			'root' => esc_url_raw(rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
			'plugin_name' => $this->plugin_name,
			'ajax_url' => admin_url('admin-ajax.php'),
			'pdc_url' => $this->pdc_client->get_api_base_url(),
		));
		wp_enqueue_script(
			'accessible-autocomplete',
			plugin_dir_url(__FILE__) . 'js/accessible-autocomplete.min.js',
			[],
			"3.0.1",
			array(),
		);
		wp_enqueue_script(
			$this->plugin_name . '-autocomplete-search',
			plugin_dir_url(__FILE__) . 'js/pdc-connector-autocomplete.js',
			['jquery', 'accessible-autocomplete'],
			time(),
			array(),
		);
		wp_localize_script($this->plugin_name . '-autocomplete-search', 'pdcAdminApi', array(
			'root' => esc_url_raw(rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
			'plugin_name' => $this->plugin_name,
			'pdc_url' => $this->pdc_client->get_api_base_url(),
		));
	}

	/**
	 * Register the Admin Menu pages for Print.com settings
	 *
	 * @since    1.0.0
	 */
	public function add_menu_pages()
	{
		add_menu_page('General Settings', 'Print.com', 'manage_options',  $this->plugin_name, array($this, 'page_general_settings'), "data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYWFnXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDY5IDY5IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2OSA2OSIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CiAgPHN0eWxlPgogICAgLnN0MXtmaWxsOiNmZmZ9CiAgPC9zdHlsZT4KICA8cGF0aCBpZD0iUGF0aF82MDQiIGQ9Ik01MC4zIDY1LjVjLTIzLjIgOS4zLTQxIC4yLTQ4LjUtMjcuMS01LjUtMjAgMi0yNS4xIDIyLjctMzQuNEM0OC43LTYuOSA2Mi44IDUuNyA2Ny43IDI4LjJjMy44IDE3LjQtLjYgMzAuNS0xNy40IDM3LjN6IiBzdHlsZT0iZmlsbDojZmYwMDQ4Ii8+CiAgPGcgaWQ9Ikdyb3VwXzgxMzQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE2LjM3MiAyNC43MjgpIj4KICAgIDxnIGlkPSJHcm91cF84MTMyIj4KICAgICAgPHBhdGggaWQ9IlBhdGhfNjA1IiBjbGFzcz0ic3QxIiBkPSJNNC4xIDcuNVYxLjRDNC4yLjIgMy43LTEgMi44LTEuOCAxLjctMi42LjQtMy0uOS0yLjloLTVWMTVjMCAuNS4zLjguOS44aDIuN1YxMWMuNi42IDEuNC45IDIuMy44IDEuMSAwIDIuMi0uNCAzLTEuMS43LS45IDEuMS0yIDEuMS0zLjJ6TS41IDYuN2MwIC42LS4xIDEuMi0uMyAxLjctLjIuNC0uNy42LTEuMS42LS41IDAtMS0uMi0xLjQtLjZWMGgxLjRDMCAwIC41LjUuNSAxLjV2NS4yeiIvPgogICAgICA8cGF0aCBpZD0iUGF0aF82MDYiIGNsYXNzPSJzdDEiIGQ9Ik0xMi44LTMuMmMtMS4yLS4xLTIuMy43LTIuNiAxLjh2LS43YzAtLjUtLjMtLjgtLjktLjhINi41djEzLjdjMCAuNS4zLjguOS44aDIuN1YzLjFjLjEtMS4zIDEtMi41IDIuMy0yLjcuMiAwIC40LS4yLjUtLjR2LTMuMWMwLS4xIDAtLjEtLjEtLjF6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwNyIgY2xhc3M9InN0MSIgZD0iTTIzLjUgMTEuNWgyLjdWLjVjLjItLjYuOC0xIDEuNC0uOS44IDAgMS4yLjUgMS4yIDEuNHY5LjdjMCAuNS4zLjcuOC43aDIuOFYuOGMuMS0xLjEtLjMtMi4xLTEtMi45LS41LS44LTEuNC0xLjItMi40LTEuMS0xLjEtLjEtMi4yLjUtMi43IDEuNXYtLjRjMC0uNS0uMy0uOC0uOS0uOGgtMy44djIuM2MwIC4zLjMuNi42LjZoLjR2MTAuOGMuMS40LjMuNy45Ljd6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwOCIgY2xhc3M9InN0MSIgZD0iTTIwLjIgMTEuNVY5LjJjMC0uMy0uMy0uNi0uNi0uNkgxOVYtMi4yYzAtLjUtLjMtLjgtLjktLjhoLTIuN3YxMy43YzAgLjUuMy43LjkuN2gzLjl6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwOSIgY2xhc3M9InN0MSIgZD0iTTQwLjIgOC43aC0uNGMtLjggMC0xLjMtLjQtMS4zLTEuM1YwaDIuMXYtMi4xYzAtLjUtLjMtLjctLjgtLjdoLTEuNHYtMS4xYzAtLjUtLjMtLjgtLjktLjhIMzVWNi45YzAgMS42LjMgMi44IDEgMy41LjcuNyAxLjcgMS4xIDMuMiAxLjFoMS45VjkuNGMwLS41LS4zLS43LS45LS43eiIvPgogICAgICA8cGF0aCBpZD0iUGF0aF82MTAiIGNsYXNzPSJzdDEiIGQ9Ik0xOC4xLTQuOWMtMS40LjYtMi41IDAtMy0xLjctLjMtMS4yLjEtMS41IDEuNC0yLjEgMS41LS43IDIuNC4xIDIuNyAxLjUuMyAxIDAgMS44LTEuMSAyLjN6Ii8+CiAgICA8L2c+CiAgICA8ZyBpZD0iR3JvdXBfODEzMyIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTguODI0IDM4LjQwNikiPgogICAgICA8cGF0aCBpZD0iUGF0aF82MTEiIGNsYXNzPSJzdDEiIGQ9Ik0tMS42LTIyYy0xLjctMS4xLTMuOS0xLjEtNS41IDAtLjcuNi0xIDEuNS0xIDIuNHY0LjVjLS4xLjkuMyAxLjggMSAyLjQgMS43IDEuMSAzLjkgMS4xIDUuNSAwIC43LS42IDEtMS41IDEtMi40di0uNmMwLS40LS4yLS42LS43LS42aC0xLjRjLS40IDAtLjcuMi0uNy42di42YzAgLjctLjMgMS4xLTEgMS4xcy0xLS40LTEtMS4xdi00LjZjMC0uNy4zLTEuMSAxLTEuMXMxIC40IDEgMS4xdi42YzAgLjQuMi42LjcuNmgxLjRjLjQgMCAuNy0uMi43LS42di0uNmMuMS0uOC0uMy0xLjctMS0yLjN6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYxMiIgY2xhc3M9InN0MSIgZD0iTTcuNS0yMmMtMS43LTEuMS0zLjktMS4xLTUuNSAwLS43LjYtMSAxLjUtMSAyLjR2NC41Yy0uMS45LjMgMS44IDEgMi40IDEuNyAxLjEgMy45IDEuMSA1LjUgMCAuNy0uNiAxLTEuNSAxLTIuNHYtNC41YzAtLjktLjQtMS44LTEtMi40em0tMS44IDYuOWMwIC43LS4zIDEuMS0xIDEuMXMtMS0uNC0xLTEuMXYtNC42YzAtLjcuMy0xLjEgMS0xLjFzMSAuNCAxIDEuMXY0LjZ6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYxMyIgY2xhc3M9InN0MSIgZD0iTS0xMC4zLTEyYy0xLjEuNS0yIDAtMi40LTEuMy0uMy0xIC4xLTEuMiAxLjEtMS43IDEuMi0uNSAxLjkuMSAyLjEgMS4yLjQuNyAwIDEuNS0uOCAxLjguMS0uMS4xLS4xIDAgMHoiLz4KICAgICAgPHBhdGggaWQ9IlBhdGhfNjE0IiBjbGFzcz0ic3QxIiBkPSJNMjIuNi0xNC4zaC0uNHYtNS42YzAtLjgtLjItMS42LS43LTIuMi0uNS0uNS0xLjItLjgtMi0uOC0xIDAtMS45LjUtMi40IDEuMy0uNC0uOC0xLjMtMS4zLTIuMy0xLjItLjgtLjEtMS42LjMtMiAxLjF2LS4zYzAtLjQtLjItLjYtLjctLjZIOS40djEuOGMwIC4yLjIuNC40LjRoLjN2Ny44YzAgLjQuMi41LjcuNWgydi04Yy4xLS40LjYtLjcgMS0uNy42IDAgLjkuNC45IDEuMXY3LjFjMCAuNC4yLjUuNi41aDIuMXYtOGMuMi0uNC42LS43IDEtLjcuNiAwIC45LjQuOSAxLjF2Ny4xYzAgLjQuMi41LjYuNWgyLjl2LTEuN2MuMy0uMy4xLS41LS4yLS41eiIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+");
	}

	public function register_sections()
	{
		add_settings_section(
			$this->plugin_name . '-credentials',
			'Credentials',
			array($this, 'section_credentials'),
			$this->plugin_name,
		);
		add_settings_section(
			$this->plugin_name . '-product',
			'Product',
			array($this, 'section_product'),
			$this->plugin_name,
		);
	}

	public function register_settings()
	{
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-api_key',
		);
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-env',
		);
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-product',
		);
	}

	public function add_product_data_tab($tabs)
	{
		$tabs['pdc_printtab'] = array(
			'label' 	=> 'Print.com',
			'priority' 	=> 60,
			'target'  =>  'pdc_product_data_tab',
			'class'    => array('show_if_simple', 'show_if_variable'),
		);

		return $tabs;
	}



	/**
	 * Saves the product settings
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function save_product_data_fields($post_id)
	{
		$this->save_text_field($post_id, $this->get_meta_key('product_sku'));
		$this->save_text_field($post_id, $this->get_meta_key('product_title'));
		$this->save_text_field($post_id, $this->get_meta_key('preset_id'));
		$this->save_text_field($post_id, $this->get_meta_key('preset_title'));
		$this->save_text_field($post_id, $this->get_meta_key('pdf_url'));
	}

	public function pdc_meta_box($post)
	{
		$order = wc_get_order($post->ID);
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-html-order-metabox.php');
	}

	public function pdc_order_meta_box()
	{
		add_meta_box(
			'pdc_order_meta_box',
			'Print.com',
			array($this, 'pdc_meta_box'),
			'shop_order',
			'normal',
			'core'
		);
	}

	private function save_text_field($post_id, $fieldname)
	{
		if (isset($_POST[$fieldname])) :
			update_post_meta($post_id, $fieldname, $_POST[$fieldname]);
		endif;
	}

	public function render_product_data_tab()
	{
		global $post, $thepostid, $product_object;
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-producttab.php');
	}

	/**
	 * Creates the settings page
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function page_general_settings()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-general.php');
	}

	/**
	 * Creates the credentials section
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function section_credentials()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-section-credentials.php');
	}

	/**
	 * Creates the product configuration section
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function section_product()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-section-product.php');
	}

	/**
	 * Will save the order item meta data
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function on_order_save(int $order_item_id)
	{
		$meta_pdf_url = $this->get_meta_key('pdf_url');
		update_post_meta($order_item_id, $meta_pdf_url, $_POST[$meta_pdf_url]);
	}


	/**
	 * Registers the PDC purchase item endpoint
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function register_pdc_purchase_endpoint()
	{
		register_rest_route('pdc/v1', '/orders', array(
			'methods' => 'POST',
			'callback' => array($this, 'pdc_purchase_order_item'),
			'permission_callback'   => function () {
				return current_user_can('manage_options');
			}
		));
		register_rest_route('pdc/v1', '/orders/(?P<id>\d+)/attach-pdf', array(
			'methods' => 'POST',
			'callback' => array($this, 'pdc_attach_pdf'),
			'permission_callback'   => function () {
				return current_user_can('manage_options');
			}
		));
		register_rest_route('pdc/v1', '/orders/webhook', array(
			'methods' => 'POST',
			'callback' => array($this, 'pdc_order_webhook'),
			'permission_callback' => '__return_true',
		));
	}

	public function pdc_list_products()
	{
		$search_term = $_POST["searchTerm"];
		$lc_search_term = strtolower($search_term);
		$products = $this->pdc_client->searchProducts();
		$filtered_products = array_filter($products, function ($item) use ($lc_search_term) {
			$lc_title = strtolower($item->title);
			return strpos($lc_title, $lc_search_term) !== false || strpos($item->sku, $lc_search_term) !== false;
		});

		usort($filtered_products, function ($a, $b) use ($lc_search_term) {
			$lc_a_title = strtolower($a->title);
			$lc_b_title = strtolower($a->title);

			// Check if the string starts with the search string
			$aStartsWith = strpos($lc_a_title, $lc_search_term) === 0;
			$bStartsWith = strpos($lc_b_title, $lc_search_term) === 0;

			// Give priority to strings that start with the search string
			if ($aStartsWith && !$bStartsWith) {
				return -1; // $a comes before $b
			} elseif (!$aStartsWith && $bStartsWith) {
				return 1; // $b comes before $a
			} else {
				// If both start or neither starts with the prefix, maintain default order
				return strcmp($lc_a_title, $lc_b_title);
			}
		});

		// Re-index the filtered and sorted array to get a flat array
		$sorted_products = array_values($filtered_products);

		wp_send_json_success(array(
			'products' => $sorted_products,
		));
	}

	public function pdc_order_webhook(\WP_REST_Request $request)
	{
		$body = json_decode($request->get_body());
		$event_type = $body->event_type;
		$payload = $body->payload;

		if ($event_type === 'ORDER_STATUS_CHANGED') {
			$order_id = $request->get_param('order_id');
			$order_item_id = $request->get_param('order_item_id');

			if ($payload->status === 'ACCEPTEDBYSUPPLIER') {
				$this->on_webhook_in_production($order_id, $order_item_id);
			}
		}

		if ($event_type === 'SHIPMENT_CREATED') {
			$this->on_webhook_shipped($payload->order_item_number, $payload->tracking_code);
		}

		return;
	}

	private function on_webhook_in_production(string $order_id, string $order_item_id)
	{
		$order_item = new \WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($this->get_meta_key('order_item_status'), "production");
		$order_item->save();

		$order = wc_get_order($order_id);
		$note = __("Item is being produced at Print.com.", 'pdc-connector');
		$order->add_order_note($note);
		$order->save();
	}

	/**
	 * Will attempt to retrieve a WC_Order_item by a Print.com Order Number
	 *
	 * @param [type] $pdc_order_item_number ex. 6000012345-1
	 * @return WC_Order_Item_Product
	 */
	private function get_order_item_by_order_item_number($pdc_order_item_number)
	{
		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT im.order_item_id 
				FROM {$wpdb->prefix}woocommerce_order_items AS i
				JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
				WHERE im.meta_key = %s AND im.meta_value = %s
				",
				$this->get_meta_key('order_item_number'),
				$pdc_order_item_number
			)
		);
		if (empty($results)) {
			return null;
		}
		$result = $results[0];
		$order_item = new \WC_Order_Item_Product($result->wp_order_item_id);
		return $order_item;
	}

	private function on_webhook_shipped(string $order_item_number, string $tracking_url)
	{
		$order_item = $this->get_order_item_by_order_item_number($order_item_number);
		$order_item->update_meta_data($this->get_meta_key('order_item_tnt_url'), $tracking_url);
		$order_item->update_meta_data($this->get_meta_key('order_item_status'), "shipped");
		$order_item->save();

		$order = wc_get_order($pdc_order->wp_order_id);
		$note = sprintf(
			// translators: placeholder is a URL to the track & trace page
			__('Item has been shipped by Print.com. Track & Trace code: <a href="%1$s">%2$s</a>.', 'pdc-connector'),
			$tracking_url,
			$tracking_url,
		);
		$order->add_order_note($note);
		$order->save();
	}

	public function pdc_attach_pdf(\WP_REST_Request $request)
	{
		$order_item_id = $request->get_param('orderItemId');
		$pdf_url = $request->get_param('pdfUrl');

		$meta_key_pdf_url = $this->get_meta_key('pdf_url');
		$order_item = new \WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($meta_key_pdf_url, $pdf_url);
		$order_item->save_meta_data();
		return $pdf_url;
	}

	/**
	 * Implementation of API method attached to GET /products/:sku/presets
	 * Will list the presets for a given product for each selection.
	 *
	 * @since      1.0.0
	 */
	public function pdc_list_presets()
	{
		$sku = $_POST["sku"];
		if (empty($sku)) {
			return new \WP_Error('no_sku', 'No SKU provided', array('sku' => $sku));
		}
		$presets = $this->pdc_client->getPresets($sku);
		wp_send_json_success(array(
			'presets' => $presets,
		));
	}

	public function pdc_purchase_order_item(\WP_REST_Request $request)
	{

		$order_item_id = $request->get_param('orderItemId');

		$result = $this->pdc_client->purchaseOrderItem($order_item_id);
		if (is_wp_error($result)) {
			return $result;
		}
		$pdc_order = $result->order;
		$order_item = new \WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($this->get_meta_key('purchase_date'), date("c"));
		$order_item->update_meta_data($this->get_meta_key('order_number'), $pdc_order->orderNumber);
		$order_item->update_meta_data($this->get_meta_key('grand_total'), $pdc_order->grandTotal);
		$order_item->update_meta_data($this->get_meta_key('order_status'), $pdc_order->status);

		$order_item->update_meta_data($this->get_meta_key('order_item_number'), $pdc_order->items[0]->orderItemNumber);
		$order_item->update_meta_data($this->get_meta_key('order_item__delivery_date'), $pdc_order->items[0]->shipments[0]->deliveryDate);
		$order_item->update_meta_data($this->get_meta_key('order_item__delivery_method'), $pdc_order->items[0]->shipments[0]->method);
		$order_item->update_meta_data($this->get_meta_key('order_item_status'), $pdc_order->items[0]->status);
		$order_item->update_meta_data($this->get_meta_key('order_item_grand_total'), $pdc_order->items[0]->grandTotal);
		$order_item->save();

		$order_id = wc_get_order_id_by_order_item_id($order_item_id);
		$order = wc_get_order($order_id);

		$note = sprintf(
			// translators: placeholder is the order number
			__("Item purchased at Print.com with order number: %s.", 'pdc-connector'),
			$pdc_order->orderNumber
		);
		$order->add_order_note($note);

		return $pdc_order;
	}

	public function render_variation_data_fields(int $index, array $variation_data, \WP_Post $variation)
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-variation_data.php');
	}

	public function save_variation_data_fields($variation_id, $i)
	{
		$fieldname_preset_id = $this->get_meta_key('preset_id');
		$fieldname_preset_title = $this->get_meta_key('preset_title');
		$fieldname_pdf_url = $this->get_meta_key('pdf_url');

		$this->save_variation_data_field($variation_id, $fieldname_preset_id, $i);
		$this->save_variation_data_field($variation_id, $fieldname_preset_title, $i);
		$this->save_variation_data_field($variation_id, $fieldname_pdf_url, $i);
	}
	private function save_variation_data_field($variation_id, $fieldname, $it)
	{
		if (isset($_POST[$fieldname]) && $_POST[$fieldname][$it]) :
			update_post_meta($variation_id, $fieldname, $_POST[$fieldname][$it]);
		endif;
	}
}
