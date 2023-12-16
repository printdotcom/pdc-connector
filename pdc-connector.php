<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://print.com
 * @since             1.0.0
 * @package           Pdc_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       Print.com Connector
 * Plugin URI:        https://print.com
 * Description:       Allows customers to configure, edit and purchase products via the Print.com API.
 * Version:           1.0.0
 * Author:            Tijmen
 * Author URI:        https://print.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pdc-connector
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PDC_CONNECTOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pdc-connector-activator.php
 */
function activate_pdc_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pdc-connector-activator.php';
	Pdc_Connector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pdc-connector-deactivator.php
 */
function deactivate_pdc_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pdc-connector-deactivator.php';
	Pdc_Connector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pdc_connector' );
register_deactivation_hook( __FILE__, 'deactivate_pdc_connector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pdc-connector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pdc_connector() {

	$plugin = new Pdc_Connector();
	$plugin->run();

}
run_pdc_connector();
