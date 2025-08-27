<?php
/**
 * Defines the contract for managing static assets on the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Asset_Manager_Interface
 *
 * Provides methods to register and enqueue scripts and styles.
 * Implementations should ensure assets are only loaded on the relevant settings page.
 */
interface Asset_Manager_Interface {

	/**
	 * Register and enqueue scripts and styles.
	 *
	 * @param Page_Definition_Interface $definition The definition contract for the page.
	 */
	public function enqueue( Page_Definition_Interface $definition ): void;
}
