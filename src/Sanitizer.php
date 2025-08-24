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
	 * @param null|string $active_tab The currently active tab.
	 *
	 * @phpstan-param null|non-empty-string $active_tab
	 */
	public function __construct(
		// @phpstan-ignore-next-line
		private ?string $active_tab
	) {}

	/**
	 * Sanitizes the settings array.
	 *
	 * This is the main callback for the 'sanitize_callback' argument in
	 * `register_setting`. It processes the raw input from the $_POST array.
	 *
	 * @param mixed $input The raw input from the form submission (from `$_POST`).
	 *
	 * @return array<string, mixed> The complete, sanitized settings array ready for saving.
	 */
	public function sanitize( mixed $input ): array {
		// TODO: Implement sanitization logic.
		if ( ! is_array( $input ) ) {
			return [];
		}
		return $input;
	}
}
