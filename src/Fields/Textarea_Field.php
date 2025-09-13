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
final class Textarea_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'textarea';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
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
			esc_textarea( $this->get_value() )
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : null;
	}
}
