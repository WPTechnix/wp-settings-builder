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
 * @phpstan-import-type Supported_Field_Type from Field_Factory
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

		$this->field_factory  = new Field_Factory();
		$this->settings_store = new Settings_Store( $this->field_factory, $this->option_name, $this->page_slug );
		$this->asset_manager  = new Asset_Manager();
		$this->page_renderer  = new Page_Renderer( $this->settings_store );
		$this->sanitizer      = new Sanitizer( $this->settings_store );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * {@inheritDoc}
	 */
	public function set_page_title( string $page_title ): self {
		$this->page_title = $page_title;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_menu_title( string $menu_title ): self {
		$this->menu_title = $menu_title;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_capability( string $capability ): self {
		$this->capability = $capability;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_parent_slug( string $parent_slug ): self {
		$this->parent_slug = $parent_slug;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_html_prefix( string $html_prefix ): self {
		$this->asset_manager->set_html_prefix( $html_prefix );
		$this->page_renderer->set_html_prefix( $html_prefix );
		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Fluent Configuration Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * {@inheritDoc}
	 */
	public function add_tab( string $id, string $title, ?string $icon = null ): self {
		$this->settings_store->add_tab( $id, $title, $icon );
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_section( string $id, string $title, ?string $description = null, ?string $tab_id = null ): self {
		$this->settings_store->add_section( $id, $title, $description, $tab_id );
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_field( string $id, string $section_id, string $type, string $title, array $extras = [] ): self {
		$this->settings_store->add_field( $id, $section_id, $type, $title, $extras );
		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Core Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * {@inheritDoc}
	 */
	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this->asset_manager, 'enqueue' ] );
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
			[ $this->page_renderer, 'render_page' ]
		);
	}

	/**
	 * Registers the settings, sections, and fields with the WordPress Settings API.
	 *
	 * @internal This method is a callback for the 'admin_init' hook and should not be called directly.
	 */
	public function register_settings(): void {
		register_setting(
			$this->settings_store->get_option_group_name(),
			$this->option_name,
			[ 'sanitize_callback' => [ $this->sanitizer, 'sanitize' ] ]
		);

		foreach ( $this->settings_store->get_sections() as $id => $section ) {
			add_settings_section(
				$id,
				$section['title'],
				! empty( $section['description'] )
					? static function () use ( $section ) {
						echo '<p class="description">' . esc_html( $section['description'] ) . '</p>';
					}
					: '__return_false',
				$this->page_slug
			);
		}

		foreach ( $this->settings_store->get_fields() as $id => $field ) {
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
}
