<?php
/**
 * An immutable value object representing the complete definition of a settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;

/**
 * Class Page_Definition
 *
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Sections_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Page_Definition implements Page_Definition_Interface {

	/**
	 * Constructs the Page Definition.
	 *
	 * @param string $option_name The key used to store data in the database.
	 * @param string $page_slug The unique slug for the admin menu page.
	 * @param string $page_title The title displayed in the `<h1>` tag of the page.
	 * @param string $menu_title The title displayed in the admin menu.
	 * @param string $capability The WordPress capability required to access the page.
	 * @param string $parent_slug The parent menu slug under which this page appears.
	 * @param array  $tabs A map of tab configurations.
	 * @param array  $sections A map of section configurations.
	 * @param array  $fields A map of field configurations.
	 *
	 * @phpstan-param non-empty-string $option_name
	 * @phpstan-param non-empty-string $page_slug
	 * @phpstan-param non-empty-string $page_title
	 * @phpstan-param non-empty-string $menu_title
	 * @phpstan-param non-empty-string $capability
	 * @phpstan-param non-empty-string $parent_slug
	 * @phpstan-param Tabs_Map $tabs
	 * @phpstan-param Sections_Map $sections A map of section configurations.
	 * @phpstan-param Fields_Map $fields A map of field configurations.
	 */
	public function __construct(
		public string $option_name,
		public string $page_slug,
		public string $page_title,
		public string $menu_title,
		public string $capability,
		public string $parent_slug,
		public array $tabs,
		public array $sections,
		public array $fields
	) {
	}


	/**
	 * {@inheritDoc}
	 */
	public function get_option_name(): string {
		return $this->option_name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_option_group(): string {
		return $this->option_name . '_group';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_page_slug(): string {
		return $this->page_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_page_title(): string {
		return $this->page_title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_menu_title(): string {
		return $this->menu_title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_parent_slug(): string {
		return $this->parent_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_tabs(): array {
		return $this->tabs;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_sections(): array {
		return $this->sections;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_fields(): array {
		return $this->fields;
	}
}
