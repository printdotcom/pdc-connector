<?php
/**
 * Test APIClient functionality
 *
 * Tests for the Print.com API client, specifically focusing on the header
 * merging functionality that was simplified in version 1.0.1.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/tests
 * @since 1.0.1
 */

namespace PdcPod\Tests;

use PdcPod\Admin\PrintDotCom\APIClient;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * APIClient test case.
 *
 * Tests the APIClient class, focusing on HTTP request handling and
 * header merging behavior.
 *
 * @since 1.0.1
 */
class Test_APIClient extends TestCase {

    public static function setUpBeforeClass() : void
    {
		if (!defined('PDC_POD_NAME')) {
			define('PDC_POD_NAME', 'pdc-pod');
		}
    }

	/**
	 * Tests that constructor sets base URL from PDC_POD_API_BASE_URL environment variable.
	 *
	 * @since 1.0.1
	 */
	public function test_constructor_sets_base_url_using_env() {
        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args'  => ['pdc-pod-api_key'],
            'return' => 'fake-api-key',
        ] );

		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );

		$client = new APIClient();

		$this->assertEquals( 'https://testapi.print.com', $client->get_api_base_url() );

		putenv( 'PDC_POD_API_BASE_URL' );
	}

    /**
	 * Tests that constructor sets base URL to api.print.com when stored environment is prod
	 *
	 * @since 1.0.1
	 */
	public function test_constructor_sets_printcom_baseurl_when_env_option_is_prod() {
        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args'  => ['pdc-pod-env'],
            'return' => 'prod',
        ] );

        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args'  => ['pdc-pod-api_key'],
            'return' => 'fake-api-key',
        ] );

		$client = new APIClient();

		$this->assertEquals( 'https://api.print.com', $client->get_api_base_url() );
	}

    /**
	 * Tests that constructor sets base URL to api.print.com when stored environment is prod
	 *
	 * @since 1.0.1
	 */
	public function test_constructor_sets_printcom_baseurl_when_env_option_is_not_set() {
        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args'  => ['pdc-pod-env'],
        ] );

        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args'  => ['pdc-pod-api_key'],
            'return' => 'fake-api-key',
        ] );

		$client = new APIClient();

		$this->assertEquals( 'https://api.stg.print.com', $client->get_api_base_url() );
	}


    /**
	 * Ensures that presets are sorted by title
	 *
	 * @since 1.0.1
	 */
	public function test_sorts_presets_by_title() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$body = json_encode( [
			'items' => [
				[ 'sku' => 'test-posters', 'title' => [ 'en' => 'Poster B1' ], 'id' => '1' ],
				[ 'sku' => 'test-posters', 'title' => [ 'en' => 'Poster A2' ], 'id' => '2' ],
				[ 'sku' => 'test-posters', 'title' => [ 'en' => 'Poster A10' ], 'id' => '3' ],
				[ 'sku' => 'test-posters', 'title' => [ 'en' => 'Poster A1' ], 'id' => '4' ],
				[ 'sku' => 'test-posters', 'title' => [ 'en' => 'Poster A0' ], 'id' => '5' ],
			],
		] );

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_body', [ 'return' => $body ] );

		$client  = new APIClient();
		$presets = $client->get_presets( 'test-posters' );

		$this->assertEquals( 'Poster A0', $presets[0]->title );
		$this->assertEquals( 'Poster A1', $presets[1]->title );
		$this->assertEquals( 'Poster A2', $presets[2]->title );
		$this->assertEquals( 'Poster A10', $presets[3]->title );
		$this->assertEquals( 'Poster B1', $presets[4]->title );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}

	/**
	 * Tests that get_preset_by_id returns no 'accessories' key when the preset
	 * configuration contains no _accessories.
	 *
	 * @since 1.4.0
	 */
	public function test_get_preset_by_id_returns_no_accessories_key_when_none_configured() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$preset_body = '{"sku":"poster-a4","configuration":{"copies":1}}';

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_body', [ 'return' => $preset_body ] );

		$client     = new APIClient();
		$reflection = new \ReflectionMethod( APIClient::class, 'get_preset_by_id' );
		$result     = $reflection->invoke( $client, 'preset-id-123' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'sku', $result );
		$this->assertArrayHasKey( 'options', $result );
		$this->assertArrayNotHasKey( 'accessories', $result );
		$this->assertEquals( 'poster-a4', $result['sku'] );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}

	/**
	 * Tests that get_preset_by_id resolves accessories via a second API call,
	 * adds them to the result, and strips _accessories from the options object.
	 *
	 * @since 1.4.0
	 */
	public function test_get_preset_by_id_resolves_accessories_and_strips_them_from_options() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$preset_body      = '{"sku":"poster-a4","configuration":{"copies":1,"_accessories":{"acc-001":2}}}';
		$accessories_body = '[{"id":"acc-001","sku":"envelope","configuration":{"size":"A4"}}]';

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction(
			'wp_remote_retrieve_body',
			[ 'return_in_order' => [ $preset_body, $accessories_body ] ]
		);

		$client     = new APIClient();
		$reflection = new \ReflectionMethod( APIClient::class, 'get_preset_by_id' );
		$result     = $reflection->invoke( $client, 'preset-id-123' );

		$this->assertArrayHasKey( 'accessories', $result );
		$this->assertCount( 1, $result['accessories'] );

		$resolved = $result['accessories'][0];
		$this->assertEquals( 'acc-001', $resolved->id );
		$this->assertEquals( 'envelope', $resolved->sku );
		$this->assertEquals( 2, $resolved->copies );

		$this->assertFalse( property_exists( $result['options'], '_accessories' ) );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}

	/**
	 * Tests that get_preset_by_id skips an accessory whose ID is absent
	 * from the product accessories list, and omits the 'accessories' key.
	 *
	 * @since 1.4.0
	 */
	public function test_get_preset_by_id_skips_accessory_not_found_in_product_list() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$preset_body      = '{"sku":"poster-a4","configuration":{"copies":1,"_accessories":{"missing-acc":1}}}';
		$accessories_body = '[{"id":"other-acc","sku":"tube","configuration":{}}]';

		// Suppress error-level logging to avoid Logger singleton instantiation.
		WP_Mock::userFunction(
			'get_option',
			[
				'args'   => [ 'pdc-pod-loglevel', 'error' ],
				'return' => 'none',
			]
		);

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction(
			'wp_remote_retrieve_body',
			[ 'return_in_order' => [ $preset_body, $accessories_body ] ]
		);

		$client     = new APIClient();
		$reflection = new \ReflectionMethod( APIClient::class, 'get_preset_by_id' );
		$result     = $reflection->invoke( $client, 'preset-id-123' );

		$this->assertIsArray( $result );
		$this->assertArrayNotHasKey( 'accessories', $result );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}

	/**
	 * Tests that prepare_order_item includes a correctly structured
	 * 'accessories' key when the resolved preset has accessories.
	 *
	 * @since 1.4.0
	 */
	public function test_prepare_order_item_includes_accessories_with_correct_structure() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$preset_body      = '{"sku":"poster-a4","configuration":{"copies":1,"_accessories":{"acc-001":2}}}';
		$accessories_body = '[{"id":"acc-001","sku":"envelope","configuration":{"size":"A4"}}]';

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction(
			'wp_remote_retrieve_body',
			[ 'return_in_order' => [ $preset_body, $accessories_body ] ]
		);

		$order = \Mockery::mock( 'WC_Order' );
		$order->shouldReceive( 'get_billing_email' )->andReturn( 'buyer@example.com' );

		$order_item = \Mockery::mock( 'WC_Order_Item_Product' );
		$order_item->shouldReceive( 'get_quantity' )->andReturn( 3 );
		$order_item->shouldReceive( 'get_id' )->andReturn( 99 );

		$shipping_address = [
			'city'       => 'Amsterdam',
			'country'    => 'NL',
			'first_name' => 'John',
			'last_name'  => 'Doe',
			'company'    => '',
			'postcode'   => '1000 AA',
			'address_1'  => 'Keizersgracht 1',
			'phone'      => '+31612345678',
		];

		$client     = new APIClient();
		$reflection = new \ReflectionMethod( APIClient::class, 'prepare_order_item' );
		$result     = $reflection->invoke(
			$client,
			$order,
			$order_item,
			'preset-id-123',
			'https://example.com/file.pdf',
			$shipping_address,
			[]
		);

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'accessories', $result );
		$this->assertCount( 1, $result['accessories'] );

		$accessory = $result['accessories'][0];
		$this->assertEquals( 'envelope', $accessory['sku'] );
		$this->assertEquals( 'acc-001', $accessory['accessoryId'] );
		$this->assertEquals( 2, $accessory['options']->copies );
		$this->assertCount( 1, $accessory['shipments'] );
		$this->assertEquals( 2, $accessory['shipments'][0]['copies'] );
		$this->assertEquals( 'buyer@example.com', $accessory['shipments'][0]['address']['email'] );
		$this->assertEquals( 'Amsterdam', $accessory['shipments'][0]['address']['city'] );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}

	/**
	 * Tests that prepare_order_item excludes the 'accessories' key when
	 * the preset has no _accessories configured.
	 *
	 * @since 1.4.0
	 */
	public function test_prepare_order_item_excludes_accessories_key_when_preset_has_none() {
		putenv( 'PDC_POD_API_BASE_URL=https://testapi.print.com' );
		putenv( 'PDC_POD_API_KEY=fake-key' );

		$preset_body = '{"sku":"poster-a4","configuration":{"copies":1}}';

		WP_Mock::userFunction( 'wp_remote_request', [ 'return' => [] ] );
		WP_Mock::userFunction( 'is_wp_error', [ 'return' => false ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [ 'return' => 200 ] );
		WP_Mock::userFunction( 'wp_remote_retrieve_body', [ 'return' => $preset_body ] );

		$order = \Mockery::mock( 'WC_Order' );
		$order->shouldReceive( 'get_billing_email' )->andReturn( 'buyer@example.com' );

		$order_item = \Mockery::mock( 'WC_Order_Item_Product' );
		$order_item->shouldReceive( 'get_quantity' )->andReturn( 1 );
		$order_item->shouldReceive( 'get_id' )->andReturn( 99 );

		$shipping_address = [
			'city'       => 'Amsterdam',
			'country'    => 'NL',
			'first_name' => 'John',
			'last_name'  => 'Doe',
			'company'    => '',
			'postcode'   => '1000 AA',
			'address_1'  => 'Keizersgracht 1',
			'phone'      => '+31612345678',
		];

		$client     = new APIClient();
		$reflection = new \ReflectionMethod( APIClient::class, 'prepare_order_item' );
		$result     = $reflection->invoke(
			$client,
			$order,
			$order_item,
			'preset-id-123',
			'https://example.com/file.pdf',
			$shipping_address,
			[]
		);

		$this->assertIsArray( $result );
		$this->assertArrayNotHasKey( 'accessories', $result );

		putenv( 'PDC_POD_API_BASE_URL' );
		putenv( 'PDC_POD_API_KEY' );
	}
}
