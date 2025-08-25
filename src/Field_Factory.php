<?php
/**
 * Factory for creating field objects based on their type.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Abstract_Field;

/**
 * Creates instances of field objects based on their type.
 *
 * @phpstan-type Text_Field_Type 'text'
 * @phpstan-type Supported_Field_Type Text_Field_Type
 *
 * @phpstan-import-type Field_Config from Abstract_Field
 */
final class Field_Factory {

	/**
	 * A map of field types to their corresponding class names.
	 *
	 * @var array
	 *
	 * @phpstan-var array<Supported_Field_Type, class-string<Abstract_Field>>
	 */
	private array $fields;

	/**
	 * Class Constructor.
	 *
	 * Initializes the map of supported field types. This can be extended
	 * programmatically if needed.
	 */
	public function __construct() {
		$this->fields = [
			// TODO: add fields.
		];
	}

	/**
	 * Get the list of all supported field type keys.
	 *
	 * @return string[] An array of supported type identifiers.
	 *
	 * @phpstan-return Supported_Field_Type[]
	 */
	public function get_supported_types(): array {
		return array_keys( $this->fields );
	}

	/**
	 * Get text-based field types.
	 *
	 * @return string[] An array of inline field type identifiers.
	 *
	 * @phpstan-return Text_Field_Type[]
	 */
	public function get_text_types(): array {
		return [];
	}

	/**
	 * Creates a field object based on its type.
	 *
	 * @param string $type         The field type identifier (e.g., 'text', 'toggle').
	 * @param array  $field_config The configuration for the field.
	 *
	 * @phpstan-param Field_Config $field_config
	 *
	 * @return Abstract_Field The instantiated field object.
	 *
	 * @throws InvalidArgumentException If the requested field type is not supported.
	 */
	public function create( string $type, array $field_config ): Abstract_Field {
		if ( ! isset( $this->fields[ $type ] ) ) {
			throw new InvalidArgumentException( sprintf( 'Unsupported field type: "%s".', $type ) );
		}

		$class_name = $this->fields[ $type ];
		if ( ! class_exists( $class_name ) ) {
			throw new InvalidArgumentException( sprintf( 'Invalid field class "%s" for type "%s".', $class_name, $type ) );
		}

		return new $class_name( $field_config );
	}
}
