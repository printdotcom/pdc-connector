<?php
/**
 * Print.com Product model
 *
 * Provides a data structure for representing a Print.com Product within the admin context.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/PrintDotCom
 * @since 1.0.0
 */

namespace PdcConnector\Admin\PrintDotCom;

/**
 * A class representing a Print.com Product
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/admin
 */
class Product {

	/**
	 * The product SKU.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $sku;

	/**
	 * The product title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $title;

	/**
	 * Constructs a new Product instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sku   The product SKU.
	 * @param string $title The product title.
	 */
	public function __construct( $sku, $title ) {
		$this->sku   = $sku ?? '';
		$this->title = $title ?? '';
	}
}
