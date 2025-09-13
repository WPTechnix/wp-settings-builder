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
 * @psalm-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Sections_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Page_Definition implements Page_Definition_Interface {

	/**
	 * Constructs the Page Definition.
	 *
	 * @param non-empty-string $option_name The key used to store data in the database.
	 * @param non-empty-string $page_slug The unique slug for the admin menu page.
	 * @param non-empty-string $page_title The title displayed in the `<h1>` tag of the page.
	 * @param non-empty-string $menu_title The title displayed in the admin menu.
	 * @param non-empty-string $capability The WordPress capability required to access the page.
	 * @param non-empty-string $parent_slug The parent menu slug under which this page appears.
	 * @param array            $tabs A map of tab configurations.
	 * @param array            $sections A map of section configurations.
	 * @param array            $fields A map of field configurations.
	 * @phpstan-param Tabs_Map $tabs
	 * @phpstan-param Sections_Map $sections A map of section configurations.
	 * @phpstan-param Fields_Map $fields A map of field configurations.
	 * @psalm-param Tabs_Map $tabs
	 * @psalm-param Sections_Map $sections A map of section configurations.
	 * @psalm-param Fields_Map $fields A map of field configurations.
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
	#[\Override]
	public function get_option_name(): string {
		return $this->option_name;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_option_group(): string {
		return $this->option_name . '_group';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_page_slug(): string {
		return $this->page_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_page_title(): string {
		return $this->page_title;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_menu_title(): string {
		return $this->menu_title;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_parent_slug(): string {
		return $this->parent_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_tabs(): array {
		return $this->tabs;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_sections(): array {
		return $this->sections;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_fields(): array {
		return $this->fields;
	}
}
