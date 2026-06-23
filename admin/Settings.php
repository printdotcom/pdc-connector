<?php

/**
 * Settings
 *
 * Everything required to configure and set up Print.com
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/admin
 * @since 1.0.0
 */

namespace PdcPod\Admin;

use PdcPod\Admin\PrintDotCom\APIClient;
use PdcPod\Includes\Logger;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Pod
 * @subpackage Pdc_Pod/admin
 */

/**
 * Admin-specific functionality of the plugin applied to hooks.
 *
 * @package    PdcPodAdmin
 * @subpackage Pdc_Pod/admin
 * @author     Tijmen <tijmen@print.com>
 */
class Settings
{
	/**
	 * Print.com API client instance.
	 *
	 * @since 1.0.0
	 * @var APIClient
	 */
	private APIClient $pdc_client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param APIClient $pdc_api_client The api client.
	 * @since    1.0.0
	 */
	public function __construct( $pdc_api_client ) {
		$this->pdc_client = $pdc_api_client;
	}

	/**
	 * Register the Admin Menu pages for Print.com settings
	 *
	 * @since    1.0.0
	 */
	public function add_menu_pages()
	{
		add_menu_page('Print.com', 'Print.com', 'manage_options', PDC_POD_NAME, array($this, 'page_general_settings'), 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYWFnXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDY5IDY5IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2OSA2OSIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CiAgPHN0eWxlPgogICAgLnN0MXtmaWxsOiNmZmZ9CiAgPC9zdHlsZT4KICA8cGF0aCBpZD0iUGF0aF82MDQiIGQ9Ik01MC4zIDY1LjVjLTIzLjIgOS4zLTQxIC4yLTQ4LjUtMjcuMS01LjUtMjAgMi0yNS4xIDIyLjctMzQuNEM0OC43LTYuOSA2Mi44IDUuNyA2Ny43IDI4LjJjMy44IDE3LjQtLjYgMzAuNS0xNy40IDM3LjN6IiBzdHlsZT0iZmlsbDojZmYwMDQ4Ii8+CiAgPGcgaWQ9Ikdyb3VwXzgxMzQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE2LjM3MiAyNC43MjgpIj4KICAgIDxnIGlkPSJHcm91cF84MTMyIj4KICAgICAgPHBhdGggaWQ9IlBhdGhfNjA1IiBjbGFzcz0ic3QxIiBkPSJNNC4xIDcuNVYxLjRDNC4yLjIgMy43LTEgMi44LTEuOCAxLjctMi42LjQtMy0uOS0yLjloLTVWMTVjMCAuNS4zLjguOS44aDIuN1YxMWMuNi42IDEuNC45IDIuMy44IDEuMSAwIDIuMi0uNCAzLTEuMS43LS45IDEuMS0yIDEuMS0zLjJ6TS41IDYuN2MwIC42LS4xIDEuMi0uMyAxLjctLjIuNC0uNy42LTEuMS42LS41IDAtMS0uMi0xLjQtLjZWMGgxLjRDMCAwIC41LjUuNSAxLjV2NS4yeiIvPgogICAgICA8cGF0aCBpZD0iUGF0aF82MDYiIGNsYXNzPSJzdDEiIGQ9Ik0xMi44LTMuMmMtMS4yLS4xLTIuMy43LTIuNiAxLjh2LS43YzAtLjUtLjMtLjgtLjktLjhINi41djEzLjdjMCAuNS4zLjguOS44aDIuN1YzLjFjLjEtMS4zIDEtMi41IDIuMy0yLjcuMiAwIC40LS4yLjUtLjR2LTMuMWMwLS4xIDAtLjEtLjEtLjF6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwNyIgY2xhc3M9InN0MSIgZD0iTTIzLjUgMTEuNWgyLjdWLjVjLjItLjYuOC0xIDEuNC0uOS44IDAgMS4yLjUgMS4yIDEuNHY5LjdjMCAuNS4zLjcuOC43aDIuOFYuOGMuMS0xLjEtLjMtMi4xLTEtMi45LS41LS44LTEuNC0xLjItMi40LTEuMS0xLjEtLjEtMi4yLjUtMi43IDEuNXYtLjRjMC0uNS0uMy0uOC0uOS0uOGgtMy44djIuM2MwIC4zLjMuNi42LjZoLjR2MTAuOGMuMS40LjMuNy45Ljd6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwOCIgY2xhc3M9InN0MSIgZD0iTTIwLjIgMTEuNVY5LjJjMC0uMy0uMy0uNi0uNi0uNkgxOVYtMi4yYzAtLjUtLjMtLjgtLjktLjhoLTIuN3YxMy43YzAgLjUuMy43LjkuN2gzLjl6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYwOSIgY2xhc3M9InN0MSIgZD0iTTQwLjIgOC43aC0uNGMtLjggMC0xLjMtLjQtMS4zLTEuM1YwaDIuMXYtMi4xYzAtLjUtLjMtLjctLjgtLjdoLTEuNHYtMS4xYzAtLjUtLjMtLjgtLjktLjhIMzVWNi45YzAgMS42LjMgMi44IDEgMy41LjcuNyAxLjcgMS4xIDMuMiAxLjFoMS45VjkuNGMwLS41LS4zLS43LS45LS43eiIvPgogICAgICA8cGF0aCBpZD0iUGF0aF82MTAiIGNsYXNzPSJzdDEiIGQ9Ik0xOC4xLTQuOWMtMS40LjYtMi41IDAtMy0xLjctLjMtMS4yLjEtMS41IDEuNC0yLjEgMS41LS43IDIuNC4xIDIuNyAxLjUuMyAxIDAgMS44LTEuMSAyLjN6Ii8+CiAgICA8L2c+CiAgICA8ZyBpZD0iR3JvdXBfODEzMyIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTguODI0IDM4LjQwNikiPgogICAgICA8cGF0aCBpZD0iUGF0aF82MTEiIGNsYXNzPSJzdDEiIGQ9Ik0tMS42LTIyYy0xLjctMS4xLTMuOS0xLjEtNS41IDAtLjcuNi0xIDEuNS0xIDIuNHY0LjVjLS4xLjkuMyAxLjggMSAyLjQgMS43IDEuMSAzLjkgMS4xIDUuNSAwIC43LS42IDEtMS41IDEtMi40di0uNmMwLS40LS4yLS42LS43LS42aC0xLjRjLS40IDAtLjcuMi0uNy42di42YzAgLjctLjMgMS4xLTEgMS4xcy0xLS40LTEtMS4xdi00LjZjMC0uNy4zLTEuMSAxLTEuMXMxIC40IDEgMS4xdi42YzAgLjQuMi42LjcuNmgxLjRjLjQgMCAuNy0uMi43LS42di0uNmMuMS0uOC0uMy0xLjctMS0yLjN6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYxMiIgY2xhc3M9InN0MSIgZD0iTTcuNS0yMmMtMS43LTEuMS0zLjktMS4xLTUuNSAwLS43LjYtMSAxLjUtMSAyLjR2NC41Yy0uMS45LjMgMS44IDEgMi40IDEuNyAxLjEgMy45IDEuMSA1LjUgMCAuNy0uNiAxLTEuNSAxLTIuNHYtNC41YzAtLjktLjQtMS44LTEtMi40em0tMS44IDYuOWMwIC43LS4zIDEuMS0xIDEuMXMtMS0uNC0xLTEuMXYtNC42YzAtLjcuMy0xLjEgMS0xLjFzMSAuNCAxIDEuMXY0LjZ6Ii8+CiAgICAgIDxwYXRoIGlkPSJQYXRoXzYxMyIgY2xhc3M9InN0MSIgZD0iTS0xMC4zLTEyYy0xLjEuNS0yIDAtMi40LTEuMy0uMy0xIC4xLTEuMiAxLjEtMS43IDEuMi0uNSAxLjkuMSAyLjEgMS4yLjQuNyAwIDEuNS0uOCAxLjguMS0uMS4xLS4xIDAgMHoiLz4KICAgICAgPHBhdGggaWQ9IlBhdGhfNjE0IiBjbGFzcz0ic3QxIiBkPSJNMjIuNi0xNC4zaC0uNHYtNS42YzAtLjgtLjItMS42LS43LTIuMi0uNS0uNS0xLjItLjgtMi0uOC0xIDAtMS45LjUtMi40IDEuMy0uNC0uOC0xLjMtMS4zLTIuMy0xLjItLjgtLjEtMS42LjMtMiAxLjF2LS4zYzAtLjQtLjItLjYtLjctLjZIOS40djEuOGMwIC4yLjIuNC40LjRoLjN2Ny44YzAgLjQuMi41LjcuNWgydi04Yy4xLS40LjYtLjcgMS0uNy42IDAgLjkuNC45IDEuMXY3LjFjMCAuNC4yLjUuNi41aDIuMXYtOGMuMi0uNC42LS43IDEtLjcuNiAwIC45LjQuOSAxLjF2Ny4xYzAgLjQuMi41LjYuNWgyLjl2LTEuN2MuMy0uMy4xLS41LS4yLS41eiIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+');
	}

	/**
	 * Registers settings sections for the plugin admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_sections()
	{
		add_settings_section(
			PDC_POD_NAME . '-credentials',
			'Credentials',
			array($this, 'section_credentials'),
			PDC_POD_NAME . '-general',
		);
		add_settings_section(
			PDC_POD_NAME . '-product',
			'Product',
			array($this, 'section_product'),
			PDC_POD_NAME . '-product',
		);
		add_settings_section(
			PDC_POD_NAME . '-support',
			'Support',
			array($this, 'section_support'),
			PDC_POD_NAME . '-support',
		);
	}

	/**
	 * Registers plugin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings()
	{
		// API key setting: simple string sanitized via sanitize_text_field.
		register_setting(
			PDC_POD_NAME . '-general-options',
			PDC_POD_NAME . '-api_key',
			array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array($this, 'sanitize_api_key'),
			)
		);
		// Environment setting: only allow 'stg' or 'prod'.
		register_setting(
			PDC_POD_NAME . '-general-options',
			PDC_POD_NAME . '-env',
			array(
				'type'              => 'string',
				'default'           => 'stg',
				'sanitize_callback' => array($this, 'sanitize_env'),
			)
		);
		// Product configuration: array of options; currently supports a boolean flag.
		register_setting(
			PDC_POD_NAME . '-product-options',
			PDC_POD_NAME . '-product',
			array(
				'type'              => 'array',
				'default'           => array('use_preset_copies' => false),
				'sanitize_callback' => array($this, 'sanitize_product'),
			)
		);
		// Log level setting: controls which messages are written to the log.
		register_setting(
			PDC_POD_NAME . '-support-options',
			PDC_POD_NAME . '-loglevel',
			array(
				'type'              => 'string',
				'default'           => 'error',
				'sanitize_callback' => array($this, 'sanitize_loglevel'),
			)
		);
	}

	/**
	 * Adds a Print.com tab to the product data tabs.
	 *
	 * @since 1.0.0
	 * @param array $tabs Existing product tabs.
	 * @return array Modified tabs.
	 */
	public function add_product_data_tab($tabs)
	{
		$tabs['pdc_printtab'] = array(
			'label'    => 'Print.com',
			'priority' => 60,
			'target'   => 'pdc_product_data_tab',
			'class'    => array('show_if_simple', 'show_if_variable'),
		);

		return $tabs;
	}

	/**
	 * Sanitizes the API key option value.
	 *
	 * Ensures a trimmed string without unsafe characters is stored.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw option value.
	 * @return string Sanitized API key string.
	 */
	public function sanitize_api_key($value)
	{
		if (is_string($value)) {
			return sanitize_text_field($value);
		}
		return '';
	}

	/**
	 * Sanitizes the environment option value.
	 *
	 * Only 'stg' (test) and 'prod' (live) are accepted. Falls back to 'stg'.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw option value.
	 * @return string 'stg' or 'prod'.
	 */
	public function sanitize_env($value)
	{
		$val = is_string($value) ? strtolower(sanitize_text_field($value)) : '';
		return in_array($val, array('stg', 'prod'), true) ? $val : 'stg';
	}

	/**
	 * Sanitizes the log level option value.
	 *
	 * Only 'none', 'error', and 'debug' are accepted. Falls back to 'error'.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed $value Raw option value.
	 * @return string 'none', 'error', or 'debug'.
	 */
	public function sanitize_loglevel($value)
	{
		$val = is_string($value) ? strtolower(sanitize_text_field($value)) : '';
		return in_array($val, array('none', 'error', 'debug'), true) ? $val : 'error';
	}

	/**
	 * Sanitizes the product configuration option value.
	 *
	 * Currently supports:
	 * - use_preset_copies: bool. Checkbox style input.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw option value.
	 * @return array Sanitized configuration array.
	 */
	public function sanitize_product($value)
	{
		$sanitized = array('use_preset_copies' => false);
		if (is_array($value)) {
			$sanitized['use_preset_copies'] = ! empty($value['use_preset_copies']) ? (bool) intval($value['use_preset_copies']) : false;
		}
		return $sanitized;
	}

	/**
	 * Creates the settings page
	 *
	 * @since       1.0.0
	 * @return      void
	 */
	public function page_general_settings()
	{
		include __DIR__ . 'partials/' . PDC_POD_NAME . '-admin-general.php';
	}

	/**
	 * Creates the credentials section
	 *
	 * @since       1.0.0
	 * @return      void
	 */
	public function section_credentials()
	{
		include __DIR__ . 'partials/' . PDC_POD_NAME . '-admin-section-credentials.php';
	}

	/**
	 * Creates the product configuration section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function section_product()
	{
		include __DIR__ . 'partials/' . PDC_POD_NAME . '-admin-section-product.php';
	}

	/**
	 * Creates the support section.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function section_support()
	{
		include __DIR__ . '/partials/' . PDC_POD_NAME . '-admin-section-support.php';
	}

	public function register_pdc_endpoints()
	{

		register_rest_route(
			'pdc/v1',
			'/verify',
			array(
				'methods'             => 'GET',
				'callback'            => array($this, 'pdc_pod_verify_key'),
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				},
			)
		);
		register_rest_route(
			'pdc/v1',
			'/download-logs',
			array(
				'methods'             => 'POST',
				'callback'            => array($this, 'download_logs'),
				'permission_callback' => function () {
					return current_user_can('manage_options');
				},
			)
		);
	}

	/**
	 * Handles verification
	 * Hooked to endoint /verify
	 *
	 * @since 1.0.0
	 * @return bool|WP_Error
	 */
	public function pdc_pod_verify_key()
	{
		$is_authenticated = $this->pdc_client->is_authenticated();
		if (! $is_authenticated) {
			return new \WP_Error(
				'pdc_pod_not_authenticated',
				__('Invalid credentials.', 'pdc-pod'),
				array('status' => 401)
			);
		}

		return true;
	}

	/**
	 * REST callback to trigger a download of the plugin log file.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function download_logs()
	{
		$logger = Logger::get_instance();
		$logger->download_log();
	}
}
