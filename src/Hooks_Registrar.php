<?php
/**
 * The internal engine that bridges the pure services with the WordPress hook system.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Persistence_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Renderer_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Sanitizer_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Asset_Loader_Interface;

/**
 * Class Hooks_Registrar
 *
 * This class is the only part of the library that is directly aware of WordPress actions
 * and filters.
 */
final class Hooks_Registrar {

	/**
	 * The unique hook suffix returned by add_submenu_page().
	 *
	 * @var string|false
	 */
	private string|false $page_hook_suffix = false;

	/**
	 * Class Constructor.
	 *
	 * @param Page_Definition_Interface $definition The definition contract for the page.
	 * @param Persistence_Interface     $persistence The service for data storage.
	 * @param Renderer_Interface        $renderer The service for HTML rendering.
	 * @param Sanitizer_Interface       $sanitizer The service for data sanitization.
	 * @param Asset_Loader_Interface    $asset_loader Asset Loader.
	 * @param array<string,callable>    $ajax_actions A map of AJAX action callbacks, keyed by action name.
	 */
	public function __construct(
		private Page_Definition_Interface $definition,
		private Persistence_Interface $persistence,
		private Renderer_Interface $renderer,
		private Sanitizer_Interface $sanitizer,
		private Asset_Loader_Interface $asset_loader,
		private array $ajax_actions
	) {
	}

	/**
	 * Registers all necessary WordPress hooks.
	 */
	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_admin_page' ], 10, 0 );
		add_action( 'admin_init', [ $this, 'register_settings_api' ], 10, 0 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		foreach ( $this->ajax_actions as $action => $callback ) {
			/** @psalm-suppress  HookNotFound */
			add_action( "wp_ajax_wptx_{$action}", $callback );
		}
	}

	/**
	 * Registers the admin page with WordPress.
	 *
	 * @internal
	 */
	public function register_admin_page(): void {
		$this->page_hook_suffix = add_submenu_page(
			$this->definition->get_parent_slug(),
			$this->definition->get_page_title(),
			$this->definition->get_menu_title(),
			$this->definition->get_capability(),
			$this->definition->get_page_slug(),
			[ $this, 'render_page_callback' ]
		);
	}

	/**
	 * Registers settings, sections, and fields with the WordPress Settings API.
	 *
	 * @internal
	 */
	public function register_settings_api(): void {
		register_setting(
			$this->definition->get_option_group(),
			$this->definition->get_option_name(),
			[ 'sanitize_callback' => [ $this, 'sanitize_callback' ] ]
		);

		foreach ( $this->definition->get_sections() as $section_id => $section ) {
			add_settings_section(
				$section_id,
				$section['title'],
				null !== $section['description']
				? static function () use ( $section ) {
					echo '<div>' . wp_kses_post( $section['description'] ) . '</div>';
				}
				: '__return_null',
				$this->definition->get_page_slug()
			);
		}

		$current_values = $this->persistence->load();

		foreach ( $this->definition->get_fields() as $field_id => $field_config ) {
			// Add the current value to the field's extras before passing it to the renderer.
			$field_config['extras']['value'] = $current_values[ $field_id ] ?? null;

			/** @psalm-suppress InvalidArgument */
			add_settings_field(
				$field_id,
				$field_config['title'],
				[ $this->renderer, 'render_field' ],
				$this->definition->get_page_slug(),
				$field_config['section'],
				[ 'config' => $field_config ]
			);
		}
	}

	/**
	 * Top-level callback for rendering the entire page. It delegates to the renderer service.
	 *
	 * @internal
	 */
	public function render_page_callback(): void {
		$this->renderer->render_page( $this->definition );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook informs the current admin page.
	 */
	public function enqueue_assets( string $hook ): void {
		// Bail if we’re not on our settings page.
		if ( $hook !== $this->page_hook_suffix ) {
			return;
		}

		$this->asset_loader->enqueue( $this->definition );
	}

	/**
	 * Top-level callback for sanitizing settings. It delegates to the sanitizer service.
	 *
	 * @param mixed $input The raw input from the form submission.
	 *
	 * @return array<array-key,mixed> The sanitized settings array.
	 *
	 * @internal
	 */
	public function sanitize_callback( mixed $input ): array {
		$old_values = $this->persistence->load();
		$new_values = $this->sanitizer->sanitize( is_array( $input ) ? $input : [], $this->definition );

		return array_merge( $old_values, $new_values );
	}
}
