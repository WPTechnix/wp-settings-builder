<?php
/**
 * Concrete implementation of Sanitizer_Interface.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use Throwable;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Sanitizer_Interface;

/**
 * Class Sanitizer
 *
 * A stateless service responsible for sanitizing raw input data against a page definition.
 *
 * @phpstan-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Sanitizer implements Sanitizer_Interface {

	/**
	 * Class Constructor.
	 *
	 * @param Field_Factory_Interface $field_factory The factory service for creating field objects.
	 */
	public function __construct(
		private Field_Factory_Interface $field_factory
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( array $raw_input, Page_Definition_Interface $definition ): array {
		$sanitized_values  = [];
		$fields_to_process = $this->get_fields_to_process( $definition );
		$option_group      = $definition->get_option_group();

		foreach ( $fields_to_process as $field_id => $field_config ) {
			try {
				if ( 'description' === $field_config['type'] ) {
					continue;
				}

				$raw_value = $raw_input[ $field_id ] ?? null;

				// Handle custom validation callbacks defined in the 'extras' array.
				$validation_callback = $field_config['extras']['validation_callback'] ?? null;
				if (
					is_callable( $validation_callback ) &&
					! $this->run_validation( $validation_callback, $raw_value, $field_config, $option_group )
				) {
					// Validation failed; the error is already set. We skip updating this value.
					continue;
				}

				$field_object                  = $this->field_factory->create( $field_config );
				$sanitized_values[ $field_id ] = $field_object->sanitize( $raw_value );
			} catch ( Throwable $e ) {
				add_settings_error( $option_group, 'field_error_' . $field_id, 'Error processing field "' . $field_config['title'] . '": ' . $e->getMessage() );
			}
		}
		return $sanitized_values;
	}

	/**
	 * Determines which fields should be processed based on the active tab.
	 *
	 * @param Page_Definition_Interface $definition The page definition.
	 *
	 * @return array
	 *
	 * @phpstan-return Fields_Map
	 */
	private function get_fields_to_process( Page_Definition_Interface $definition ): array {
		$tabs = $definition->get_tabs();
		if ( empty( $tabs ) ) {
			return $definition->get_fields();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$active_tab = $this->get_active_tab( $tabs );
		if ( null === $active_tab ) {
			return [];
		}

		$sections_on_tab    = array_filter(
			$definition->get_sections(),
			static fn( array $section ): bool => $section['tab'] === $active_tab
		);
		$section_ids_on_tab = array_keys( $sections_on_tab );

		return array_filter(
			$definition->get_fields(),
			static fn( array $field ): bool => in_array( $field['section'], $section_ids_on_tab, true )
		);
	}

	/**
	 * Determines the current active tab from the request.
	 *
	 * @param array $tabs The map of configured tabs.
	 *
	 * @phpstan-param Tabs_Map $tabs
	 *
	 * @return string|null The active tab ID, or null if no tabs exist.
	 *
	 * @phpstan-return non-empty-string|null
	 */
	private function get_active_tab( array $tabs ): ?string {
		if ( empty( $tabs ) ) {
			return null;
		}

        // phpcs:ignore WordPress.Security.NonceVerification
		$tab_from_request = $_POST['tab'] ?? null;

		$active_tab = is_string( $tab_from_request ) ? sanitize_key( $tab_from_request ) : '';
		if ( ! empty( $active_tab ) && array_key_exists( $active_tab, $tabs ) ) {
			return $active_tab;
		}
		return array_key_first( $tabs );
	}

	/**
	 * Executes a user-provided validation callback and handles errors.
	 *
	 * @param callable $callback The validation callback.
	 * @param mixed    $raw_value The raw input value.
	 * @param array    $field_config The field's configuration.
	 * @param string   $option_group The option group name for add_settings_error.
	 *
	 * @phpstan-param Field_Config $field_config
	 * @phpstan-param non-empty-string $option_group
	 *
	 * @return bool True if validation passes, false otherwise.
	 */
	private function run_validation( callable $callback, mixed $raw_value, array $field_config, string $option_group ): bool {
		try {
			$result = $callback( $raw_value, $field_config );
			if ( true === $result ) {
				return true;
			}
			$error_message = is_string( $result ) && ! empty( $result ) ? $result : sprintf( 'Validation failed for the "%s" field.', $field_config['title'] );

			add_settings_error( $option_group, 'validation_error_' . $field_config['id'], $error_message );
			return false;
		} catch ( Throwable $e ) {
			$error_message = sprintf( 'An error occurred during validation for the "%s" field.', $field_config['title'] );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$error_message .= ' ' . $e->getMessage();
			}
			add_settings_error( $option_group, 'validation_exception_' . $field_config['id'], $error_message );
			return false;
		}
	}
}
