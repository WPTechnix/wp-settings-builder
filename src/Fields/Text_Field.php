<?php
/**
 * Text Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Text Field Class
 */
class Text_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'text';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$this->render_field( 'text' );
	}

	/**
	 * Renders the field.
	 *
	 * @param string $type The type of input field to render.
	 *
	 * @phpstan-param non-empty-string $type
	 */
	public function render_field( string $type ): void {
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" %s />',
			esc_attr( $type ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( (string) $this->get_value() ),
			$this->get_extra_html_attributes_string( [ 'class' => 'regular-text' ] ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): ?string {
		$value = parent::get_value();
		return is_scalar( $value ) ? (string) $value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();
		return is_scalar( $default_value ) ? (string) $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {

		$default_value     = $this->get_default_value();
		$sanitized_default = is_string( $default_value ) ? sanitize_text_field( $default_value ) : null;

		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : $sanitized_default;
	}

	/**
	 * Should use inline title as the field label?
	 *
	 * @return bool
	 */
	public function should_use_inline_title_as_label(): bool {
		return true;
	}
}
