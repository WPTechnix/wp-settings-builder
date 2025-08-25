<?php
/**
 * Multiselect Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Handles rendering and sanitization for multi-select dropdown fields.
 */
final class Multiselect_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options          = $this->get_options();
		$html_prefix      = $this->field_config['extras']['html_prefix'];
		$current_values   = is_array( $value ) ? $value : [];
		$field_name_array = $this->field_config['name'] . '[]'; // Append [] for array submission.

		$option_elements = [];
		foreach ( $options as $option_value => $option_label ) {
			$is_selected       = in_array( (string) $option_value, $current_values, true );
			$option_elements[] = sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( (string) $option_value ),
				selected( true, $is_selected, false ),
				esc_html( (string) $option_label )
			);
		}

		$default_attributes = [
			'multiple' => 'multiple',
			'class'    => "{$html_prefix}-select2-field",
		];
		$merged_attributes  = array_merge( $default_attributes, $attributes );

		printf(
			'<select id="%s" name="%s" %s>%s</select>',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $field_name_array ),
			$this->build_attributes_string( $merged_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			implode( "\n", $option_elements ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
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
			fn( $value ) => $this->is_valid_choice( $value )
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
			// If the submitted value isn't an array (e.g., nothing selected),
			// return an empty array.
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
