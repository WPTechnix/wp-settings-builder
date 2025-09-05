<?php

/**
 * Settings Builder
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Field_Factory;
use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Class Settings_Builder
 *
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Settings_Builder {

	/**
	 * Setting Page Instances
	 *
	 * @var array
	 *
	 * @phpstan-var array<non-empty-string, Settings_Page>
	 */
	private static array $instances = [];

	/**
	 * Factory service for creating field objects.
	 *
	 * @var Field_Factory
	 */
	private Field_Factory $field_factory;

	/**
	 * Asset Registry.
	 *
	 * @var Asset_Loader
	 */
	private Asset_Loader $asset_loader;

	/**
	 * Sanitizer service.
	 *
	 * @var Sanitizer
	 */
	private Sanitizer $sanitizer;

	/**
	 * Renderer service.
	 *
	 * @var Html_Renderer
	 */
	private Html_Renderer $renderer;

	/**
	 * An array of registered ajax actions from field classes.
	 *
	 * @var array
	 *
	 * @phpstan-var array<non-empty-string, callable>
	 */
	private array $ajax_actions = [];

	/**
	 * Default Field classes.
	 *
	 * @var array
	 *
	 * @phpstan-var list<class-string<Abstract_Field>>
	 */
	private array $default_field_classes = [
		Fields\Description_Field::class,
		Fields\Text_Field::class,
		Fields\Textarea_Field::class,
		Fields\Number_Field::class,
		Fields\Url_Field::class,
		Fields\Email_Field::class,
		Fields\Password_Field::class,
		Fields\Color_Field::class,
		Fields\Choice_Field::class,
		Fields\Select_Field::class,
		Fields\Buttons_Group_Field::class,
		Fields\Multi_Select_Field::class,
		Fields\Multi_Check_Field::class,
		Fields\Checkbox_Field::class,
		Fields\Switch_Field::class,
		Fields\Date_Field::class,
		Fields\Time_Field::class,
		Fields\Date_Time_Field::class,
		Fields\Date_Range_Field::class,
		Fields\Date_Time_Range_Field::class,
		Fields\Post_Field::class,
		Fields\Posts_Field::class,
		Fields\User_Field::class,
		Fields\Users_Field::class,
		Fields\Term_Field::class,
		Fields\Terms_Field::class,
		Fields\Media_Field::class,
		Fields\Media_Multiple_Field::class,
		Fields\Wysiwyg_Field::class,
		Fields\Code_Editor_Field::class,
	];

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		$this->field_factory = new Field_Factory();
		$this->asset_loader  = new Asset_Loader( $this->field_factory );
		$this->sanitizer     = new Sanitizer( $this->field_factory );
		$this->renderer      = new Html_Renderer( $this->field_factory );

		foreach ( $this->default_field_classes as $field_class ) {
			$this->register_field_class( $field_class );
		}
	}

	/**
	 * Registers a custom field class with the framework.
	 *
	 * This method allows developers to add their own custom field types. The provided
	 * class must extend `Abstract_Field`.
	 *
	 * @param string $field_class The fully qualified class name of the custom field.
	 *
	 * @phpstan-param class-string<Abstract_Field> $field_class
	 *
	 * @return self
	 */
	public function register_field_class( string $field_class ): self {
		$this->field_factory->register( $field_class );

		// Discover the default assets from the class itself.
		$default_assets = $field_class::get_asset_definitions();
		$ajax_actions   = $field_class::get_ajax_actions();

		foreach ( $ajax_actions as $action => $handler ) {
			$callback = [ $field_class, $handler ];
			if ( ! isset( $this->ajax_actions[ $action ] ) && is_callable( $callback ) ) {
				$this->ajax_actions[ $action ] = $callback;
			}
		}

		foreach ( $default_assets as $asset ) {
			$this->asset_loader::add_registry( $asset );
		}

		return $this;
	}

	/**
	 * Register script or a style.
	 *
	 * @param string       $handle   The unique handle for the asset.
	 * @param string       $type     The type of asset: 'css' or 'js'.
	 * @param string       $src      The path of the asset.
	 * @param string[]     $deps     An array of dependencies.
	 * @param string|false $version  Version (Optional).
	 *
	 * @phpstan-param non-empty-string $handle
	 * @phpstan-param 'css'|'js' $type
	 * @phpstan-param non-empty-string $src
	 * @phpstan-param list<non-empty-string> $deps
	 * @phpstan-param non-empty-string|false $version
	 *
	 * @return self
	 */
	public function register_asset(
		string $handle,
		string $type,
		string $src,
		array $deps = [],
		string|false $version = false,
	): self {

		$this->asset_loader::add_registry(
			[
				'handle'  => $handle,
				'type'    => $type,
				'src'     => $src,
				'deps'    => $deps,
				'version' => $version,
			],
			true
		);

		return $this;
	}

	/**
	 * Get instance of a setting page by slug.
	 *
	 * @param string $page_slug The page slug. (Optional).
	 *
	 * @phpstan-param non-empty-string $page_slug
	 */
	public static function get_instance( ?string $page_slug = null ): ?Settings_Page {
		if ( null === $page_slug ) {
			$page_slug = array_key_first( self::$instances );
		}
		if ( empty( $page_slug ) ) {
			return null;
		}
		return self::$instances[ $page_slug ] ?? null;
	}

	/**
	 * Create a settings page.
	 *
	 * @param string $option_name Option name.
	 * @param string $page_slug Page slug.
	 *
	 * @phpstan-param non-empty-string $option_name
	 * @phpstan-param non-empty-string $page_slug
	 */
	public function create( string $option_name, string $page_slug ): Settings_Page {

		$persistence = new Wp_Options_Persistence( $option_name );

		$instance                      = new Settings_Page(
			$this->field_factory,
			$persistence,
			$this->sanitizer,
			$this->renderer,
			$this->asset_loader,
			$this->ajax_actions,
			$option_name,
			$page_slug
		);
		self::$instances[ $page_slug ] = $instance;
		return $instance;
	}
}
