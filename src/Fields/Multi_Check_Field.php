<?php
/**
 * Multi Check Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare( strict_types=1 );

namespace WPTechnix\WP_Settings_Builder\Fields;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;
use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Multi Check Field Class
 */
class Multi_Check_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'multi_check';

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	#[\Override]
	public function render(): void {
		$options        = $this->get_options();
		$current_values = $this->get_value();
		$field_name     = $this->get_name() . '[]'; // Append [] for array submission.

		$fields_html = [];

		foreach ( $options as $option_value => $option_label ) {
			if ( ! is_scalar( $option_label ) ) {
				continue;
			}
			$option_label = (string) $option_label;
			$option_value = (string) $option_value;

			// Create a unique ID for each checkbox for the label's 'for' attribute.
			$checkbox_id = $this->get_id() . '_' . sanitize_key( $option_value );
			$is_checked  = in_array( $option_value, $current_values, true );

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
				esc_attr( $option_value ),
				checked( true, $is_checked, false ),
				$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $option_label )
			);
		}

		// Output the checkboxes, separated by a newline for cleaner HTML source.
		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return list<non-empty-string>
	 */
	#[\Override]
	public function get_default_value(): array {
		$default = parent::get_default_value();
		if ( ! is_array( $default ) ) {
			return [];
		}

		return $this->filter_valid_choices( $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return list<non-empty-string>
	 */
	#[\Override]
	public function get_value(): array {
		$value = parent::get_value();
		return is_array( $value ) ? $this->filter_valid_choices( $value ) : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return list<non-empty-string>|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?array {
		if ( ! is_array( $value ) ) {
			return null;
		}

		return $this->filter_valid_choices( $value );
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
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
