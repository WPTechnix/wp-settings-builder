<?php
/**
 * Color Field Class using the WordPress Color Picker.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Color Field Class
 *
 * Renders a text input and enhances it with the WordPress color picker script.
 */
final class Color_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'color';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $css_handles = [ 'wp-color-picker' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $js_handles = [ 'wp-color-picker' ];

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$default_attributes = [
			'class'              => 'wptx-color-picker regular-text',
			'data-default-color' => $this->get_default_value() ?? '',
		];

		printf(
			'<input type="text" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( (string) $this->get_value() ),
			$this->get_extra_html_attributes_string( $default_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
    // Ensure the wpColorPicker function exists before calling it.
    if (typeof jQuery.fn.wpColorPicker === 'function') {
        $('.wptx-color-picker').wpColorPicker();
    }
});
JS;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		if ( ! is_string( $value ) ) {
			return $this->get_default_value();
		}

		$trimmed_value = trim( $value );

		// Allow clearing the value.
		if ( '' === $trimmed_value ) {
			return '';
		}

		// Check for a valid 3- or 6-digit hex color.
		if ( ! empty( preg_match( '/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $trimmed_value ) ) ) {
			return $trimmed_value;
		}

		// If the value is invalid, fall back to the default.
		return $this->get_default_value();
	}
}
