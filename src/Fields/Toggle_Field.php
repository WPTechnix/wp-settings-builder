<?php
/**
 * Toggle Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Toggle Field Class
 *
 * Renders a checkbox styled as a toggle switch.
 */
final class Toggle_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$description = $this->field_config['extras']['description'] ?? null;
		$html_prefix = $this->field_config['extras']['html_prefix'];

		// Build the core toggle switch HTML structure.
		$toggle_html = sprintf(
			'<span class="%s-toggle">
				<input type="checkbox" id="%s" name="%s" value="1" %s %s />
				<span class="%s-toggle-slider"></span>
			</span>',
			esc_attr( $html_prefix ),
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			checked( true, $value, false ),
			$this->build_attributes_string( $attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_attr( $html_prefix )
		);

		if ( is_string( $description ) && ! empty( $description ) ) {
			// If a description exists, wrap the toggle and the description in a <label>.
			// This makes the description text itself clickable.
			printf(
				'<label for="%s">%s %s</label>',
				esc_attr( $this->field_config['id'] ),
				$toggle_html, // phpcs:ignore WordPress.Security.EscapeOutput
				wp_kses_post( $description )
			);
		} else {
			// If no description exists, just output the toggle structure.
			// The main field title in the <th> will act as the label.
			echo $toggle_html; // phpcs:ignore WordPress.Security.EscapeOutput
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
