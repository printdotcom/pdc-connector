<?php

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

	public string $sku;
	public string $title;

	public function __construct( $sku, $title ) {
		$this->sku   = $sku ?? '';
		$this->title = $title ?? '';
	}
}
