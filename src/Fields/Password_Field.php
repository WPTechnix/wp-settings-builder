<?php
/**
 * Password Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Password Field Class
 */
final class Password_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$default_attributes = [ 'class' => 'regular-text' ];

		$merged_attributes = array_merge( $default_attributes, $attributes );

		printf(
			'<input type="password" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			esc_attr( is_scalar( $value ) ? (string) $value : '' ),
			$this->build_attributes_string( $merged_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): string {
		// Passwords should not be modified beyond scalar casting.
		return is_scalar( $value ) ? (string) $value : '';
	}
}
