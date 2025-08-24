<?php
/**
 * Responsible for storing and retrieving settings data.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;

/**
 * Stores and retrieves settings data.
 *
 * @phpstan-import-type Supported_Field_Type from Field_Factory
 * @phpstan-import-type Field_Config from Field_Factory
 *
 * @phpstan-type Section_Config array{
 *   title: non-empty-string,
 *   description: null|non-empty-string,
 *   tab_id: null|non-empty-string,
 * }
 *
 * @phpstan-type Tab_Config array{
 *   title: non-empty-string,
 *   icon: null|non-empty-string
 * }
 *
 * @phpstan-type Fields_Config array<non-empty-string,Field_Config>
 * @phpstan-type Sections_Config array<non-empty-string,Section_Config>
 * @phpstan-type Tabs_Config array<non-empty-string,Tab_Config>
 */
final class Settings_Store {

	/**
	 * Tabs configuration.
	 *
	 * @var array
	 *
	 * @phpstan-var Tabs_Config
	 */
	private array $tabs = [];

	/**
	 * Sections configuration.
	 *
	 * @var array
	 *
	 * @phpstan-var Sections_Config
	 */
	private array $sections = [];

	/**
	 * Fields configuration.
	 *
	 * @var array
	 *
	 * @phpstan-var Fields_Config
	 */
	private array $fields = [];

	/**
	 * Active tab
	 *
	 * @var null|string
	 *
	 * @phpstan-var null|non-empty-string
	 */
	private ?string $active_tab = null;

	/**
	 * Saved settings data.
	 *
	 * @var array
	 *
	 * @phpstan-var array<string, mixed>
	 */
	private array $saved_settings = [];

	/**
	 * Class Constructor
	 *
	 * @param Field_Factory $field_factory Field factory.
	 * @param string        $option_name   WP Option that store the settings.
	 * @param string        $page_slug     Admin page slug.
	 *
	 * @phpstan-param non-empty-string $option_name
	 * @phpstan-param non-empty-string $page_slug
	 */
	public function __construct(
		private Field_Factory $field_factory,
		private string $option_name,
		private string $page_slug,
	) {}

	/**
	 * Gets a saved option value from the database.
	 *
	 * This is the primary method for retrieving a field's current value for rendering.
	 * It intelligently falls back to the field's configured 'default' value if no
	 * saved value exists in the database.
	 *
	 * @param string     $key The specific option key (field ID) to retrieve.
	 * @param mixed|null $default_value A final fallback value if no saved option or field default is found.
	 *
	 * @phpstan-param non-empty-string $key
	 *
	 * @return mixed The saved value, the field's default value, or the provided default.
	 */
	public function get( string $key, mixed $default_value = null ): mixed {
		$this->fetch_settings();
		if ( isset( $this->saved_settings[ $key ] ) ) {
			return $this->saved_settings[ $key ];
		}

		return $default_value;
	}

	/**
	 * Fetch the stored settings from option.
	 *
	 * @param bool $force Force fetch.
	 */
	public function fetch_settings( bool $force = false ): void {
		if ( empty( $this->saved_settings ) || $force ) {
			$settings_from_db     = get_option( $this->option_name, [] );
			$this->saved_settings = is_array( $settings_from_db ) ? $settings_from_db : [];
		}
	}

	/**
	 * Get Page Slug
	 *
	 * @return string
	 * @phpstan-return non-empty-string
	 */
	public function get_page_slug(): string {
		return $this->page_slug;
	}

	/**
	 * Get option name.
	 *
	 * @return string
	 * @phpstan-return non-empty-string
	 */
	public function get_option_name(): string {
		return $this->option_name;
	}


