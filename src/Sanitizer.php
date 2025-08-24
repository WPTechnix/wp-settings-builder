<?php
/**
 * Sanitizes settings before they are saved to the database.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

/**
 * Sanitizes settings before they are saved to the database.
 */
final class Sanitizer {

	/**
	 * Class Constructor
	 *
	 * @param Settings_Store $settings_store Settings Store.
	 */
	public function __construct(
		// @phpstan-ignore-next-line
		private Settings_Store $settings_store
	) {}

	/**
	 * Sanitizes the settings array.
	 *
	 * This is the main callback for the 'sanitize_callback' argument in
	 * `register_setting`. It processes the raw input from the $_POST array.
	 *
	 * @param mixed $input The raw input from the form submission (from `$_POST`).
	 *
	 * @return array The complete, sanitized settings array ready for saving.
	 *
	 * @phpstan-return array<string, mixed>
	 */
	public function sanitize( mixed $input ): array {
		// TODO: Implement sanitization logic.
		if ( ! is_array( $input ) ) {
			return [];
		}
		return $input;
	}
}
