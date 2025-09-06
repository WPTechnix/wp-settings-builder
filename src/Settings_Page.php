<?php
/**
 * The public-facing factory for creating and configuring a settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 * @author WPTechnix <developers@wptechnix.com>
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Interfaces\Asset_Loader_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Renderer_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Sanitizer_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Persistence_Interface;

/**
 * Class Settings_Page
 *
 * This is the main entry point for the library. It provides a fluent interface
 * for developers to define the structure of a settings page. Its `init()` method
 * acts as the Composition Root, assembling all necessary services and activating the page.
 *
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Sections_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Field_Extras from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Settings_Page {

	/**
	 * The main title of the settings page, displayed in the `<h1>` tag.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $page_title = 'Settings';

	/**
	 * The title displayed in the WordPress admin menu.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $menu_title = 'Settings';

	/**
	 * The WordPress capability required to access and save the settings page.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $capability = 'manage_options';

	/**
	 * The slug of the parent menu page under which this page will appear.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $parent_slug = 'options-general.php';

	/**
	 * A map of tab configurations.
	 *
	 * @var array
	 *
	 * @phpstan-var Tabs_Map
	 */
	private array $tabs = [];

	/**
	 * A map of section configurations.
	 *
	 * @var array
	 *
	 * @phpstan-var Sections_Map
	 */
	private array $sections = [];

	/**
	 * A map of field configurations.
	 *
	 * @var array
	 *
	 * @phpstan-var Fields_Map
	 */
	private array $fields = [];

	/**
	 * Factory constructor.
	 *
	 * @param Field_Factory_Interface $field_factory  The factory service for creating field objects.
	 * @param Persistence_Interface   $persistence    The persistence service for storing settings data.
	 * @param Sanitizer_Interface     $sanitizer      The sanitizer service for sanitizing user input.
	 * @param Renderer_Interface      $renderer       The renderer service for rendering the page HTML.
	 * @param Asset_Loader_Interface  $asset_loader   Asset loader for enqueuing scripts and styles.
	 * @param array                   $ajax_actions   A map of AJAX action callbacks, keyed by action name.
	 * @param string                  $option_name Option key where settings are stored.
	 * @param string                  $page_slug   The unique URL slug for the admin page.
	 *
	 * @phpstan-param non-empty-string $option_name
	 * @phpstan-param non-empty-string $page_slug
	 * @phpstan-param array<string, callable> $ajax_actions
	 */
	public function __construct(
		private Field_Factory_Interface $field_factory,
		private Persistence_Interface $persistence,
		private Sanitizer_Interface $sanitizer,
		private Renderer_Interface $renderer,
		private Asset_Loader_Interface $asset_loader,
		private array $ajax_actions,
		private string $option_name,
		private string $page_slug
	) {
	}

	/**
	 * Sets the main title of the settings page.
	 *
	 * @param string $page_title  The title displayed at the top of the page.
	 *
	 * @phpstan-param non-empty-string $page_title
	 *
	 * @return self
	 */
	public function set_page_title( string $page_title ): self {
		$this->page_title = $page_title;
		return $this;
	}

	/**
	 * Sets the title for the admin menu item.
	 *
	 * @param string $menu_title The text for the menu link.
	 *
	 * @phpstan-param non-empty-string $menu_title
	 *
	 * @return self
	 */
	public function set_menu_title( string $menu_title ): self {
		$this->menu_title = $menu_title;
		return $this;
	}

	/**
	 * Sets the WordPress capability required to view and save the settings page.
	 *
	 * @param string $capability A valid WordPress capability string (e.g., 'manage_options').
	 *
	 * @phpstan-param non-empty-string $capability
	 *
	 * @return self
	 */
	public function set_capability( string $capability ): self {
		$this->capability = $capability;
		return $this;
	}

	/**
	 * Sets the parent menu page slug.
	 *
	 * @param string $parent_slug The slug of the parent menu (e.g., 'options-general.php', 'themes.php').
	 *
	 * @phpstan-param non-empty-string $parent_slug
	 *
	 * @return self
	 */
	public function set_parent_slug( string $parent_slug ): self {
		$this->parent_slug = $parent_slug;
		return $this;
	}

	/**
	 * Adds a navigation tab to the settings page.
	 *
	 * @param string      $id A unique identifier for the tab.
	 * @param string      $title The visible title of the tab.
	 * @param string|null $icon  Optional. A Dashicons class for an icon (e.g., 'dashicons-admin-generic').
	 *
	 * @phpstan-param non-empty-string      $id A unique identifier for the tab.
	 * @phpstan-param non-empty-string      $title The visible title of the tab.
	 * @phpstan-param non-empty-string|null $icon  Optional. A Dashicons class for an icon (e.g., 'dashicons-admin-generic').
	 *
	 * @return self
	 */
	public function add_tab( string $id, string $title, ?string $icon = null ): self {
		$sanitized_id = sanitize_key( $id );
		/** @phpstan-var non-empty-string $sanitized_id */
		$this->tabs[ $sanitized_id ] = [
			'id'    => $sanitized_id,
			'title' => $title,
			'icon'  => $icon ?? '',
		];
		return $this;
	}

	/**
	 * Adds a settings section to group related fields.
	 *
	 * @param string      $id          A unique identifier for the section.
	 * @param string      $title       The visible title of the section.
	 * @param string|null $description Optional. A short description displayed below the section title.
	 * @param string|null $tab_id      Optional. The ID of the tab this section belongs to.
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param non-empty-string|null $description
	 * @phpstan-param non-empty-string|null $tab_id
	 *
	 * @return self
	 *
	 * @throws InvalidArgumentException When the specified tab does not exist.
	 */
	public function add_section( string $id, string $title, ?string $description = null, ?string $tab_id = null ): self {
		if ( ! empty( $tab_id ) && ! isset( $this->tabs[ $tab_id ] ) ) {
			throw new InvalidArgumentException( sprintf( 'Cannot add section "%s" to non-existent tab "%s".', $id, $tab_id ) );
		}
		$sanitized_id     = sanitize_key( $id );
		$sanitized_tab_id = ! empty( $tab_id ) ? sanitize_key( $tab_id ) : null;
		/** @phpstan-var non-empty-string $sanitized_id */
		/** @phpstan-var non-empty-string|null $sanitized_tab_id */
		$this->sections[ $sanitized_id ] = [
			'id'          => $sanitized_id,
			'title'       => $title,
			'description' => $description,
			'tab'         => $sanitized_tab_id,
		];
		return $this;
	}

	/**
	 * Adds a setting field to a specified section.
	 *
	 * @param string $id         A unique identifier for the field.
	 * @param string $section_id The ID of the section this field belongs to.
	 * @param string $type       The type of field (e.g., 'text', 'switch', 'select').
	 * @param string $title      The label displayed for the field.
	 * @param array  $extras     Optional. Additional arguments for the field (e.g., 'description', 'options').
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $section_id
	 * @phpstan-param non-empty-string $type
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param Field_Extras $extras
	 *
	 * @return self
	 *
	 * @throws InvalidArgumentException When the specified section does not exist or non-registered field-type called.
	 */
	public function add_field( string $id, string $section_id, string $type, string $title, array $extras = [] ): self {
		$sanitized_id = sanitize_key( $id );
		if ( ! isset( $this->sections[ $section_id ] ) ) {
			throw new InvalidArgumentException( sprintf( 'Cannot add field "%s" to non-existent section "%s".', $id, $section_id ) );
		}

		$registered_fields = $this->field_factory->get_registered_fields();
		if ( ! isset( $registered_fields[ $type ] ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Field type "%s" is not registered.', $type )
			);
		}

		/** @phpstan-var non-empty-string $sanitized_id */
		$this->fields[ $sanitized_id ] = [
			'id'      => $sanitized_id,
			'title'   => $title,
			'section' => $section_id,
			'type'    => $type,
			'name'    => $this->option_name . '[' . $sanitized_id . ']',
			'extras'  => $extras,
		];
		return $this;
	}

	/**
	 * Assembles all services, creates the page definition, and hooks into WordPress.
	 * This method is the final call after the page has been configured. It builds and
	 * activates the entire settings page.
	 */
	public function init(): void {

		$definition = new Page_Definition(
			$this->option_name,
			$this->page_slug,
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->parent_slug,
			$this->tabs,
			$this->sections,
			$this->fields
		);

		$hooks_registrar = new Hooks_Registrar(
			$definition,
			$this->persistence,
			$this->renderer,
			$this->sanitizer,
			$this->asset_loader,
			$this->ajax_actions
		);

		$hooks_registrar->init();
	}

	/**
	 * Get the setting value.
	 *
	 * @param string $key           The specific option key (field ID) to retrieve.
	 * @param mixed  $default_value A final fallback value if no other value is available.
	 *
	 * @phpstan-param non-empty-string $key
	 *
	 * @return mixed
	 */
	public function get_setting( string $key, mixed $default_value = null ): mixed {
		return $this->persistence->get( $key, $default_value );
	}

	/**
	 * Set the setting value.
	 *
	 * @param string $key   The specific option key (field ID) to set.
	 * @param mixed  $value The value to set.
	 *
	 * @phpstan-param non-empty-string $key
	 */
	public function set_setting( string $key, mixed $value ): bool {
		return $this->persistence->set( $key, $value );
	}
}
