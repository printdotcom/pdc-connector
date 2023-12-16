<?php

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
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/admin
 * @author     Tijmen <tijmen@print.com>
 */
class Pdc_Connector_Admin
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
	 * Access Token for Print.com API
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $pdc_access_token;

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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/pdc-connector-admin.css', array(), $this->version, 'all');
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
			'pdc_url' => get_option($this->plugin_name . '-env_baseurl'),
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
		add_submenu_page($this->plugin_name, 'Print.com Purchases', 'Purchases', 'manage_options', $this->plugin_name . '-purchases', array($this, 'page_purchases'));
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
			$this->plugin_name . '-environment',
			'Environment',
			array($this, 'section_environment'),
			$this->plugin_name,
		);
	}

	public function register_settings()
	{

		// register_setting( $option_group, $option_name, $sanitize_callback );

		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-user',
		);
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-pw',
		);
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-env_baseurl',
		);
	} // register_settings()

	public function register_fields()
	{
		add_settings_field($this->plugin_name . '-user', 'E-mail', array($this, 'pdc_inputfield'), $this->plugin_name, $this->plugin_name . '-credentials', [
			'type' => "email",
			"name" => $this->plugin_name . '-user',
		]);
		add_settings_field($this->plugin_name . '-pw', 'Password', array($this, 'pdc_inputfield'), $this->plugin_name, $this->plugin_name . '-credentials', [
			'type' => "password",
			"name" => $this->plugin_name . '-pw',
		]);
	}

	public function pdc_password($args)
	{
		printf(
			'<input type="password" id="%s" name="%s" value="%s" />',
			$args['name'],
			$args['name'],
			get_option($args['name'], "")
		);
	}

	public function pdc_inputfield($args)
	{
		$type = $args['type'];
		$name = $args['name'];
		$value = get_option($name);
		echo "<input type=\"$type\" name=\"$name\" id=\"$name\" value=\"$value\"  />";
	}

	public function add_product_data_tab($tabs)
	{
		$tabs['pdc_printtab'] = array(
			'label' 	=> 'Print',
			'priority' 	=> 60,
			'target'  =>  'pdc_product_data_tab'
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
		$this->save_text_field($post_id, $this->plugin_name . '_sku');
		$this->save_text_field($post_id, $this->plugin_name . '_preset_id');
		$this->save_text_field($post_id, $this->plugin_name . '_template_json');
		$this->save_text_field($post_id, $this->plugin_name . '_file_url');
		$this->save_checkbox_field($post_id, $this->plugin_name . '_editable');
	}

	public function pdc_meta_box($post)
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-html-order-metabox.php');
	}

	public function pdc_order_meta_box()
	{
		add_meta_box(
			'pdc_purchase',
			'Print.com',
			array($this, 'pdc_meta_box'),
			'woocommerce_page_wc-orders',
			'normal',
			'core'
		);
	}

	private function save_checkbox_field($post_id, $fieldname)
	{
		$value = isset($_POST[$fieldname]) ? 'yes' : 'no';
		update_post_meta($post_id, $fieldname, $value);
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
	 * Creates the purchases
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function page_purchases()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-purchases.php');
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
	 * Creates the environment section
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function section_environment()
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-section-environment.php');
	}

	/**
	 * Will save the order item meta data
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function on_order_save(int $order_item_id)
	{
		update_post_meta($order_item_id, $this->plugin_name . '_pdf_url', $_POST[$this->plugin_name . "_pdf_url"]);
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
	}

	public function pdc_attach_pdf(WP_REST_Request $request)
	{
		$order_item_id = $request->get_param('orderItemId');
		$pdf_url = $request->get_param('pdfUrl');
		$order_item = new WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($this->plugin_name . '_pdf_url', $pdf_url);
		$order_item->save_meta_data();
		return $pdf_url;
	}

	public function pdc_purchase_order_item(WP_REST_Request $request)
	{

		$order_item_id = $request->get_param('orderItemId');
		$baseUrl = get_option($this->plugin_name . '-env_baseurl');

		$order_item = new WC_Order_Item_Product($order_item_id);
		$order_id = wc_get_order_id_by_order_item_id($order_item_id);
		$order = wc_get_order($order_id);
		$shipping_address = $order->get_address('shipping');
		if (empty($shipping_address)) {
			return new WP_Error('no_shipping_address', 'No shipping address found', array('order' => $order));
		}

		$pdc_preset_id = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_preset_id', true);
		$pdc_pdf_url = wc_get_order_item_meta($order_item_id, $this->plugin_name . '_pdf_url', true);
		$token = $this->getToken();
		$result = $this->performHttpRequest('GET', $baseUrl . 'customerpresets/' . urlencode($pdc_preset_id), NULL, $token);

		if (is_wp_error($result)) {
			return $result;
		}
		if (empty($result)) {
			return new WP_Error('no_preset', 'No preset found', array('preset_id' => $pdc_preset_id));
		}

		$address = [
			"city" => "Wierden",
			"country" => "NL",
			"email" => "tijmen@print.com",
			"firstName" => "Tijmen",
			"houseNumber" => "9",
			"lastName" => "Bruggeman",
			"postcode" => "7641AG",
			"fullstreet" => "Weusteweg 9",
			"telephone" => "0613762125"
		];
		$preset = json_decode($result);
		$item_options = $preset->configuration;
		$item_options->copies = $order_item->get_quantity();
		$order_request = array(
			"billingAddress" => $address,
			"customerReference" => $order->get_order_number() . '-' . $order_item_id,
			"items" => [[
				"sku" => $preset->sku,
				"fileUrl" => $pdc_pdf_url,
				"options" => $item_options,
				"senderAddress" => $address,
				"shipments" => [[
					"address" => [
						"city" => $shipping_address['city'],
						"country" => $shipping_address['country'],
						"firstName" => $shipping_address['first_name'],
						"lastName" => $shipping_address['last_name'],
						"companyName" => $shipping_address['company'],
						"postcode" => $shipping_address['postcode'],
						"fullstreet" => $shipping_address['address_1'],
						"telephone" => $shipping_address['phone'],
					],
					"copies" => $order_item->get_quantity()
				]]
			]]

		);

		$result = $this->performHttpRequest('POST', $baseUrl . 'orders', $order_request, $token);

		if (is_wp_error($result)) {
			return $result;
		}
		if (empty($result)) {
			return new WP_Error('order_failed', 'unable to place order', array('order' => $order_request));
		}

		$pdc_order_result = json_decode($result);
		$pdc_order = $pdc_order_result->order;
		$order_item->update_meta_data($this->plugin_name . "_purchase_date", date("c"));
		$order_item->update_meta_data($this->plugin_name . "_order_number", $pdc_order->orderNumber);
		$order_item->update_meta_data($this->plugin_name . "_grand_total", $pdc_order->grandTotal);
		$order_item->update_meta_data($this->plugin_name . "_order_status", $pdc_order->status);

		$order_item->update_meta_data($this->plugin_name . '_order_item_number', $pdc_order->items[0]->orderItemNumber);
		$order_item->update_meta_data($this->plugin_name . "_order_item__delivery_date", $pdc_order->items[0]->shipments[0]->deliveryDate);
		$order_item->update_meta_data($this->plugin_name . "_order_item__delivery_method", $pdc_order->items[0]->shipments[0]->method);
		$order_item->update_meta_data($this->plugin_name . "_order_item_status", $pdc_order->items[0]->status);
		$order_item->update_meta_data($this->plugin_name . "_order_item_grand_total", $pdc_order->items[0]->grandTotal);
		$order_item->save();
		$order = wc_get_order($order_id);

		$note = __("Item purchased at Print.com with order number: $pdc_order->orderNumber.");
		$order->add_order_note( $note );

		$this->insert_pdc_order($pdc_order, $order, $order_item);

		return $pdc_order;
	}

	private function insert_pdc_order($pdc_order, WC_Order $order , WC_Order_Item_Product $order_item) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'pdc_orders';
	
		$wpdb->insert(
			$table_name,
			array(
				'pdc_ordernumber' => $pdc_order->orderNumber,
				'wp_order_id' => $order->get_ID(),
				'pdc_price'  => $pdc_order->grandTotal,
				'pdc_status' => $pdc_order->status,
			)
		);
	}

	private function getToken()
	{
		if ($this->pdc_access_token) return $this->pdc_access_token;

		$username = get_option($this->plugin_name . '-user');
		$password = get_option($this->plugin_name . '-pw');
		$baseUrl = get_option($this->plugin_name . '-env_baseurl');
		$token = $this->performHttpRequest('POST', $baseUrl . 'login', [
			'credentials' => [
				'username' => $username,
				'password' => $password
			]
		], '');
		$parsedToken = str_replace('"', "", $token);
		$this->pdc_access_token = $parsedToken;
		return $parsedToken;
	}

	private function performHttpRequest($method, $url, $data = NULL, $token)
	{
		$curl = curl_init($url);
		$params = [
			CURLOPT_TIMEOUT => 30,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				"accept: application/json",
				"content-type: application/json",
			]
		];

		if ($method === 'POST' && !empty($data)) {
			$params[CURLOPT_POSTFIELDS] = json_encode($data);
		}
		if ($token) {
			$params[CURLOPT_HTTPHEADER][] = "authorization: Bearer " . $token;
		}
		curl_setopt_array($curl, $params);

		// Execute
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpcode != 200) {
			return new WP_Error($httpcode, $response);
		}

		// Check for cURL errors
		if ($err) {
			echo "cURL Error: " . $err . "\n";
			return new WP_Error($httpcode, $err);
		}
		return $response;
	}

	public function hide_order_item_meta($arr)
	{
		$arr[] = 'pdc-connector_pdf_url';
		$arr[] = 'pdc-connector_preset_id';
		$arr[] = 'pdc-connector_order_item__delivery_date';
		$arr[] = 'pdc-connector_order_item__delivery_method';
		$arr[] = 'pdc-connector_order_item_status';
		$arr[] = 'pdc-connector_order_item_grand_total';
		$arr[] = 'pdc-connector_purchase_date';
		$arr[] = 'pdc-connector_order_number';
		$arr[] = 'pdc-connector_grand_total';
		$arr[] = 'pdc-connector_order_status';
		$arr[] = 'pdc-connector_order_item_number';
		return $arr;
	}
}
