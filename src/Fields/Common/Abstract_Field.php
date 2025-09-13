<?php
/**
 * Provides the basic structure and common functionality for all field types.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Common
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Common;

use WPTechnix\WP_Settings_Builder\Interfaces\Field_Interface;
use InvalidArgumentException;

/**
 * Abstract field class to provide basic structure and common functionality.
 *
 * @phpstan-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
abstract class Abstract_Field implements Field_Interface {

	/**
	 * Field type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type;

	/**
	 * CSS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $css_handles = [];

	/**
	 * JavaScript handles to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $js_handles = [];

	/**
	 * Class Constructor.
	 *
	 * @param array $field_config  The field's config array properties.
	 * @phpstan-param Field_Config $field_config
	 * @psalm-param Field_Config $field_config
	 */
	final public function __construct(
		protected array $field_config,
	) {
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_type(): string {
		return static::$type;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_id(): string {
		return $this->field_config['id'];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_name(): string {
		return $this->field_config['name'];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_title(): string {
		return $this->field_config['title'];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_section(): string {
		return $this->field_config['section'];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_extra( string $key, mixed $default_value = null ): mixed {
		return $this->field_config['extras'][ $key ] ?? $default_value;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_description(): string {
		$description = $this->get_extra( 'description', '' );
		return is_string( $description ) ? $description : '';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_default_value(): mixed {
		return $this->get_extra( 'default' );
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_value(): mixed {
		return $this->get_extra( 'value', $this->get_default_value() );
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_render_description_below(): bool {
		return '' !== $this->get_description();
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_asset_definitions(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_enqueued_styles(): array {
		return static::$css_handles;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_enqueued_scripts(): array {
		return static::$js_handles;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_css_contents(): string {
		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_js_contents(): string {
		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_ajax_actions(): array {
		return [];
	}

	/**
	 * Build an HTML attributes string from an array.
	 *
	 * This helper method constructs a valid HTML attribute string from an
	 * associative array, with proper escaping.
	 *
	 * @param array<array-key, mixed> $attributes The array of attributes (key => value).
	 *
	 * @return string The generated HTML attributes string.
	 *
	 * @throws InvalidArgumentException When an attribute value is not a string, numeric or a boolean.
	 */
	protected function build_attributes_string( array $attributes ): string {
		$attr_parts = [];
		foreach ( $attributes as $key => $value ) {
			$key = (string) $key;
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
	 * @return array<array-key,mixed>
	 */
	protected function get_extra_html_attributes(): array {
		$field_attributes = $this->get_extra( 'html_attributes', [] );
		return is_array( $field_attributes ) ? $field_attributes : [];
	}

	/**
	 * Get field extra HTML attributes as string.
	 *
	 * @param array<array-key,mixed> $default_attributes Additional attributes.
	 *
	 * @return string
	 */
	protected function get_extra_html_attributes_string( array $default_attributes = [] ): string {
		return $this->build_attributes_string(
			array_merge( $default_attributes, $this->get_extra_html_attributes() )
		);
	}
}
