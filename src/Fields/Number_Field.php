<?php
/**
 * Number Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Handles rendering and sanitization for number fields.
 */
final class Number_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$default_attributes = [ 'class' => 'regular-text' ];
		$merged_attributes  = array_merge( $default_attributes, $attributes );

		printf(
			'<input type="number" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			esc_attr( is_numeric( $value ) ? (string) $value : '' ),
			$this->build_attributes_string( $merged_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?float {
		$default_value = parent::get_default_value();
		return is_numeric( $default_value ) ? (float) $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?float {
		return is_numeric( $value ) ? (float) $value : $this->get_default_value();
	}
}
