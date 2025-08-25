<?php
/**
 * Radio Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Radio Field Class
 */
final class Radio_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options     = $this->get_options();
		$html_prefix = $this->field_config['extras']['html_prefix'];

		$fields_html = [];

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id = $this->field_config['id'] . '_' . sanitize_key( (string) $option_value );

			$fields_html[] = sprintf(
				// Wrap each radio button in its own label for better accessibility and layout control.
				'<label for="%s" class="%s-radio-label">
					<input type="radio" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $html_prefix ),
				esc_attr( $radio_id ),
				esc_attr( $this->field_config['name'] ),
				esc_attr( (string) $option_value ),
				checked( (string) $value, (string) $option_value, false ),
				$this->build_attributes_string( $attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( (string) $option_label )
			);
		}

		// Output the radio buttons, separated by a newline for cleaner HTML source.
		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();

		return $this->is_valid_choice( $default_value )
			? (string) $default_value
			: null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		return $this->is_valid_choice( $value )
			? (string) $value
			: $this->get_default_value();
	}
}
