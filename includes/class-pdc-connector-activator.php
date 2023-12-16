<?php

/**
 * Fired during plugin activation
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/includes
 * @author     Tijmen <tijmen@print.com>
 */
class Pdc_Connector_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'pdc_orders';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			pdc_ordernumber varchar(55) DEFAULT '' NOT NULL,
			pdc_status varchar(55) DEFAULT '' NOT NULL,
			wp_order_id mediumint(9) NOT NULL,
			pdc_price DECIMAL(10,2) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta($sql);
	}
}
