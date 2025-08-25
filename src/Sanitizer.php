<?php
/**
 * Sanitizes settings before they are saved to the database.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

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
		// 1. Fetch all previously saved options from the database.
		// This is the base we'll be merging the new values into.
		$option_name = $this->settings_store->get_option_name();
		$old_options = get_option( $option_name, [] );
		$old_options = is_array( $old_options ) ? $old_options : [];

		// If the submitted data isn't an array, it's invalid. Return the old
		// options to prevent data loss and show a settings error.
		if ( ! is_array( $input ) ) {
			add_settings_error(
				$this->settings_store->get_option_group_name(),
				'invalid_input_type',
				__( 'Settings data received was not in the expected format. No changes were saved.', 'default' ),
				'error'
			);

			return $old_options;
		}

		// 2. Determine which fields we need to process from this submission.
		$fields_to_process = $this->get_fields_to_process();
		$new_values        = [];

		foreach ( $fields_to_process as $field_id => $field_config ) {
			$raw_value  = $input[ $field_id ] ?? null;
			$field_type = $field_config['type'];

			if ( 'description' === $field_type ) {
				continue; // Description-only fields have no value to save.
			}

			$field_object = $this->field_factory->create( $field_type, $field_config );

			// If a value is not submitted (e.g., an unchecked checkbox),
			// it will be null. The field's sanitize method is responsible
			// for handling this to return its correct "off" state.
			$sanitized_value = $field_object->sanitize( $raw_value );

			// Store the processed value.
			$new_values[ $field_id ] = $sanitized_value;
		}

		// 3. Merge the newly sanitized values into the old options and return.
		// This preserves all settings from other tabs not in this submission.
		return array_merge( $old_options, $new_values );
	}

	/**
	 * Determines which fields should be processed in the current request.
	 *
	 * If tabs are enabled, it returns only the fields for the active tab.
	 * Otherwise, it returns all registered fields.
	 *
	 * @return array
	 *
	 * @phpstan-return array<non-empty-string, Field_Config>
	 */
	private function get_fields_to_process(): array {
		// If the settings page does not use tabs, process all registered fields.
		if ( ! $this->settings_store->has_tabs() ) {
			return $this->settings_store->get_fields();
		}

		$active_tab = $this->settings_store->get_active_tab();

		// Failsafe: if tabs are enabled but for some reason no tab is active,
		// process nothing to prevent accidental data loss.
		if ( null === $active_tab ) {
			return [];
		}

		// Get the IDs of all sections that belong to the active tab.
		$sections_on_tab    = $this->settings_store->get_sections( $active_tab );
		$section_ids_on_tab = array_keys( $sections_on_tab );

		if ( empty( $section_ids_on_tab ) ) {
			return [];
		}

		// Filter all registered fields to get only those belonging to one of the
		// sections on the active tab.
		return array_filter(
			$this->settings_store->get_fields(),
			static fn( array $field ): bool => in_array( $field['section'], $section_ids_on_tab, true )
		);
	}
}
