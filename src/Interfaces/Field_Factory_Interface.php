<?php
/**
 * Defines the contract for a service that creates field objects.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

use InvalidArgumentException;

/**
 * Interface Field_Factory_Interface
 *
 * @phpstan-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
interface Field_Factory_Interface {

	/**
	 * Creates a field object instance from its configuration.
	 *
	 * @param array $field_config The configuration array for the field.
	 *
	 * @phpstan-param Field_Config $field_config
	 *
	 * @return Field_Interface The instantiated field object.
	 *
	 * @throws InvalidArgumentException If the field type is not supported or the class is invalid.
	 */
	public function create( array $field_config ): Field_Interface;

	/**
	 * Retrieves a map of all registered field types and their corresponding class names.
	 *
	 * @return array A map of field types to their corresponding class names.
	 *
	 * @phpstan-return array<non-empty-string, class-string<Field_Interface>>
	 */
	public function get_registered_fields(): array;
}
