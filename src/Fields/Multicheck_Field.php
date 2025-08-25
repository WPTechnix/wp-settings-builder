<?php
/**
 * Multicheck Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Handles rendering and sanitization for a field with multiple checkboxes.
 */
final class Multicheck_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options        = $this->get_options();
		$html_prefix    = $this->field_config['extras']['html_prefix'];
		$current_values = is_array( $value ) ? $value : [];
		$field_name     = $this->field_config['name'] . '[]'; // Append [] for array submission.

		// Prepare the array of string values once, before the loop, for efficiency.
		$string_current_values = array_map( 'strval', $current_values );

		$fields_html = [];

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each checkbox for the label's 'for' attribute.
			$checkbox_id = $this->field_config['id'] . '_' . sanitize_key( (string) $option_value );
			$is_checked  = in_array( (string) $option_value, $string_current_values, true );

			$fields_html[] = sprintf(
				// Wrap each checkbox in its own label for accessibility and layout control.
				// A surrounding <div> or <p> could be used in custom CSS for line breaks.
				'<label for="%s" class="%s-multicheck-label">
					<input type="checkbox" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $checkbox_id ),
				esc_attr( $html_prefix ),
				esc_attr( $checkbox_id ),
				esc_attr( $field_name ),
				esc_attr( (string) $option_value ),
				checked( true, $is_checked, false ),
				$this->build_attributes_string( $attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( (string) $option_label )
			);
		}

		// Output the checkboxes, separated by a newline for cleaner HTML source.
		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 *
	 * @phpstan-return array<int, string>
	 */
	public function get_default_value(): array {
		$default_value = parent::get_default_value();

		if ( ! is_array( $default_value ) ) {
			return [];
		}

		// Filter the default values to ensure every item is a valid choice.
		$valid_defaults = array_filter(
			$default_value,
			fn( $val ) => $this->is_valid_choice( $val )
		);

		// Re-index the array and ensure all values are strings.
		return array_values( array_map( 'strval', $valid_defaults ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 *
	 * @phpstan-return array<int, string>
	 */
	public function sanitize( mixed $value ): array {
		if ( ! is_array( $value ) ) {
			// If no checkboxes are checked, the key won't be in $_POST.
			// The sanitizer will pass `null`, so we must return an empty array.
			return [];
		}

		// Filter the submitted array to ensure every item is a valid choice.
		$sanitized_values = array_filter(
			$value,
			fn( $v ) => $this->is_valid_choice( $v )
		);

		// Re-index the array and ensure all values are strings for consistent storage.
		return array_values( array_map( 'strval', $sanitized_values ) );
	}
}
