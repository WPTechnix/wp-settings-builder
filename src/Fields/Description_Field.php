<?php
/**
 * Description Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Description Field Class
 */
final class Description_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 */
	public function render( mixed $value, array $attributes ): void {
		// This field only displays its description, which is handled by the renderer.
		// It has no input element.
		if ( ! empty( $this->field_config['extras']['description'] ) ) {
			echo '<div ' . $this->build_attributes_string( $attributes ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo wp_kses_post( $this->field_config['extras']['description'] );
			echo '</div>';
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): mixed {
		// No value to sanitize.
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): mixed {
		return null;
	}
}
