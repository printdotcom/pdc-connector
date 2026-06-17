<?php

/**
 * Print.com Accessory model
 *
 * Provides a data structure for representing a Print.com Accessory within the admin context.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/admin/PrintDotCom
 * @since 1.6.0
 */

namespace PdcPod\Admin\PrintDotCom;

/**
 * Class representing a Print.com Accessory.
 *
 * @link       https://print.com
 * @since      1.6.0
 *
 * @package    Pdc_Pod
 * @subpackage Pdc_Pod/admin
 */
class Accessory {

	/**
	 * The accessory identifier.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public string $accessory_id;

	/**
	 * The accessory SKU.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public string $sku;

	/**
	 * The number of copies for this accessory.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	public int $copies;

	/**
	 * The accessory configuration.
	 *
	 * @since 1.6.0
	 * @var object
	 */
	public object $configuration;

	/**
	 * Constructs a new Accessory instance.
	 *
	 * @since 1.6.0
	 *
	 * @param string $accessory_id  The accessory identifier.
	 * @param string $sku           The accessory SKU.
	 * @param object $configuration The accessory configuration.
	 * @param int    $copies        The number of copies.
	 */
	public function __construct( $accessory_id, $sku, $configuration, $copies ) {
		$this->accessory_id  = $accessory_id;
		$this->sku           = $sku;
		$this->configuration = $configuration;
		$this->copies        = $copies;
	}
}
