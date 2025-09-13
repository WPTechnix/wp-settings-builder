<?php
/**
 * Defines the contract for a service that sanitizes settings data.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Sanitizer_Interface
 */
interface Sanitizer_Interface {

	/**
	 * Sanitizes an array of raw input data against a page definition
	 *
	 * @param array<array-key, mixed>   $raw_input The raw data, typically from $_POST.
	 * @param Page_Definition_Interface $definition The blueprint of the page to sanitize against.
	 *
	 * @return array<array-key, mixed> The sanitized data ready for persistence.
	 */
	public function sanitize( array $raw_input, Page_Definition_Interface $definition ): array;
}
