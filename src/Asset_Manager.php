<?php
/**
 * Manage styling and scripts for the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

/**
 * Handles the enqueueing and rendering of all static assets for the settings page.
 */
final class Asset_Manager {

	/**
	 * Class Constructor
	 *
	 * @param string $html_prefix HTML prefix to use in assets.
	 *
	 * @phpstan-param non-empty-string $html_prefix
	 */
	public function __construct(
		// @phpstan-ignore-next-line
		private string $html_prefix
	) {}

	/**
	 * Enqueues all necessary scripts and styles for the settings page.
	 *
	 * @internal This method is hooked into 'admin_enqueue_scripts' to loads assets (not to be called directly).
	 */
	public function enqueue(): void {
	}
}
