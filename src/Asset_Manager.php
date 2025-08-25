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
	 * The HTML prefix that will be used in generated CSS/JS.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $html_prefix = 'wptechnix-settings'; // @phpstan-ignore-line

	/**
	 * Set HTML prefix that will be used in generated CSS/JS.
	 *
	 * @param string $html_prefix HTML prefix.
	 *
	 * @phpstan-param non-empty-string $html_prefix
	 */
	public function set_html_prefix( string $html_prefix ): void {
		$this->html_prefix = $html_prefix;
	}

	/**
	 * Enqueues all necessary scripts and styles for the settings page.
	 *
	 * @internal This method is hooked into 'admin_enqueue_scripts' to loads assets (not to be called directly).
	 */
	public function enqueue(): void {
	}
}
