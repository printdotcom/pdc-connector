<?php

namespace PdcConnector\Admin\PrintDotCom;

use PdcConnector\Includes\Core;

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

    // API Key for the Print.com API
    private $pdc_api_key;

    /**
     * Initializes the API client
     *
     * @param string $client_id Unique reference for this client
     */
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;

        $env = get_option($plugin_name . '-env');
        
        // Allow environment variable override for testing
        if (getenv('PDC_API_BASE_URL')) {
            $this->pdc_api_base_url = getenv('PDC_API_BASE_URL');
        } else {
            $this->pdc_api_base_url = $env === 'prod' ? "https://api.print.com" : "https://api.stg.print.com";
        }

        // Allow environment variable override for testing  
        if (getenv('PDC_API_KEY')) {
            $this->pdc_api_key = getenv('PDC_API_KEY');
        } else {
            $api_key = get_option($plugin_name . '-api_key');
            $this->pdc_api_key = $api_key;
        }
    }

    /**
     * Retrieves the API base url based on the current environment.
     */
    public function get_api_base_url()
    {
        return $this->pdc_api_base_url;
    }

    /**
     * Returns the API Key used for authenticated requests
     */
    private function get_token()
    {
        return $this->pdc_api_key;
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
    private function performAuthenticatedRequest($method, $path, $data = NULL, $headers = [])
    {
        $url = $this->pdc_api_base_url . $path;
        $token = $this->get_token();
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
            $params[CURLOPT_HTTPHEADER][] = "authorization: PrintApiKey " . $token;
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
            $result = $this->performAuthenticatedRequest('GET', "/customerpresets");
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


    public function searchProducts()
    {
        $result = NULL;
        $cached = get_transient($this->plugin_name . '-products');
        if ($cached) {
            $result = json_decode($cached);
        } else {
            $response = $this->performAuthenticatedRequest('GET', '/products', NULL);
            if (is_wp_error($response)) {
                return $response;
            }
            if (empty($response)) {
                return new \WP_Error('no result', 'No products found');
            }
            set_transient($this->plugin_name . '-products', $response, 60 * 60 * 24); // 1 day
            $result = json_decode($response);
        }


        $products = array_map(function ($result_item) {
            return new Product($result_item->sku, $result_item->titlePlural);
        }, $result);

        return $products;
    }

    /**
     * Purchases an order item through the Print.com API.
     *
     * This function creates a print order by retrieving preset configuration,
     * combining it with WooCommerce order data, and submitting it to Print.com.
     * It handles preset retrieval, file URLs, shipping addresses, and quantity
     * management based on the provided arguments.
     *
     * @since 1.0.0
     *
     * @param int   $order_item_id The WooCommerce order item ID to purchase.
     * @param array $args {
     *     Optional. Arguments for customizing the purchase behavior.
     *
     *     @type bool $use_preset_copies Whether to use preset-defined copy count.
     *                                   If false, uses order item quantity. Default true.
     * }
     *
     * @return object|WP_Error Returns the Print.com order response object on success,
     *                         or WP_Error on failure with error details.
     */
    public function purchaseOrderItem($order_item_id, $args)
    {
        $order_item = new \WC_Order_Item_Product($order_item_id);
        $order_id = wc_get_order_id_by_order_item_id($order_item_id);
        $order = wc_get_order($order_id);
        $shipping_address = $order->get_address('shipping');
        if (empty($shipping_address)) {
            return new \WP_Error('no_shipping_address', 'No shipping address found', array('order' => $order));
        }

        $product = wc_get_product($order_item["product_id"]);
        $pdc_preset_id = $product->get_meta(Core::get_meta_key('preset_id'));
        $pdc_pdf_url = wc_get_order_item_meta($order_item_id, Core::get_meta_key('pdf_url'), true);

        $result = $this->performAuthenticatedRequest('GET', '/customerpresets/' . urlencode($pdc_preset_id), NULL);

        if (is_wp_error($result)) {
            return $result;
        }
        if (empty($result)) {
            return new \WP_Error('no_preset', 'No preset found', array('preset_id' => $pdc_preset_id));
        }

        $preset = json_decode($result);

        $item_options = $preset->configuration;
        if (isset($args['use_preset_copies']) && !$args['use_preset_copies']) {
            // when preset copies 
            $copies = $order_item->get_quantity();
            $item_options->copies = $copies;
        }

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

        $order_body = apply_filters($this->plugin_name . '_before_place_order', $order_request);

        $result = $this->performAuthenticatedRequest('POST',  '/orders', $order_body, [
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