	/**
	 * Determines the active tab and stores it in the class property.
	 *
	 * @return null|string
	 *
	 * @phpstan-return null|non-empty-string
	 */
	public function get_active_tab(): ?string {

		if ( isset( $this->active_tab ) ) {
			return $this->active_tab;
		}

		if ( empty( $this->tabs ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = sanitize_key( $_REQUEST['tab'] ?? '' );

		if ( empty( $active_tab ) || ! isset( $this->tabs[ $active_tab ] ) ) {
			$active_tab = array_key_first( $this->tabs );
		}

		$this->active_tab = empty( $active_tab ) ? null : (string) $active_tab;

		return $this->active_tab;
	}

	/**
	 * Get option group name.
	 *
	 * @return string
	 * @phpstan-return non-empty-string
	 */
	public function get_option_group_name(): string {
		return $this->option_name . '_group';
	}

	/**
	 * Adds a navigation tab to the settings page.
	 *
	 * This automatically enables the tabbed interface.
	 *
	 * @param string      $id    A unique identifier for the tab.
	 * @param string      $title The visible title of the tab.
	 * @param null|string $icon  (Optional) A Dashicons class for an icon (e.g., 'dashicons-admin-generic').
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param null|non-empty-string $icon
	 */
	public function add_tab( string $id, string $title, ?string $icon = null ): void {
		$id = sanitize_key( $id );

		/** @phpstan-var non-empty-string $id */

		$this->tabs[ $id ] = [
			'title' => $title,
			'icon'  => $icon,
		];
	}

	/**
	 * Adds a settings section to group related fields.
	 *
	 * @param string      $id          A unique identifier for the section.
	 * @param string      $title       The visible title of the section (an `<h2>` tag).
	 * @param null|string $description (Optional) A short description displayed below the section title.
	 * @param null|string $tab_id      (Optional) The ID of the tab this section belongs to. Required for tabbed interfaces.
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param null|non-empty-string $description
	 * @phpstan-param null|non-empty-string $tab_id
	 */
	public function add_section(
		string $id,
		string $title,
		?string $description = null,
		?string $tab_id = null
	): void {
		$id = sanitize_key( $id );

		/** @phpstan-var non-empty-string $id */

		$this->sections[ $id ] = [
			'title'       => $title,
			'description' => $description,
			'tab_id'      => $tab_id,
		];
	}

	/**
	 * Adds a setting field to a section.
	 *
	 * @param string $id         A unique identifier for the field, used as the key in the options array.
	 * @param string $section_id The ID of the section this field belongs to.
	 * @param string $type       The type of field (e.g., 'text', 'toggle', 'select').
	 * @param string $title      The title displayed for the field.
	 * @param array  $extras     (Optional) Additional arguments for the field.
	 *
	 * @phpstan-param non-empty-string $id
	 * @phpstan-param non-empty-string $section_id
	 * @phpstan-param Supported_Field_Type $type
	 * @phpstan-param non-empty-string $title
	 * @phpstan-param array<string, mixed> $extras
	 *
	 * @throws InvalidArgumentException When invalid field type or non-existent section is provided.
	 */
	public function add_field( string $id, string $section_id, string $type, string $title, array $extras = [] ): void {

		$id = sanitize_key( $id );

		/** @phpstan-var non-empty-string $id */

		if ( ! isset( $this->sections[ $section_id ] ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Section "%s" must be added before adding fields to it.',
					$section_id
				)
			);
		}

		$supported_types = $this->field_factory->get_supported_types();

		if ( ! in_array( $type, $supported_types, true ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Unsupported field type: "%s". Supported types are: "%s"',
					$type,
					implode( '", "', $supported_types )
				)
			);
		}

		$field_config = [
			'id'      => $id,
			'name'    => $this->option_name . '[' . $id . ']',
			'section' => $section_id,
			'type'    => $type,
			'title'   => $title,
			'extras'  => $extras,
		];

		$this->fields[ $id ] = $field_config;
	}

	/**
	 * Get registered tabs.
	 *
	 * @return array
	 *
	 * @phpstan-return Tabs_Config
	 */
	public function get_tabs(): array {
		return $this->tabs;
	}

	/**
	 * Check if at least one tab registered.
	 *
	 * @return bool
	 */
	public function has_tabs(): bool {
		return ! empty( $this->tabs );
	}

	/**
	 * Get registered sections.
	 *
	 * @param null|string $tab_id (Optional) The tab ID to retrieve sections for.
	 *
	 * @phpstan-param null|non-empty-string $tab_id
	 *
	 * @return array
	 *
	 * @phpstan-return Sections_Config
	 */
	public function get_sections( ?string $tab_id = null ): array {
		if ( null === $tab_id ) {
			return $this->sections;
		}

		return array_filter(
			$this->sections,
			static fn( $section ) => $section['tab_id'] === $tab_id
		);
	}

	/**
	 * Get registered fields.
	 *
	 * @param null|string $section_id (Optional) The section ID to retrieve fields for.
	 *
	 * @phpstan-param null|non-empty-string $section_id
	 *
	 * @return array
	 *
	 * @phpstan-return Fields_Config
	 */
	public function get_fields( ?string $section_id = null ): array {
		if ( null === $section_id ) {
			return $this->fields;
		}

		return array_filter(
			$this->fields,
			static fn( $field ) => $field['section'] === $section_id
		);
	}

	/**
	 * Get the setting field.
	 *
	 * @param string $field_id The field ID to retrieve.
	 *
	 * @phpstan-param non-empty-string $field_id
	 *
	 * @return null|array
	 *
	 * @phpstan-return null|Field_Config
	 */
	public function get_field( string $field_id ): ?array {
		return $this->fields[ $field_id ] ?? null;
	}
}
