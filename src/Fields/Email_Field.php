<?php
/**
 * Email Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Email Field Class
 */
final class Email_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$default_attributes = [ 'class' => 'regular-text' ];

		$merged_attributes = array_merge( $default_attributes, $attributes );

		printf(
			'<input type="email" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			esc_attr( is_scalar( $value ) ? (string) $value : '' ),
			$this->build_attributes_string( $merged_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * Get the default value for the field.
	 *
	 * @return string|null
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();
		return is_string( $default_value ) ? $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		$default_value     = $this->get_default_value();
		$sanitized_default = is_string( $default_value ) ? sanitize_email( $default_value ) : null;
		return is_string( $value ) ? sanitize_email( $value ) : $sanitized_default;
	}
}
