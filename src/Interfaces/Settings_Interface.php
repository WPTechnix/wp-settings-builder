<?php
/**
 * Defines the public contract for a settings page builder.
 *
 * This interface outlines the methods required for a class that fluently
 * builds and manages a WordPress settings page.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

use WPTechnix\WP_Settings_Builder\Field_Factory;

/**
 * Defines the public contract for a settings page builder.
 *
 * @phpstan-import-type Supported_Field_Type from Field_Factory
 */
interface Settings_Interface {
	/*
	|--------------------------------------------------------------------------
	| Page Configuration
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the main title of the settings page, typically displayed in an `<h1>` tag.
	 *
	 * @param string $page_title The main title for the settings page.
	 *
	 * @phpstan-param non-empty-string $page_title
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function set_page_title( string $page_title ): static;

	/**
	 * Sets the title displayed in the WordPress admin menu for the settings page.
	 *
	 * @param string $menu_title The title for the admin menu item.
	 *
	 * @phpstan-param non-empty-string $menu_title
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function set_menu_title( string $menu_title ): static;

	/**
	 * Sets the required WordPress capability to view and save the settings page.
	 *
	 * @param string $capability The WordPress capability string (e.g., 'manage_options').
	 *
	 * @phpstan-param non-empty-string $capability
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function set_capability( string $capability ): static;

	/**
	 * Sets the parent menu page slug under which this settings page will appear.
	 *
	 * @param string $parent_slug The slug of the parent menu (e.g., 'options-general.php', 'themes.php').
	 *
	 * @phpstan-param non-empty-string $parent_slug
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function set_parent_slug( string $parent_slug ): static;

	/**
	 * Sets the HTML class prefix used for custom styling of the settings page elements.
	 *
	 * @param string $html_prefix The HTML prefix to use for CSS classes.
	 *
	 * @phpstan-param non-empty-string $html_prefix
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function set_html_prefix( string $html_prefix ): static;

	/*
	|--------------------------------------------------------------------------
	| Settings Structure Definition
	|--------------------------------------------------------------------------
	*/

	/**
	 * Adds a navigation tab to the settings page, enabling a tabbed interface.
	 *
	 * @param string      $id    A unique identifier for the tab.
	 * @param string      $title The visible title of the tab.
	 * @param null|string $icon  (Optional) A Dashicons class for an icon (e.g., 'dashicons-admin-generic').
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param null|non-empty-string $icon
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function add_tab( string $id, string $title, ?string $icon = null ): static;

	/**
	 * Adds a settings section to group related fields under a common heading.
	 *
	 * @param string      $id          A unique identifier for the section.
	 * @param string      $title       The visible title of the section.
	 * @param null|string $description (Optional) A short description displayed below the section title.
	 * @param null|string $tab_id      (Optional) The ID of the tab this section belongs to. This is required for tabbed interfaces.
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param null|non-empty-string $description
	 * @phpstan-param null|non-empty-string $tab_id
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function add_section( string $id, string $title, ?string $description = null, ?string $tab_id = null ): static;

	/**
	 * Adds a setting field to a specified section.
	 *
	 * @param string $id         A unique identifier for the field, used as the key in the options array.
	 * @param string $section_id The ID of the section this field belongs to.
	 * @param string $type       The type of field (e.g., 'text', 'toggle', 'select').
	 * @param string $title      The label displayed for the field.
	 * @param array  $extras     (Optional) Additional configuration arguments for the field, such as 'options', 'placeholder', or 'description'.
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $section_id
	 * @phpstan-param Supported_Field_Type $type
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param array<string, mixed> $extras
	 *
	 * @return static Provides a fluent interface for method chaining.
	 */
	public function add_field( string $id, string $section_id, string $type, string $title, array $extras = [] ): static;

	/*
	|--------------------------------------------------------------------------
	| Core Functionality
	|--------------------------------------------------------------------------
	*/

	/**
	 * Hooks the settings framework into WordPress to register and render the page.
	 *
	 * This method must be called for the settings page to be activated and displayed.
	 */
	public function init(): void;

	/**
	 * Retrieves a saved setting value from the database.
	 *
	 * @param string     $key           The specific option key (field ID) to retrieve.
	 * @param mixed|null $default_value A final fallback value if no other value is available.
	 *
	 * @phpstan-param non-empty-string $key
	 *
	 * @return mixed The saved value, the field's default, or the provided default.
	 */
	public function get( string $key, mixed $default_value = null ): mixed;
}
