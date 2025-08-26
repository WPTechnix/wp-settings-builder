<?php
/**
 * Manages the enqueueing of static assets for the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Asset_Manager_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;

/**
 * Class Asset_Manager
 *
 * A simple service to handle script and style registration and enqueueing.
 * It ensures assets are only loaded on the relevant settings page.
 */
final class Asset_Manager implements Asset_Manager_Interface {

	/**
	 * {@inheritDoc}
	 */
	public function enqueue( Page_Definition_Interface $definition ): void {
		$fields = $definition->get_fields();
		// TODO: to be implemented.
	}
}
