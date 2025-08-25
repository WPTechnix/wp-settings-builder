<?php
/**
 * Sanitizes settings before they are saved to the database.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use InvalidArgumentException;
use Throwable;

/**
 * Sanitizes settings before they are saved to the database.
 *
 * This class is state-aware. When sanitizing a form submission (especially
 * from a tabbed interface), it merges the newly submitted values with the
 * existing saved values to prevent data loss from other tabs.
 *
 * @phpstan-import-type Field_Config from Settings_Store
 */
final class Sanitizer {

	/**
	 * Class Constructor
	 *
	 * @param Field_Factory  $field_factory  The factory for creating field objects.
	 * @param Settings_Store $settings_store The shared settings store object.
	 */
	public function __construct(
		private Field_Factory $field_factory,
		private Settings_Store $settings_store
	) {}

	/**
	 * Sanitizes the settings array by merging new input with existing options.
	 *
	 * This is the main callback for the 'sanitize_callback' argument in
	 * `register_setting`. It processes the raw input from the $_POST array.
	 *
	 * @param mixed $input The raw input from the form submission (from `$_POST`).
	 *
	 * @return array The complete, sanitized settings array ready for saving.
	 *
	 * @phpstan-return array<string, mixed>
	 */
	public function sanitize( mixed $input ): array {
		$option_name  = $this->settings_store->get_option_name();
		$option_group = $this->settings_store->get_option_group_name();
		$old_options  = get_option( $option_name, [] );
		$old_options  = is_array( $old_options ) ? $old_options : [];

		if ( ! is_array( $input ) ) {
			add_settings_error(
				$option_group,
				'error_invalid_input',
				'Invalid input received from submission. Please try again.'
			);
			return $old_options;
		}

		$fields_to_process = $this->get_fields_to_process();
		$new_values        = [];

		foreach ( $fields_to_process as $field_id => $field_config ) {
			try {
				$raw_value  = $input[ $field_id ] ?? null;
				$field_type = $field_config['type'];

				if ( 'description' === $field_type ) {
					continue;
				}

				// Handle custom validation first.
				$validation_callback = $field_config['extras']['validation_callback'] ?? null;
				if ( is_callable( $validation_callback ) ) {
					try {
						$result = $validation_callback( $raw_value, $field_config );
						if ( true !== $result ) {
							$error_message = is_string( $result ) && ! empty( $result )
								? $result
								: sprintf( 'Validation failed for the "%s" field.', $field_config['title'] );

							add_settings_error( $option_group, 'error_validation_' . $field_id, $error_message );

							// Revert to the old value and skip to the next field.
							$new_values[ $field_id ] = $old_options[ $field_id ] ?? null;
							continue;
						}
					} catch ( Throwable $e ) {
						$error_message = sprintf( 'An error occurred during validation for the "%s" field.', $field_config['title'] );

						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							$error_message .= " {$e->getMessage()}";
						}

						add_settings_error( $option_group, 'error_validation_' . $field_id, $error_message );

						$new_values[ $field_id ] = $old_options[ $field_id ] ?? null;
						continue;
					}
				}

				// If validation passes, proceed with sanitization.
				$field_object            = $this->field_factory->create( $field_type, $field_config );
				$sanitized_value         = $field_object->sanitize( $raw_value );
				$new_values[ $field_id ] = $sanitized_value;
			} catch ( InvalidArgumentException $e ) {
				// This catches errors from the Field_Factory or field constructor.
				add_settings_error( $option_group, 'error_field_config_' . $field_id, esc_html( $e->getMessage() ) );
				$new_values[ $field_id ] = $old_options[ $field_id ] ?? null;
			}
		}

		return array_merge( $old_options, $new_values );
	}

	/**
	 * Determines which fields should be processed in the current request.
	 *
	 * @return array
	 * @phpstan-return array<non-empty-string, Field_Config>
	 */
	private function get_fields_to_process(): array {
		if ( ! $this->settings_store->has_tabs() ) {
			return $this->settings_store->get_fields();
		}

		$active_tab = $this->settings_store->get_active_tab();

		if ( null === $active_tab ) {
			return [];
		}

		$sections_on_tab    = $this->settings_store->get_sections( $active_tab );
		$section_ids_on_tab = array_keys( $sections_on_tab );

		if ( empty( $section_ids_on_tab ) ) {
			return [];
		}

		return array_filter(
			$this->settings_store->get_fields(),
			static fn( array $field ): bool => in_array( $field['section'], $section_ids_on_tab, true )
		);
	}
}
