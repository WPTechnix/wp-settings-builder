<?php
/**
 * Text Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Text Field Class
 */
class Text_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'text';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$this->render_field( 'text' );
	}

	/**
	 * Renders the field.
	 *
	 * @param non-empty-string $type The type of input field to render.
	 */
	public function render_field( string $type ): void {
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" %s />',
			esc_attr( $type ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_value() ),
			$this->get_extra_html_attributes_string( [ 'class' => 'regular-text' ] ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return is_scalar( $default_value ) ? (string) $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return is_scalar( $value ) ? (string) $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_scalar( $value ) ? sanitize_email( (string) $value ) : null;
	}

	/**
	 * Should use inline title as the field label?
	 *
	 * @return bool
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return true;
	}
}
