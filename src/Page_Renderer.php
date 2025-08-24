<?php
/**
 * Handles all HTML output for the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

/**
 * Class responsible for handling all HTML output for the settings page.
 */
final class Page_Renderer {

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
	 * Renders the entire settings page.
	 *
	 * This is the main callback function for `add_submenu_page`.
	 */
	public function render_page(): void {
		// TODO: Implement.
	}

	/**
	 * The WordPress callback for rendering a settings field.
	 *
	 * @param array $args Arguments passed from `add_settings_field`.
	 * @phpstan-param array<non-empty-string, mixed> $args
	 */
	public function render_field( array $args ): void {
		// TODO: Implement.
	}
}
