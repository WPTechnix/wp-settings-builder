<?php
/**
 * A fluent builder for creating and managing WordPress admin settings pages.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Interfaces\Settings_Interface;

/**
 * A fluent builder for creating and managing WordPress admin settings pages.
 *
 * @phpstan-import-type Field_Config from Field_Factory
 *
 * @phpstan-type Section_Config array{
 *   title: non-empty-string,
 *   description: null|non-empty-string,
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
final class Settings implements Settings_Interface {

	/**
	 * Instance of the factory responsible for creating field objects.
	 *
	 * @var Field_Factory
	 */
	private Field_Factory $field_factory;

	/**
	 * Store for retrieving settings data.
	 *
	 * @var Settings_Store
	 */
	private Settings_Store $settings_store;

	/**
	 * Instance of the asset manager responsible for enqueuing scripts and styles.
	 *
	 * @var Asset_Manager
	 */
	private Asset_Manager $asset_manager;

	/**
	 * Instance of the page renderer responsible for rendering the settings page.
	 *
	 * @var Page_Renderer
	 */
	private Page_Renderer $page_renderer;

	/**
	 * Sanitizer instance.
	 *
	 * @var Sanitizer
	 */
	private Sanitizer $sanitizer;

	/**
	 * Page Title
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $page_title;

	/**
	 * Menu Title
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $menu_title;

	/**
	 * Capability required to access the settings page.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $capability = 'manage_options';

	/**
	 * Parent slug for the submenu page.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $parent_slug = 'options-general.php';

	/**
	 * Active tab ID.
	 *
	 * @var null|string
	 *
	 * @phpstan-var null|non-empty-string
	 */
	private null|string $active_tab = null;

	/**
	 * The HTML prefix that will be used in fields markup and generated CSS/JS.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $html_prefix = 'wptechnix-settings';

	/**
	 * Query argument for tab selection.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $tab_query_arg = 'tab';

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
	 * Settings constructor.
	 *
	 * Initializes the settings framework with essential parameters and default configurations.
	 *
	 * @param string $option_name The name of the option to be stored in the wp_options table.
	 * @param string $page_slug   The unique slug for the settings page URL.
	 *
	 * @phpstan-param non-empty-string $option_name
	 * @phpstan-param non-empty-string $page_slug
	 *
	 * @throws InvalidArgumentException If option_name or page_slug are empty.
	 */
	public function __construct(
		private string $option_name,
		private string $page_slug
	) {
		if ( empty( $this->option_name ) ) {
			throw new InvalidArgumentException( 'Option name cannot be empty.' );
		}

		if ( empty( $this->page_slug ) ) {
			throw new InvalidArgumentException( 'Page slug cannot be empty.' );
		}

		$default_page_title = __( 'Settings', 'default' );

		/** @phpstan-var non-empty-string $default_page_title */

		$this->page_title = $default_page_title;
		$this->menu_title = $this->page_title;

		$this->determine_active_tab();

		$this->settings_store = new Settings_Store( $this->option_name );
		$this->field_factory  = new Field_Factory();
		$this->asset_manager  = new Asset_Manager( $this->html_prefix );
		$this->page_renderer  = new Page_Renderer( $this->active_tab );
		$this->sanitizer      = new Sanitizer( $this->active_tab );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the page title.
	 *
	 * @param string $page_title The page title.
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
	 * Sets the menu title.
	 *
	 * @param string $menu_title The menu title.
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
	 * Sets the capability required to access the settings page.
	 *
	 * @param string $capability The capability.
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
	 * Sets the parent slug for the submenu page.
	 *
	 * @param string $parent_slug The parent slug.
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
	 * Sets the HTML prefix for the settings page.
	 *
	 * @param string $html_prefix The HTML prefix.
	 *
	 * @phpstan-param non-empty-string $html_prefix
	 *
	 * @return self
	 */
	public function set_html_prefix( string $html_prefix ): self {
		$this->html_prefix = $html_prefix;
		return $this;
	}

	/**
	 * Sets the query argument for tab selection.
	 *
	 * @param string $tab_query_arg The query argument.
	 * @phpstan-param non-empty-string $tab_query_arg
	 * @return self
	 */
	public function set_tab_query_arg( string $tab_query_arg ): self {
		$this->tab_query_arg = $tab_query_arg;
		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Core Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Initializes the settings page by hooking into WordPress actions.
	 */
	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this->asset_manager, 'enqueue' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_tab( string $id, string $title, ?string $icon = null ): self {
		$id = sanitize_key( $id );

		/** @phpstan-var non-empty-string $id */

		$this->tabs[ $id ] = [
			'title' => $title,
			'icon'  => $icon,
		];

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_section(
		string $id,
		string $title,
		?string $description = null,
		?string $tab_id = null
	): self {
		$id = sanitize_key( $id );

		/** @phpstan-var non-empty-string $id */

		$this->sections[ $id ] = [
			'title'       => $title,
			'description' => $description,
			'tab_id'      => $tab_id,
		];

		return $this;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When unsupported field type or non-existing section is provided
	 */
	public function add_field(
		string $id,
		string $section_id,
		string $type,
		string $title,
		array $extras = []
	): self {

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

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get( string $key, mixed $default_value = null ): mixed {
		return $this->settings_store->get( $key, $default_value );
	}

	/**
	 * Registers the settings page with the WordPress admin menu.
	 *
	 * @internal This method is a callback for the 'admin_menu' hook and should not be called directly.
	 */
	public function register_page(): void {
		add_submenu_page(
			$this->parent_slug,
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->page_slug,
			[ $this->page_renderer, 'render_page' ],
			null
		);
	}

	/**
	 * Registers the settings, sections, and fields with the WordPress Settings API.
	 *
	 * @internal This method is a callback for the 'admin_init' hook and should not be called directly.
	 */
	public function register_settings(): void {

		register_setting(
			$this->option_name,
			$this->option_name,
			[ 'sanitize_callback' => [ $this->sanitizer, 'sanitize' ] ]
		);

		foreach ( $this->sections as $id => $section ) {
			add_settings_section(
				$id,
				$section['title'],
				! empty( $section['description'] )
					? function () use ( $section ) {
						echo '<p class="description">' . esc_html( $section['description'] ) . '</p>';
					}
					: '__return_false',
				$this->page_slug
			);
		}

		foreach ( $this->fields as $id => $field ) {
			$args = $field;

			if ( in_array( $field['type'], $this->field_factory->get_text_types(), true ) ) {
				$args['label_for'] = $id;
			}

			add_settings_field(
				$id,
				$field['title'],
				[ $this->page_renderer, 'render_field' ],
				$this->page_slug,
				$field['section'],
				$args
			);
		}
	}

	/**
	 * Determines the active tab and stores it in the class property.
	 */
	private function determine_active_tab(): void {
		if ( empty( $this->tabs ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = sanitize_key( $_REQUEST[ $this->tab_query_arg ] ?? '' );

		if ( empty( $active_tab ) || ! isset( $this->tabs[ $active_tab ] ) ) {
			$active_tab = (string) array_key_first( $this->tabs );
		}

		$this->active_tab = $active_tab;
	}
}
