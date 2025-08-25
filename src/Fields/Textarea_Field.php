<?php
/**
 * Textarea Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Textarea Field Class
 */
final class Textarea_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		$default_attributes = [
			'rows'  => 5,
			'cols'  => 50,
			'class' => 'large-text',
		];

		$merged_attributes = array_merge( $default_attributes, $attributes );

		printf(
			'<textarea id="%s" name="%s" %s>%s</textarea>',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			$this->build_attributes_string( $merged_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_textarea( is_scalar( $value ) ? (string) $value : '' )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): string {
		return sanitize_textarea_field( is_scalar( $value ) ? (string) $value : '' );
	}
}
