<?php
/**
 * Defines the contract for a page definition object.
 *
 * This interface ensures that any service needing configuration data depends on a stable
 * abstraction, not a concrete implementation. This decouples the entire system from the
 * specific structure of the configuration data object.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Page_Definition_Interface
 *
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Sections_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Sections_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
interface Page_Definition_Interface {

	/**
	 * Get option name
	 *
	 * @return non-empty-string
	 */
	public function get_option_name(): string;

	/**
	 * Get option group
	 *
	 * @return non-empty-string
	 */
	public function get_option_group(): string;

	/**
	 * Get page slug
	 *
	 * @return non-empty-string
	 */
	public function get_page_slug(): string;

	/**
	 * Get page title
	 *
	 * @return non-empty-string
	 */
	public function get_page_title(): string;

	/**
	 * Get menu title
	 *
	 * @return non-empty-string
	 */
	public function get_menu_title(): string;

	/**
	 * Get capability
	 *
	 * @return non-empty-string
	 */
	public function get_capability(): string;

	/**
	 * Get parent slug
	 *
	 * @return non-empty-string
	 */
	public function get_parent_slug(): string;

	/**
	 * Get Tabs
	 *
	 * @return array
	 * @phpstan-return Tabs_Map
	 * @psalm-return Tabs_Map
	 */
	public function get_tabs(): array;

	/**
	 * Get sections
	 *
	 * @return array
	 * @phpstan-return Sections_Map
	 * @psalm-return Sections_Map
	 */
	public function get_sections(): array;

	/**
	 * Get Fields
	 *
	 * @return array[]
	 * @phpstan-return Fields_Map
	 * @psalm-return Fields_Map
	 */
	public function get_fields(): array;

	/**
	 * Determines the current active tab from the request.
	 *
	 * @return non-empty-string|false The active tab ID, or false if no tabs exist.
	 */
	public function get_active_tab(): string|false;

	/**
	 * Retrieve the configuration for all fields belongs to
	 * the current page or tab.
	 *
	 * @return array[]
	 * @phpstan-return Fields_Map
	 * @psalm-return Fields_Map
	 */
	public function get_active_fields(): array;
}
