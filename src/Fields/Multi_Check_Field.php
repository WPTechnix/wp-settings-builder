<?php
/**
 * Multi Check Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Multi Check Field Class
 */
class Multi_Check_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'multi_check';

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render(): void {
		$options        = $this->get_options();
		$current_values = $this->get_value();
		$field_name     = $this->get_name() . '[]'; // Append [] for array submission.

		$fields_html = [];

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each checkbox for the label's 'for' attribute.
			$checkbox_id = $this->get_id() . '_' . sanitize_key( (string) $option_value );
			$is_checked  = in_array( (string) $option_value, $current_values, true );

			$fields_html[] = sprintf(
				// Wrap each checkbox in its own label for accessibility and layout control.
				// A surrounding <div> or <p> could be used in custom CSS for line breaks.
				'<label for="%s" class="wptx-multicheck-label">
					<input type="checkbox" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $checkbox_id ),
				esc_attr( $checkbox_id ),
				esc_attr( $field_name ),
				esc_attr( (string) $option_value ),
				checked( true, $is_checked, false ),
				$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
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
	public function get_value(): array {
		$value = parent::get_value();
		if ( ! is_array( $value ) ) {
			$value = $this->get_default_value();
		}

		return array_filter(
			$value,
			fn( $val ) => $this->is_valid_choice( $val )
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

	/**
	 * {@inheritDoc}
	 */
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-multicheck-label {
	display: block;
	margin-bottom: 10px;
}
.wptx-multicheck-label:not(:has(:disabled)):not(:has(:read-only)) {
	cursor: pointer;
}
CSS;
	}
}
