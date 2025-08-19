<?php

namespace PdcConnector\Admin\PrintDotCom;

/**
 * A class representing a Print.com Preset
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Connector
 * @subpackage Pdc_Connector/admin
 */

class Preset {

	public string $id;
	public string $sku;
	public string $title;

	public function __construct( $sku, $title, $id ) {
		$this->id    = $id;
		$this->sku   = $sku ?? '';
		$this->title = $title ?? '';
	}
}
