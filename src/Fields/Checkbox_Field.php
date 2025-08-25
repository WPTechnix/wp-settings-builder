<?php
/**
 * Checkbox Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Checkbox Field Class
 */
final class Checkbox_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$description = $this->field_config['extras']['description'] ?? null;

		// First, build the core <input> element HTML. This is used in both cases.
		$input_html = sprintf(
			'<input type="checkbox" id="%s" name="%s" value="1" %s %s />',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			checked( true, $value, false ),
			$this->build_attributes_string( $attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);

		if ( is_string( $description ) && ! empty( $description ) ) {
			// If a description exists, wrap the input and the description in a <label>.
			// This makes the description text itself clickable, improving UX.
			printf(
				'<label for="%s">%s %s</label>',
				esc_attr( $this->field_config['id'] ),
				$input_html, // phpcs:ignore WordPress.Security.EscapeOutput
				wp_kses_post( $description ) // Sanitize the description, allowing safe HTML.
			);
		} else {
			// If no description exists, just output the input element.
			// The main field title in the <th> still acts as the primary label.
			echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): bool {
		$default_value = parent::get_default_value();
		if ( is_bool( $default_value ) ) {
			return $default_value;
		}
		return is_scalar( $default_value ) && '1' === (string) $default_value;
	}


	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_scalar( $value ) ) {
			return '1' === (string) $value;
		}

		return false;
	}
}
