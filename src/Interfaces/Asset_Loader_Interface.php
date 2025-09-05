<?php
/**
 * Defines the contract for enqueuing static assets on the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Asset_Loader_Interface
 *
 * Provides methods to enqueue scripts and styles.
 */
interface Asset_Loader_Interface {

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param Page_Definition_Interface $definition The definition contract for the page.
	 */
	public function enqueue( Page_Definition_Interface $definition ): void;
}
