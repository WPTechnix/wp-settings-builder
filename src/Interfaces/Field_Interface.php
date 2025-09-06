<?php
/**
 * Defines the contract for all field type objects.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Field_Interface
 *
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
interface Field_Interface {


	/**
	 * Get the unique type identifier for this field class.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public static function get_type(): string;

	/**
	 * Get the field's unique ID.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_id(): string;

	/**
	 * Get the field's HTML name attribute.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_name(): string;

	/**
	 * Get the field title.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_title(): string;

	/**
	 * Get the section ID the field belongs to.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_section(): string;

	/**
	 * Get a value from the field's extra data.
	 *
	 * @param string $key           The array key to retrieve.
	 * @param mixed  $default_value The default value if the key is not found.
	 *
	 * @phpstan-param non-empty-string $key
	 *
	 * @return mixed
	 */
	public function get_extra( string $key, mixed $default_value = null ): mixed;

	/**
	 * Get field description
	 *
	 * @return string
	 */
	public function get_description(): string;

	/**
	 * Get the default value of the field.
	 *
	 * @return mixed
	 */
	public function get_default_value(): mixed;

	/**
	 * Get the stored value of the field.
	 *
	 * @return mixed
	 */
	public function get_value(): mixed;

	/**
	 * Sanitize the raw input value for this field.
	 *
	 * @param mixed $value The raw input value from the form submission.
	 *
	 * @return mixed The sanitized value ready for persistence.
	 */
	public function sanitize( mixed $value ): mixed;

	/**
	 * Should use inline title as the field label?
	 *
	 * @return bool
	 */
	public function should_use_inline_title_as_label(): bool;

	/**
	 * Should the description be rendered below the field?
	 *
	 * @return bool
	 */
	public function should_render_description_below(): bool;

	/**
	 * Render the field's HTML markup to standard output.
	 *
	 * @throws \InvalidArgumentException When invalid HTML attributes provided.
	 */
	public function render(): void;

	/**
	 * Get the asset definitions for this field.
	 *
	 * @return array
	 *
	 * @phpstan-return list<Asset>
	 */
	public static function get_asset_definitions(): array;

	/**
	 * Get enqueued styles for this field.
	 *
	 * @return array
	 *
	 * @phpstan-return list<non-empty-string>
	 */
	public static function get_enqueued_styles(): array;

	/**
	 * Get enqueued scripts for this field.
	 *
	 * @return array
	 *
	 * @phpstan-return list<non-empty-string>
	 */
	public static function get_enqueued_scripts(): array;

	/**
	 * Get the CSS contents.
	 *
	 * @return string
	 */
	public static function get_css_contents(): string;

	/**
	 * Get the JS contents.
	 *
	 * @return string
	 */
	public static function get_js_contents(): string;


	/**
	 * Get AJAX actions handled by this field.
	 *
	 * The key is the AJAX action name (without the 'wp_ajax_' prefix),
	 * and the value is the static method name in the class to handle it.
	 *
	 * @return array
	 *
	 * @phpstan-return array<non-empty-string, non-empty-string>
	 */
	public static function get_ajax_actions(): array;
}
