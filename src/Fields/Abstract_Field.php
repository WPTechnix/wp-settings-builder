<?php
/**
 * Provides the basic structure and common functionality for all field types.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Interfaces\Field_Interface;

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
	 * Class Constructor.
	 *
	 * @param array $field_config  The field's config uration properties.
	 *
	 * @phpstan-param Field_Config $field_config
	 */
	public function __construct(
		protected array $field_config,
	) {
	}

	/**
	 * Get the default value for the field.
	 *
	 * @return mixed The default value.
	 */
	public function get_default_value(): mixed {
		return $this->field_config['extras']['default'] ?? null;
	}

	/**
	 * Build an HTML attributes string from an array.
	 *
	 * This helper method constructs a valid HTML attribute string from an
	 * associative array, with proper escaping.
	 *
	 * @param array $attributes The array of attributes (key => value).
	 * @phpstan-param array<non-empty-string, scalar> $attributes
	 *
	 * @return string The generated HTML attributes string.
	 */
	protected function build_attributes_string( array $attributes ): string {
		$attr_parts = [];
		foreach ( $attributes as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attr_parts[] = esc_attr( $key );
				}
			} else {
				$attr_parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
			}
		}

		return implode( ' ', $attr_parts );
	}
}
