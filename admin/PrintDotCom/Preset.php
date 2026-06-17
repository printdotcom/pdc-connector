<?php
/**
 * Print.com Preset model
 *
 * Provides a data structure for representing a Print.com Preset within the admin context.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/admin/PrintDotCom
 * @since 1.0.0
 */

namespace PdcPod\Admin\PrintDotCom;

/**
 * A class representing a Print.com Preset
 *
 * @link       https://print.com
 * @since      1.0.0
 *
 * @package    Pdc_Pod
 * @subpackage Pdc_Pod/admin
 */
class Preset {

	/**
	 * The preset identifier.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $id;

	/**
	 * The product SKU associated with this preset.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $sku;

	/**
	 * The preset title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $title;

	/**
	 * The preset configuration.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public object $configuration;

	/**
	 * Accessory IDs mapped to their quantities.
	 *
	 * @since 1.6.0
	 * @var array<string, int>
	 */
	public array $accessory_ids;

	/**
	 * Resolved accessory objects for this preset.
	 *
	 * @since 1.6.0
	 * @var Accessory[]
	 */
	public array $accessories;

	/**
	 * Constructs a new Preset instance.
	 *
	 * @since 1.0.0
	 *
	 * @param object $raw_preset   The preset retrieved from the API
	 */
	public function __construct( $raw_preset ) {
		$this->id = $raw_preset->id;
		$this->sku = $raw_preset->sku;
		$this->title = $raw_preset->title->en;
		
		$preset_configuration = $raw_preset->configuration;
		unset( $preset_configuration->variants );
		unset( $preset_configuration->deliveryPromise ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$this->accessory_ids = array();
		$this->accessories = array();
		if ( isset( $preset_configuration->_accessories ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$this->accessory_ids = (array) $preset_configuration->_accessories; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			unset( $preset_configuration->_accessories ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		$this->configuration = $preset_configuration;

	}

	/**
	 * Sets the resolved accessories for this preset.
	 *
	 * @since 1.6.0
	 *
	 * @param Accessory[] $accessories The resolved accessories.
	 */
	public function set_accessories( $accessories ) {
		$this->accessories = $accessories;
	}

	/**
	 * Overrides the copy count in the configuration.
	 *
	 * @since 1.6.0
	 *
	 * @param int $copies The number of copies.
	 */
	public function set_copies( $copies ) {
		$this->configuration->copies = $copies;
	}
}
