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
			'pdc_url' => get_option($this->plugin_name . '-env_baseurl'),
		));
		wp_enqueue_script(
			$this->plugin_name . '-autocomplete-search',
			plugin_dir_url(__FILE__) . 'js/pdc-connector-autocomplete.js',
			['jquery', 'jquery-ui-autocomplete'],
			null,
			true
		);
		wp_localize_script($this->plugin_name . '-autocomplete-search', 'pdcAdminApi', array(
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
		$this->save_text_field($post_id, $this->plugin_name . '_sku');
		$this->save_text_field($post_id, $this->plugin_name . '_sku_title');
		$this->save_text_field($post_id, $this->plugin_name . '_preset_id');
		$this->save_text_field($post_id, $this->plugin_name . '_preset_name');
		$this->save_text_field($post_id, $this->plugin_name . '_file_url');
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
		register_rest_route('pdc/v1', '/orders/webhook', array(
			'methods' => 'POST',
			'callback' => array($this, 'pdc_order_webhook'),
		));
		register_rest_route('pdc/v1', '/products', array(
			'methods' => 'GET',
			'callback' => array($this, 'pdc_list_products'),
		));
		register_rest_route('pdc/v1', '/products/(?P<sku>[a-zA-Z0-9_-]+)/presets', array(
			'methods' => 'GET',
			'callback' => array($this, 'pdc_list_presets'),
		));
	}

	public function pdc_order_webhook(WP_REST_Request $request)
	{
		$body = json_decode($request->get_body());
		$event_type = $body->event_type;

		if ($event_type === 'ORDER_STATUS_CHANGED') {
			$order_id = $request->get_param('order_id');
			$order_item_id = $request->get_param('order_item_id');
			$payload = $body->payload;

			if ($payload->status === 'ACCEPTEDBYSUPPLIER') {
				$this->on_webhook_in_production($order_id, $order_item_id);
			}
		}

		if ($event_type === 'SHIPMENT_CREATED') {
			$this->on_webhook_shipped($order_id, $order_item_id, $payload->tracking_code);
		}

		return;
	}

	private function on_webhook_in_production(string $order_id, string $order_item_id)
	{
		$order_item = new WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($this->plugin_name . "_order_item_status", "production");
		$order_item->save();

		$order = wc_get_order($order_id);
		$note = __("Item is being produced at Print.com.");
		$order->add_order_note($note);
		$order->save();
	}

	private function on_webhook_shipped(string $order_id, string $order_item_id, string $tracking_url)
	{
		$order = wc_get_order($order_id);
		$order_item = new WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data($this->plugin_name . "_order_item_tnt_url", $tracking_url);
		$note = __("Item has been shipped by Print.com. Track & Trace code: <a href=\"$tracking_url.\">$tracking_url</a>.");
		$order->add_order_note($note);
		$order_item->update_meta_data($this->plugin_name . "_order_item_status", "shipped");
		$order_item->save();
		$order->save();
	}

	private function retrieve_pdc_order(string $order_item_number)
	{
		$order_number = substr($order_item_number, 0, strpos($order_item_number, '-'));
		$baseUrl = get_option($this->plugin_name . '-env_baseurl');
		$token = $this->getToken();
		$result = $this->performHttpRequest('GET', $baseUrl . 'orders/' . urlencode($order_number), NULL, $token);

		if (is_wp_error($result)) {
			return $result;
		}
		if (empty($result)) {
			return new WP_Error('no_order', 'No order found', array('order_number' => $order_number));
		}

		$decoded = json_decode($result);

		return $decoded->order;
	}

	public function pdc_attach_pdf(WP_REST_Request $request)
	{
		$order_item_id = $request->get_param('orderItemId');
		$pdf_url = $request->get_param('pdfUrl');
		$order_item = new WC_Order_Item_Product($order_item_id);
		$order_item->update_meta_data("_{$this->plugin_name}_pdf_url", $pdf_url);
		$order_item->save_meta_data();
		return $pdf_url;
	}

	public function pdc_list_products(WP_REST_Request $request)
	{
		$searchterm = $request->get_param('term');
		if (empty($searchterm)) {
			return new WP_Error('no_searchterm', 'No search term provided', array('term' => $searchterm));
		}

		$products = get_transient($this->plugin_name . '-products');
		if (!$products) {
			$baseUrl = get_option($this->plugin_name . '-env_baseurl');
			$token = $this->getToken();
			$result = $this->performHttpRequest('GET', $baseUrl . 'products', NULL, $token);
			$decoded_result = json_decode($result);
			$sku_list = array_map(function ($product) {
				return array(
					'sku' => $product->sku,
					'title' => $product->titlePlural,
				);
			}, $decoded_result);
			$encoded = json_encode($sku_list);
			set_transient($this->plugin_name . '-products', $encoded, 60 * 60 * 24); // 1 day
			$products = $encoded;
		}

		$decoded = json_decode($products);
		usort($decoded, function ($a, $b) use ($searchterm) {
			$searchterm_lower = strtolower($searchterm);
			$titleA = strtolower($a->title);
			$titleB = strtolower($b->title);

			// Check for prefix match and give it the highest priority
			$startsWithA = strpos($titleA, $searchterm_lower) === 0 ? 1 : 0;
			$startsWithB = strpos($titleB, $searchterm_lower) === 0 ? 1 : 0;
			if ($startsWithA !== $startsWithB) {
				return $startsWithB <=> $startsWithA;
			}
			$similarityA = similar_text($searchterm_lower, $titleA);
			$similarityB = similar_text($searchterm_lower, $titleB);
			// Sort in descending order of similarity
			return $similarityB <=> $similarityA;
		});
		$filtered = array_slice($decoded, 0, 5);

		return $filtered;
	}

	/**
	 * Implementation of API method attached to GET /products/:sku/presets
	 * Will list the presets for a given product for each selection.
	 *
	 * @since      1.0.0
	 * @param      WP_Rest_Request    $request       Request object
	 */
	public function pdc_list_presets(WP_REST_Request $request)
	{
		$sku = $request->get_param('sku');
		return $this->getCustomerPresetsBySKU($sku);
	}

	private function getCustomerPresetsBySKU(string $sku)
	{
		$presets = get_transient($this->plugin_name . '-customerpresets');
		if (!$presets) {
			$baseUrl = get_option($this->plugin_name . '-env_baseurl');
			$token = $this->getToken();
			$result = $this->performHttpRequest('GET', $baseUrl . "customerpresets", NULL, $token);
			$decoded_result = json_decode($result);
			$presets = array_map(function ($preset) {
				return array(
					'sku' => $preset->sku,
					'preset_id' => $preset->id,
					'title' => $preset->title->en,
				);
			}, $decoded_result->items);
			$encoded = json_encode($presets);
			set_transient($this->plugin_name . '-customerpresets', $encoded, 60 * 10); // 1 minutes
		} else {
			$presets = json_decode($presets);
		}
		$filteredBySku = array_filter($presets, function ($preset) use ($sku) {
			return $preset->sku === $sku;
		});
		return $filteredBySku;
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

		$pdc_preset_id = wc_get_order_item_meta($order_item_id, "_{$this->plugin_name}_preset_id", true);
		$pdc_pdf_url = wc_get_order_item_meta($order_item_id, "_{$this->plugin_name}_pdf_url", true);
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

		// remove unwanted options from preset
		unset($preset->configuration->variants);
		unset($preset->configuration->_accessories);
		unset($preset->configuration->deliveryPromise);

		$item_options = $preset->configuration;
		$item_options->copies = $order_item->get_quantity();

		// remove unwanted options
		unset($item_options->_accessories);
		unset($item_options->variants);
		unset($item_options->deliveryPromise);

		$restapi_url = esc_url_raw(rest_url());
		$order_request = array(
			"billingAddress" => $address,
			"customerReference" => $order->get_order_number() . '-' . $order_item_id,
			"webhookUrl" => $restapi_url . "pdc/v1/orders/webhook?order_item_id=" . $order_item_id . "&order_id=" . $order_id,
			"items" => [[
				"sku" => $preset->sku,
				"fileUrl" => $pdc_pdf_url,
				"options" => $item_options,
				"senderAddress" => $address,
				"approveDesign" => true,
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
		$order->add_order_note($note);

		$this->insert_pdc_order($pdc_order, $order, $order_item);

		return $pdc_order;
	}

	private function insert_pdc_order($pdc_order, WC_Order $order, WC_Order_Item_Product $order_item)
	{
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

		$transient_token = get_transient($this->plugin_name . '-token');
		if ($transient_token) return $transient_token;

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
		set_transient($this->plugin_name . '-token', $parsedToken, 60 * 60 * 24); // 1 day
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

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpcode != 200) {
			return new WP_Error($httpcode, $response);
		}

		if ($err) {
			return new WP_Error($httpcode, $err);
		}
		return $response;
	}

	public function hide_order_item_meta($arr)
	{
		$arr[] = '_pdc-connector_pdf_url';
		$arr[] = '_pdc-connector_preset_id';
		$arr[] = 'pdc-connector_order_item__delivery_date';
		$arr[] = 'pdc-connector_order_item__delivery_method';
		$arr[] = 'pdc-connector_order_item_status';
		$arr[] = 'pdc-connector_order_item_grand_total';
		$arr[] = 'pdc-connector_purchase_date';
		$arr[] = 'pdc-connector_order_number';
		$arr[] = 'pdc-connector_grand_total';
		$arr[] = 'pdc-connector_order_status';
		$arr[] = 'pdc-connector_order_item_number';
		$arr[] = 'pdc-connector_order_item_tnt_url';
		return $arr;
	}


	public function render_variation_data_fields(int $index, array $variation_data, WP_Post $variation)
	{
		include(plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-variation_data.php');
	}

	public function save_variation_data_fields($variation_id, $i)
	{
		$fieldname_sku = $this->plugin_name . '_sku';
		$fieldname_sku_title = $this->plugin_name . '_sku_title';
		$fieldname_preset_id = $this->plugin_name . '_preset_id';
		$fieldname_preset_name = $this->plugin_name . '_preset_name';
		$fieldname_file_url = $this->plugin_name . '_file_url';

		$this->save_variation_data_field($variation_id, $fieldname_sku);
		$this->save_variation_data_field($variation_id, $fieldname_sku_title);
		$this->save_variation_data_field($variation_id, $fieldname_preset_id);
		$this->save_variation_data_field($variation_id, $fieldname_preset_name);
		$this->save_variation_data_field($variation_id, $fieldname_file_url);
	}
	private function save_variation_data_field($variation_id, $fieldname)
	{
		if (isset($_POST[$fieldname])) :
			update_post_meta($variation_id, $fieldname, $_POST[$fieldname]);
		endif;
	}
}
