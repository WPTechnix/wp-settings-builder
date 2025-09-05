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
class Textarea_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'textarea';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$default_attributes = [
			'rows'  => 5,
			'cols'  => 50,
			'class' => 'large-text',
		];

		printf(
			'<textarea id="%s" name="%s" %s>%s</textarea>',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			$this->get_extra_html_attributes_string( $default_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_textarea( (string) $this->get_value() )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {

		$default_value     = $this->get_default_value();
		$sanitized_default = is_string( $default_value ) ? sanitize_textarea_field( $default_value ) : null;

		return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : $sanitized_default;
	}
}
