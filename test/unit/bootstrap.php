<?php
/**
 * PHPUnit bootstrap file
 *
 * Initializes the testing environment for WordPress plugin tests.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/tests
 * @since 1.0.1
 */

require_once dirname( __DIR__ ) . '/../vendor/autoload.php';

WP_Mock::bootstrap();

// Minimal WP_Error stub — the real class is only available inside a full WordPress
// environment; we only need the constructor and basic accessors for unit tests.
if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public string $code;
		public string $message;
		/** @var mixed */
		public $data;

		/**
		 * @param string $code
		 * @param string $message
		 * @param mixed  $data
		 */
		public function __construct( $code = '', $message = '', $data = '' ) {
			$this->code    = (string) $code;
			$this->message = (string) $message;
			$this->data    = $data;
		}

		public function get_error_code(): string {
			return $this->code;
		}

		public function get_error_message(): string {
			return $this->message;
		}

		/** @return mixed */
		public function get_error_data() {
			return $this->data;
		}
	}
}
