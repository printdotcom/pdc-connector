<?php
/**
 * Test Settings functionality
 *
 * Tests for the Settings class, covering all sanitization callbacks and the
 * verify-key REST endpoint handler.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/tests
 * @since 1.5.0
 */

namespace PdcPod\Tests;

use PdcPod\Admin\Settings;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Settings test case.
 *
 * @since 1.5.0
 */
class Test_Settings extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		if ( ! defined( 'PDC_POD_NAME' ) ) {
			define( 'PDC_POD_NAME', 'pdc-pod' );
		}
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Creates a Settings instance backed by a mock APIClient.
	 *
	 * @return Settings
	 */
	private function make_settings(): Settings
	{
		$mock_client = \Mockery::mock( 'PdcPod\\Admin\\PrintDotCom\\APIClient' );
		return new Settings( $mock_client );
	}

	// -------------------------------------------------------------------------
	// sanitize_api_key()
	// -------------------------------------------------------------------------

	/**
	 * Tests that sanitize_api_key returns the sanitized string for string input.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_api_key_returns_sanitized_string(): void
	{
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ 'my-api-key' ],
			'return' => 'my-api-key',
		] );

		$result = $this->make_settings()->sanitize_api_key( 'my-api-key' );

		$this->assertSame( 'my-api-key', $result );
	}

	/**
	 * Tests that sanitize_api_key returns an empty string for non-string input.
	 *
	 * sanitize_text_field must not be called when the value is not a string.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_api_key_returns_empty_for_non_string(): void
	{
		$settings = $this->make_settings();

		$this->assertSame( '', $settings->sanitize_api_key( null ) );
		$this->assertSame( '', $settings->sanitize_api_key( [] ) );
		$this->assertSame( '', $settings->sanitize_api_key( 42 ) );
	}

	// -------------------------------------------------------------------------
	// sanitize_env()
	// -------------------------------------------------------------------------

	/**
	 * Provides test cases for sanitize_env().
	 *
	 * Each entry contains: raw input, expected output.
	 *
	 * @return array<string, array{mixed, string}>
	 */
	public function provide_sanitize_env_cases(): array
	{
		return [
			'prod lowercase'  => [ 'prod', 'prod' ],
			'stg lowercase'   => [ 'stg',  'stg' ],
			'PROD uppercase'  => [ 'PROD', 'prod' ],
			'STG uppercase'   => [ 'STG',  'stg' ],
			'mixed Stg'       => [ 'Stg',  'stg' ],
			'invalid value'   => [ 'live', 'stg' ],
			'empty string'    => [ '',     'stg' ],
		];
	}

	/**
	 * Tests that sanitize_env accepts only 'stg' and 'prod', defaulting to 'stg'.
	 *
	 * @since 1.5.0
	 * @dataProvider provide_sanitize_env_cases
	 * @testdox sanitize_env() returns '$expected' for string input '$input'
	 *
	 * @param mixed  $input    Raw string input.
	 * @param string $expected Expected sanitized environment value.
	 */
	public function test_sanitize_env_with_string_input( $input, string $expected ): void
	{
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ $input ],
			'return' => $input,
		] );

		$this->assertSame( $expected, $this->make_settings()->sanitize_env( $input ) );
	}

	/**
	 * Tests that sanitize_env returns 'stg' for non-string input.
	 *
	 * sanitize_text_field must not be called when the value is not a string.
	 *
	 * @since 1.5.0
	 * @testdox sanitize_env() returns 'stg' for non-string input
	 */
	public function test_sanitize_env_returns_default_for_non_string(): void
	{
		$settings = $this->make_settings();

		$this->assertSame( 'stg', $settings->sanitize_env( null ) );
		$this->assertSame( 'stg', $settings->sanitize_env( [] ) );
		$this->assertSame( 'stg', $settings->sanitize_env( 42 ) );
	}

	// -------------------------------------------------------------------------
	// sanitize_loglevel()
	// -------------------------------------------------------------------------

	/**
	 * Provides test cases for sanitize_loglevel().
	 *
	 * Each entry contains: raw input, expected sanitized output.
	 *
	 * @return array<string, array{string, string}>
	 */
	public function provide_sanitize_loglevel_cases(): array
	{
		return [
			'valid error level'   => [ 'error', 'error' ],
			'valid none level'    => [ 'none',  'none' ],
			'valid debug level'   => [ 'debug', 'debug' ],
			'uppercase ERROR'     => [ 'ERROR', 'error' ],
			'uppercase DEBUG'     => [ 'DEBUG', 'debug' ],
			'mixed case None'     => [ 'None',  'none' ],
			'invalid value info'  => [ 'info',  'error' ],
			'empty string'        => [ '',      'error' ],
		];
	}

	/**
	 * Tests that sanitize_loglevel returns the correct value for string input.
	 *
	 * @since 1.5.0
	 * @dataProvider provide_sanitize_loglevel_cases
	 * @testdox sanitize_loglevel() returns '$expected' for string input '$input'
	 *
	 * @param string $input    Raw string input.
	 * @param string $expected Expected sanitized log level.
	 */
	public function test_sanitize_loglevel_with_string_input( string $input, string $expected ): void
	{
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ $input ],
			'return' => $input,
		] );

		$this->assertSame( $expected, $this->make_settings()->sanitize_loglevel( $input ) );
	}

	/**
	 * Tests that sanitize_loglevel returns 'error' for non-string input.
	 *
	 * sanitize_text_field is not called when the value is not a string.
	 *
	 * @since 1.5.0
	 * @testdox sanitize_loglevel() returns 'error' for non-string input
	 */
	public function test_sanitize_loglevel_returns_default_for_non_string(): void
	{
		$settings = $this->make_settings();

		$this->assertSame( 'error', $settings->sanitize_loglevel( null ) );
		$this->assertSame( 'error', $settings->sanitize_loglevel( [] ) );
		$this->assertSame( 'error', $settings->sanitize_loglevel( 42 ) );
	}

	// -------------------------------------------------------------------------
	// sanitize_product()
	// -------------------------------------------------------------------------

	/**
	 * Tests that sanitize_product returns true for use_preset_copies when set to '1'.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_product_sets_use_preset_copies_true_when_enabled(): void
	{
		$result = $this->make_settings()->sanitize_product( [ 'use_preset_copies' => '1' ] );

		$this->assertSame( [ 'use_preset_copies' => true ], $result );
	}

	/**
	 * Tests that sanitize_product returns false for use_preset_copies when empty.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_product_sets_use_preset_copies_false_when_empty(): void
	{
		$result = $this->make_settings()->sanitize_product( [ 'use_preset_copies' => '' ] );

		$this->assertSame( [ 'use_preset_copies' => false ], $result );
	}

	/**
	 * Tests that sanitize_product returns the default array for non-array input.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_product_returns_defaults_for_non_array(): void
	{
		$settings = $this->make_settings();

		$this->assertSame( [ 'use_preset_copies' => false ], $settings->sanitize_product( null ) );
		$this->assertSame( [ 'use_preset_copies' => false ], $settings->sanitize_product( 'enabled' ) );
	}

	// -------------------------------------------------------------------------
	// sanitize_purchasing()
	// -------------------------------------------------------------------------

	/**
	 * Tests that sanitize_purchasing returns both keys sanitized and lowercased.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_returns_both_keys_lowercased(): void
	{
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ 'PSP' ],
			'return' => 'PSP',
		] );
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ 'Automatic' ],
			'return' => 'Automatic',
		] );

		$result = $this->make_settings()->sanitize_purchasing( [
			'purchase-payment' => 'PSP',
			'auto-purchase'    => 'Automatic',
		] );

		$this->assertSame( 'psp',       $result['purchase-payment'] );
		$this->assertSame( 'automatic', $result['auto-purchase'] );
	}

	/**
	 * Tests that sanitize_purchasing preserves only keys present in the input.
	 *
	 * When only 'purchase-payment' is provided, 'auto-purchase' must be absent.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_only_stores_present_keys(): void
	{
		WP_Mock::userFunction( 'sanitize_text_field', [
			'args'   => [ 'directdebit' ],
			'return' => 'directdebit',
		] );

		$result = $this->make_settings()->sanitize_purchasing( [
			'purchase-payment' => 'directdebit',
		] );

		$this->assertArrayHasKey( 'purchase-payment', $result );
		$this->assertArrayNotHasKey( 'auto-purchase', $result );
		$this->assertSame( 'directdebit', $result['purchase-payment'] );
	}

	/**
	 * Tests that sanitize_purchasing falls back to 'banktransfer' when
	 * 'purchase-payment' is a non-string value.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_payment_defaults_to_banktransfer_for_non_string(): void
	{
		$result = $this->make_settings()->sanitize_purchasing( [
			'purchase-payment' => 42,
		] );

		$this->assertSame( 'banktransfer', $result['purchase-payment'] );
	}

	/**
	 * Tests that sanitize_purchasing falls back to 'manual' when
	 * 'auto-purchase' is a non-string value.
	 *
	 * Note: null is intentionally excluded here because isset() returns false
	 * for null, meaning the key is simply absent from the result rather than
	 * falling back to the default. This test uses an integer value (42) to
	 * exercise the explicit fallback branch.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_auto_purchase_defaults_to_manual_for_non_string(): void
	{
		$result = $this->make_settings()->sanitize_purchasing( [
			'auto-purchase' => 42,
		] );

		$this->assertSame( 'manual', $result['auto-purchase'] );
	}

	/**
	 * Tests that sanitize_purchasing returns an empty array for non-array input.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_returns_empty_array_for_non_array(): void
	{
		$settings = $this->make_settings();

		$this->assertSame( [], $settings->sanitize_purchasing( 'banktransfer' ) );
		$this->assertSame( [], $settings->sanitize_purchasing( null ) );
	}

	/**
	 * Tests that sanitize_purchasing returns an empty array for an empty array input.
	 *
	 * Neither key is added when neither key is present in the input.
	 *
	 * @since 1.5.0
	 */
	public function test_sanitize_purchasing_returns_empty_array_for_empty_input(): void
	{
		$this->assertSame( [], $this->make_settings()->sanitize_purchasing( [] ) );
	}

	// -------------------------------------------------------------------------
	// pdc_pod_verify_key()
	// -------------------------------------------------------------------------

	/**
	 * Tests that pdc_pod_verify_key returns true when the API client is authenticated.
	 *
	 * @since 1.5.0
	 */
	public function test_pdc_pod_verify_key_returns_true_when_authenticated(): void
	{
		$mock_client = \Mockery::mock( 'PdcPod\\Admin\\PrintDotCom\\APIClient' );
		$mock_client->shouldReceive( 'is_authenticated' )->once()->andReturn( true );

		$settings = new Settings( $mock_client );

		$this->assertTrue( $settings->pdc_pod_verify_key() );
	}

	/**
	 * Tests that pdc_pod_verify_key returns a WP_Error with status 401 when
	 * the API client is not authenticated.
	 *
	 * @since 1.5.0
	 */
	public function test_pdc_pod_verify_key_returns_wp_error_when_not_authenticated(): void
	{
		WP_Mock::userFunction( '__', [
			'return_arg' => 0,
		] );

		$mock_client = \Mockery::mock( 'PdcPod\\Admin\\PrintDotCom\\APIClient' );
		$mock_client->shouldReceive( 'is_authenticated' )->once()->andReturn( false );

		$settings = new Settings( $mock_client );
		$result   = $settings->pdc_pod_verify_key();

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'pdc_pod_not_authenticated', $result->get_error_code() );
		$this->assertSame( 401, $result->get_error_data()['status'] );
	}
}
