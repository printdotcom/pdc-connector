<?php
/**
 * Test Logger functionality
 *
 * Tests for the Logger class, focusing on environment resolution
 * added in version 1.4.0.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/tests
 * @since 1.4.0
 */

namespace PdcPod\Tests;

use PdcPod\Includes\Logger;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Logger test case.
 *
 * Tests the Logger class.
 *
 * @since 1.4.0
 */
class Test_Logger extends TestCase {

	public static function setUpBeforeClass(): void {
		if ( ! defined( 'PDC_POD_NAME' ) ) {
			define( 'PDC_POD_NAME', 'pdc-pod' );
		}
	}

	/**
	 * Tests that get_environment returns the PDC_POD_API_BASE_URL env var
	 * when it is set, taking precedence over the stored option.
	 *
	 * @since 1.4.0
	 */
	public function test_get_environment_returns_env_var_when_set() {
		putenv( 'PDC_POD_API_BASE_URL=https://custom.api.example.com' );

		$reflection = new \ReflectionMethod( Logger::class, 'get_environment' );
		$result     = $reflection->invoke( null );

		$this->assertEquals( 'https://custom.api.example.com', $result );

		putenv( 'PDC_POD_API_BASE_URL' );
	}

	/**
	 * Tests that get_environment returns the production URL when
	 * the stored environment option is 'prod'.
	 *
	 * @since 1.4.0
	 */
	public function test_get_environment_returns_production_url_when_option_is_prod() {
		WP_Mock::userFunction(
			'get_option',
			[
				'args'   => [ 'pdc-pod-env' ],
				'return' => 'prod',
			]
		);

		$reflection = new \ReflectionMethod( Logger::class, 'get_environment' );
		$result     = $reflection->invoke( null );

		$this->assertEquals( 'https://api.print.com', $result );
	}

	/**
	 * Tests that get_environment returns the staging URL when
	 * the stored environment option is not 'prod'.
	 *
	 * @since 1.4.0
	 */
	public function test_get_environment_returns_staging_url_when_option_is_not_prod() {
		WP_Mock::userFunction(
			'get_option',
			[
				'args'   => [ 'pdc-pod-env' ],
				'return' => 'stg',
			]
		);

		$reflection = new \ReflectionMethod( Logger::class, 'get_environment' );
		$result     = $reflection->invoke( null );

		$this->assertEquals( 'https://api.stg.print.com', $result );
	}
}
