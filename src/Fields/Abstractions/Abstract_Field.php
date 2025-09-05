<?php
/**
 * Provides the basic structure and common functionality for all field types.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Abstractions
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Abstractions;

use WPTechnix\WP_Settings_Builder\Interfaces\Field_Interface;
use InvalidArgumentException;

/**
 * Abstract field class to provide basic structure and common functionality.
 *
 * @phpstan-type Field_Config array{
 *   id: non-empty-string,
 *   title: non-empty-string,
 *   section: non-empty-string,
 *   name: non-empty-string,
 *   type: non-empty-string,
 *   extras: array<string,mixed>
 * }
 */
abstract class Abstract_Field implements Field_Interface {

	/**
	 * Field type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type;

	/**
	 * CSS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $css_handles = [];

	/**
	 * JavaScript handles to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $js_handles = [];

	/**
	 * Class Constructor.
	 *
	 * @param array $field_config  The field's config uration properties.
	 *
	 * @phpstan-param Field_Config $field_config
	 */
	final public function __construct(
		protected array $field_config,
	) {
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_type(): string {
		return static::$type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id(): string {
		return $this->field_config['id'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_name(): string {
		return $this->field_config['name'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title(): string {
		return $this->field_config['title'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_section(): string {
		return $this->field_config['section'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_extra( string $key, mixed $default_value = null ): mixed {
		return $this->field_config['extras'][ $key ] ?? $default_value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_description(): string {
		$description = $this->get_extra( 'description', '' );
		return is_string( $description ) ? $description : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): mixed {
		return $this->get_extra( 'default', null );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): mixed {
		return $this->get_extra( 'value', $this->get_default_value() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_use_inline_title_as_label(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_render_description_below(): bool {
		return ! empty( $this->get_description() );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_asset_definitions(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_enqueued_styles(): array {
		return static::$css_handles;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_enqueued_scripts(): array {
		return static::$js_handles;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_css_contents(): string {
		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_js_contents(): string {
		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_ajax_actions(): array {
		return [];
	}

	/**
	 * Build an HTML attributes string from an array.
	 *
	 * This helper method constructs a valid HTML attribute string from an
	 * associative array, with proper escaping.
	 *
	 * @param array $attributes The array of attributes (key => value).
	 *
	 * @phpstan-param array<non-empty-string, scalar> $attributes
	 *
	 * @return string The generated HTML attributes string.
	 *
	 * @throws InvalidArgumentException When an attribute value is not a string, numeric or a boolean.
	 */
	protected function build_attributes_string( array $attributes ): string {
		$attr_parts = [];
		foreach ( $attributes as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attr_parts[] = esc_attr( $key );
				}
			} elseif ( is_scalar( $value ) ) {
				$value        = (string) $value;
				$attr_parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
			} else {
				throw new InvalidArgumentException(
					"HTML attribute '$key' must be a string, numeric or a boolean value."
				);
			}
		}

		return implode( ' ', $attr_parts );
	}

	/**
	 * Get field extra HTML attributes.
	 *
	 * @return array
	 *
	 * @phpstan-return array<non-empty-string, scalar>
	 */
	protected function get_extra_html_attributes(): array {
		$field_attributes = $this->get_extra( 'html_attributes', [] );
		return is_array( $field_attributes ) ? $field_attributes : [];
	}

	/**
	 * Get field extra HTML attributes as string.
	 *
	 * @param array $default_attributes Additional attributes.
	 *
	 * @phpstan-param array<non-empty-string, scalar> $default_attributes
	 *
	 * @return string
	 */
	protected function get_extra_html_attributes_string( array $default_attributes = [] ): string {
		return $this->build_attributes_string(
			array_merge( $default_attributes, $this->get_extra_html_attributes() )
		);
	}
}
