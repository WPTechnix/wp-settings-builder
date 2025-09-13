<?php
/**
 * Concrete implementation of Field_Factory_Interface.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;

/**
 * Class Field_Factory
 *
 * Manages a registry of field types and instantiates them on demand.
 */
final class Field_Factory implements Field_Factory_Interface {

	/**
	 * A map of registered field types to their class names.
	 *
	 * @var array<non-empty-string, class-string<Abstract_Field>>
	 */
	private array $fields = [];

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When invalid/non-existent field type is provided.
	 */
	#[\Override]
	public function create( array $field_config ): Abstract_Field {
		$field_type = $field_config['type'];

		if ( ! isset( $this->fields[ $field_type ] ) ) {
			throw new InvalidArgumentException( sprintf( 'Unsupported field type: "%s".', $field_type ) );
		}

		$class_name = $this->fields[ $field_type ];
		return new $class_name( $field_config );
	}

	/**
	 * Register a field class.
	 *
	 * @param class-string<Abstract_Field> $field_class The fully qualified class name of the field to register.
	 *
	 * @throws InvalidArgumentException If the provided class is not a valid field class.
	 */
	public function register( string $field_class ): void {
		if ( ! is_a( $field_class, Abstract_Field::class, true ) ) {
			throw new InvalidArgumentException( sprintf( 'Invalid field class provided: "%s".', $field_class ) );
		}
		$this->fields[ $field_class::get_type() ] = $field_class;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_registered_fields(): array {
		return $this->fields;
	}
}
