<?php
/**
 * Defines the contract for a service that renders the settings page HTML.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Renderer_Interface
 */
interface Renderer_Interface {

	/**
	 * Renders the entire settings page HTML to standard output
	 *
	 * @param Page_Definition_Interface $definition The blueprint of the page to render.
	 */
	public function render_page( Page_Definition_Interface $definition ): void;

	/**
	 * Renders a single settings field
	 *
	 * This method is designed to be used as a callback for WordPress's `add_settings_field`.
	 *
	 * @param array $args The arguments array passed by `do_settings_fields`.
	 *                    It must contain a 'config' key with the Field_Config.
	 *
	 * @phpstan-param array<string, mixed> $args
	 */
	public function render_field( array $args ): void;
}
