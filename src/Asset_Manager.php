<?php
/**
 * Manages the enqueueing of static assets for the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Asset_Manager_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;

/**
 * Class Asset_Manager
 *
 * Handles the registration and conditional enqueueing of all assets. It discovers
 * default assets from field types and allows for developer overrides.
 *
 * @phpstan-import-type Style_Extra from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Script_Extra from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Asset_Manager implements Asset_Manager_Interface {

	/**
	 * The handle for the main JavaScript file.
	 *
	 * @var string
	 */
	private const MAIN_JS_HANDLE = 'wp-settings-builder-main-js';

	/**
	 * The handle for the main CSS file.
	 *
	 * @var string
	 */
	private const MAIN_CSS_HANDLE = 'wp-settings-builder-main-css';

	/**
	 * A flag to ensure assets are only registered once per request.
	 *
	 * @var bool
	 */
	private bool $assets_registered = false;

	/**
	 * The final, merged registry of all known asset definitions.
	 *
	 * @var array
	 * @phpstan-var array<non-empty-string, Asset>
	 */
	private array $final_asset_registry = [];

	/**
	 * Class constructor.
	 *
	 * @param Field_Factory_Interface $field_factory      The factory service for creating field objects.
	 * @param array                   $registered_assets  A list of assets registered by the developer (overrides).
	 *
	 * @phpstan-param array<non-empty-string, Asset> $registered_assets
	 */
	public function __construct(
		private Field_Factory_Interface $field_factory,
		private array $registered_assets
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function enqueue( Page_Definition_Interface $definition ): void {
		if ( ! $this->assets_registered ) {
			$this->build_and_register_final_registry();
			$this->assets_registered = true;
		}

		$active_field_types = array_unique( array_column( $definition->get_fields(), 'type' ) );

		if ( empty( $active_field_types ) ) {
			return;
		}

		/** @phpstan-var list<non-empty-string> $active_field_types */

		$this->enqueue_page_assets( $active_field_types );
	}

	/**
	 * Enqueues all necessary assets for the current settings page.
	 *
	 * @param array $active_field_types A list of unique field types active on the page.
	 *
	 * @phpstan-param list<non-empty-string> $active_field_types
	 */
	private function enqueue_page_assets( array $active_field_types ): void {
		$script_handles = [];
		$style_handles  = [];
		$inline_css     = $this->get_default_inline_css();
		$inline_js      = $this->get_default_inline_js();

		foreach ( $this->get_active_field_classes( $active_field_types ) as $class_name ) {
			$asset_handles = array_keys( $class_name::get_asset_definitions() );

			foreach ( $asset_handles as $handle ) {
				if ( ! isset( $this->final_asset_registry[ $handle ] ) ) {
					continue;
				}

				$asset_type = $this->final_asset_registry[ $handle ]['type'];
				if ( 'js' === $asset_type ) {
					$script_handles[] = $handle;
				} elseif ( 'css' === $asset_type ) {
					$style_handles[] = $handle;
				}
			}

			$inline_css .= $class_name::get_css_contents();
			$inline_js  .= $class_name::get_js_contents();
		}

		if ( empty( $script_handles ) && empty( $style_handles ) && empty( $inline_css ) && empty( $inline_js ) ) {
			return;
		}

		$this->enqueue_assets( $script_handles, $style_handles, $inline_js, $inline_css );
	}

	/**
	 * Handles the actual enqueuing of scripts and styles.
	 *
	 * @param array  $script_handles The script handles to enqueue.
	 * @param array  $style_handles The style handles to enqueue.
	 * @param string $inline_js The inline JavaScript to add.
	 * @param string $inline_css The inline CSS to add.
	 *
	 * @phpstan-param list<non-empty-string> $script_handles
	 * @phpstan-param list<non-empty-string> $style_handles
	 */
	private function enqueue_assets( array $script_handles, array $style_handles, string $inline_js, string $inline_css ): void {
		wp_enqueue_script( self::MAIN_JS_HANDLE );
		if ( ! empty( $inline_js ) ) {
			wp_add_inline_script( self::MAIN_JS_HANDLE, $inline_js, 'after' );
		}

		wp_enqueue_style( self::MAIN_CSS_HANDLE );
		if ( ! empty( $inline_css ) ) {
			wp_add_inline_style( self::MAIN_CSS_HANDLE, $inline_css );
		}

		foreach ( array_unique( $script_handles ) as $handle ) {
			wp_enqueue_script( $handle );
		}

		foreach ( array_unique( $style_handles ) as $handle ) {
			wp_enqueue_style( $handle );
		}
	}

	/**
	 * Builds the final asset registry and registers everything with WordPress.
	 */
	private function build_and_register_final_registry(): void {
		$default_assets             = $this->get_all_default_field_assets();
		$this->final_asset_registry = array_merge( $default_assets, $this->registered_assets );

		foreach ( $this->final_asset_registry as $handle => $config ) {
			if ( 'js' === $config['type'] ) {
				$this->register_script( $handle, $config );
			} elseif ( 'css' === $config['type'] ) {
				$this->register_style( $handle, $config );
			}
		}

		$this->register_main_assets();
	}

	/**
	 * Registers a single script with WordPress.
	 *
	 * @param string $handle The script handle.
	 * @param array  $config The asset configuration.
	 *
	 * @phpstan-param non-empty-string $handle
	 * @phpstan-param Asset $config
	 */
	private function register_script( string $handle, array $config ): void {
		$extra = $config['extra'] ?? true;
		/** @phpstan-var Script_Extra $extra */
		wp_register_script( $handle, $config['src'], $config['deps'] ?? [], $config['ver'] ?? null, $extra );
	}

	/**
	 * Registers a single style with WordPress.
	 *
	 * @param string $handle The style handle.
	 * @param array  $config The asset configuration.
	 *
	 * @phpstan-param non-empty-string $handle
	 * @phpstan-param Asset $config
	 */
	private function register_style( string $handle, array $config ): void {
		$extra = $config['extra'] ?? 'all';
		/** @phpstan-var Style_Extra $extra */
		wp_register_style( $handle, $config['src'], $config['deps'] ?? [], $config['ver'] ?? null, $extra );
	}

	/**
	 * Registers the main placeholder assets for inline content.
	 */
	private function register_main_assets(): void {
		wp_register_script(
			self::MAIN_JS_HANDLE,
			'',
			[ 'jquery', 'wp-hooks' ],
			null, // phpcs:ignore
			true
		);

		wp_register_style(
			self::MAIN_CSS_HANDLE,
			'',
			[ 'wp-admin' ],
			null // phpcs:ignore
		);
	}

	/**
	 * Gathers asset definitions from all available field types.
	 *
	 * @return array
	 * @phpstan-return array<non-empty-string, Asset>
	 */
	private function get_all_default_field_assets(): array {
		$all_assets    = [];
		$field_classes = $this->field_factory->get_registered_fields();

		foreach ( $field_classes as $class_name ) {
			$assets     = $class_name::get_asset_definitions();
			$all_assets = array_merge( $all_assets, $assets );
		}

		return $all_assets;
	}

	/**
	 * Gets the fully qualified class names for a list of field type identifiers.
	 *
	 * @param array $field_types A list of field type identifiers.
	 *
	 * @phpstan-param list<non-empty-string> $field_types
	 *
	 * @return array
	 *
	 * @phpstan-return list<class-string<Field_Interface>>
	 */
	private function get_active_field_classes( array $field_types ): array {
		$registered_fields = $this->field_factory->get_registered_fields();
		$active_classes    = [];

		foreach ( $field_types as $type ) {
			if ( isset( $registered_fields[ $type ] ) ) {
				$active_classes[] = $registered_fields[ $type ];
			}
		}

		return $active_classes;
	}

	/**
	 * Returns the default inline JS.
	 *
	 * @return string
	 */
	private function get_default_inline_js(): string {
		return '';
	}

	/**
	 * Returns the default inline CSS.
	 *
	 * @return string
	 */
	private function get_default_inline_css(): string {
		return '';
	}
}
