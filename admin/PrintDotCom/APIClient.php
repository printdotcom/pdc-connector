<?php

namespace PdcConnector\Admin\PrintDotCom;

/**
 * Client to connect to the Print.com API
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/admin
 */

class APIClient
{

    // Base URL of the Print.com API
    private $pdc_api_base_url;

    // Unique reference for this client,
    // will be used to store unique cache entries
    private $plugin_name;

    // Access token for the Print.com API
    private $pdc_access_token;

    /**
     * Initializes the API client
     *
     * @param string $client_id Unique reference for this client
     */
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
        $this->pdc_api_base_url = get_option($plugin_name . '-env_baseurl');;
    }

    /**
     * Retrieves the access token for the Print.com API
     * Will check if there is already a access token stored
     * If not, it will attempt to retrieve one from the API
     */
    private function get_token()
    {
        if ($this->pdc_access_token) {
            return $this->pdc_access_token;
        }

        $this->pdc_access_token = $this->retrieve_access_token();
        return $this->pdc_access_token;
    }

    /**
     * DO NOT use this method, use get_token() instead.
     * 
     * Retrieves an access token for the Print.com API
     * Will first attempt to retrieve the token from the transient cache
     * If the token is not found, it will attempt to retrieve it from the API
     * 
     */
    private function retrieve_access_token()
    {
        $transient_token = get_transient($this->plugin_name . '-token');
        if ($transient_token) {
            return $transient_token;
        }

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
        return $parsedToken;
    }

    /**
     * Performs an authenticated request to the Print.com API
     * A more convenient wrapper around performHttpRequest
     * 
     * @param string $method The HTTP method to use
     * @param string $path The path to request
     * @param array $data Optional data to send in the request
     * @param array $headers Optional headers to send with the request
     * @return string|WP_Error The unparsed response from the API
     */
    private function perfromAuthenticatedRequest($method, $path, $data = NULL, $headers = [])
    {
        $token = $this->get_token();
        $url = $this->pdc_api_base_url . $path;
        return $this->performHttpRequest($method, $url, $data, $token, $headers);
    }

    /**
     * Performs an HTTP request to the Print.com API
     *
     * @param string $method The HTTP method to use
     * @param string $url The URL to request
     * @param array $data The data to send in the request
     * @param string $token The access token to use
     * @param array $headers Additional headers to send with the request
     * @return string|WP_Error The unparsed response from the API
     */
    private function performHttpRequest($method, $url, $data = NULL, $token = NULL, $headers = [])
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

        if (!empty($headers)) {
            $params[CURLOPT_HTTPHEADER] = array_merge($params[CURLOPT_HTTPHEADER], $headers);
        }

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
            return new \WP_Error($httpcode, $response);
        }

        if ($err) {
            return new \WP_Error($httpcode, $err);
        }
        return $response;
    }


    /**
     * Retrieves a list of Print.com Presets
     * 
     * @param string $sku The SKU of the product to retrieve the Presets for
     * @return Pdc_Preset[] A list of Print.com Presets
     */
    public function getPresets($sku)
    {
        $result = get_transient($this->plugin_name . '-customerpresets');
        if (empty($result)) {
            $result = $this->perfromAuthenticatedRequest('GET', "customerpresets");
            set_transient($this->plugin_name . '-customerpresets', $result, 30); // 30 seconds
        }
        $decoded_result = json_decode($result);

        $presets = array_map(function ($preset) {
            return new Preset($preset->sku, $preset->title->en, $preset->id);
        }, $decoded_result->items);

        $filteredBySku = array_filter($presets, function ($preset) use ($sku) {
            return $preset->sku === $sku;
        });
        return array_values($filteredBySku);
    }

    public function getPresetByID($id)
    {
        $result = $this->perfromAuthenticatedRequest('GET', "customerpresets/" . urlencode($id));
        if (is_wp_error($result)) {
            return $result;
        }
        if (empty($result)) {
            return new \WP_Error('no_preset', 'No preset found', array('preset_id' => $id));
        }

        $preset = json_decode($result);
        return $preset;
    }

    public function listProducts()
    {
        $cached = get_transient($this->plugin_name . '-products');
        if ($cached) {
            return json_decode($cached);
        }

        $result = $this->perfromAuthenticatedRequest('GET', 'products');
        if (is_wp_error($result)) {
            return $result;
        }

        if (empty($result)) {
            return new \WP_Error('no result', 'No products found');
        }

        $decoded_result = json_decode($result);
        $mapped_products = array_map(function ($item) {
            return new Product($item->sku, $item->titlePlural);
        }, $decoded_result);

        $products = array_values($mapped_products);

        set_transient($this->plugin_name . '-products', $products, 60 * 60 * 24); // 1 day

        return $products;
    }

    public function purchaseOrderItem(string $order_item_id)
    {
        $order_item = new \WC_Order_Item_Product($order_item_id);
        $order_id = wc_get_order_id_by_order_item_id($order_item_id);
        $order = wc_get_order($order_id);
        $shipping_address = $order->get_address('shipping');
        if (empty($shipping_address)) {
            return new \WP_Error('no_shipping_address', 'No shipping address found', array('order' => $order));
        }

        $product = wc_get_product($order_item["product_id"]);
        $variation_id = $order_item->get_variation_id();
        $pdc_preset_id = '';
        if ($variation_id) {
            // if variation, get preset from variation
            $variation_preset_id = get_post_meta($variation_id, $this->plugin_name . '_preset_id', true);
            $pdc_preset_id = $variation_preset_id;
        }

        if (empty($pdc_preset_id)) {
            $pdc_preset_id = $product->get_meta($this->plugin_name . '_preset_id');
        }

        $pdc_pdf_url = wc_get_order_item_meta($order_item_id, "_{$this->plugin_name}_pdf_url", true);

        $result = $this->perfromAuthenticatedRequest('GET', 'customerpresets/' . urlencode($pdc_preset_id), NULL);

        if (is_wp_error($result)) {
            return $result;
        }
        if (empty($result)) {
            return new \WP_Error('no_preset', 'No preset found', array('preset_id' => $pdc_preset_id));
        }

        $preset = json_decode($result);

        // remove unwanted options from preset
        unset($preset->configuration->variants);
        unset($preset->configuration->_accessories);
        unset($preset->configuration->deliveryPromise);

        $item_options = $preset->configuration;
        $copies = $order_item->get_quantity();
        $item_options->copies = $copies;

        // remove unwanted options
        unset($item_options->_accessories);
        unset($item_options->variants);
        unset($item_options->deliveryPromise);

        $restapi_url = esc_url_raw(rest_url());
        $order_request = array(
            "customerReference" => $order->get_order_number() . '-' . $order_item_id,
            "webhookUrl" => $restapi_url . "pdc/v1/orders/webhook?order_item_id=" . $order_item_id . "&order_id=" . $order_id,
            "items" => [[
                "sku" => $preset->sku,
                "fileUrl" => $pdc_pdf_url,
                "options" => $item_options,
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
                    "copies" => $copies,
                ]]
            ]]

        );

        $result = $this->perfromAuthenticatedRequest('POST',  'orders', $order_request, [
            'pdc-request-source' => 'pdc-woocommerce',
        ]);

        if (is_wp_error($result)) {
            return new \WP_Error('place_order_failed', 'failed placing the order', array('result' => $result));
        }

        if (empty($result)) {
            return new \WP_Error('order_failed', 'unable to place order', array('order' => $order_request));
        }

        return json_decode($result);
    }
}
