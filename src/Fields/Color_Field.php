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
	 * @var non-empty-string
	 */
	protected static string $type = 'color';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $css_handles = [ 'wp-color-picker' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $js_handles = [ 'wp-color-picker' ];

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$default_attributes = [
			'class'              => 'wptx-color-picker regular-text',
			'data-default-color' => $this->get_default_value(),
		];

		printf(
			'<input type="text" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_value() ),
			$this->get_extra_html_attributes_string( $default_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return self::is_valid_hex( $default_value ) ? $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return self::is_valid_hex( $value ) ? $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		if ( ! is_string( $value ) ) {
			return null;
		}

		$trimmed_value = trim( $value );

		// Check for a 6-digit hex color.
		if ( self::is_valid_hex( $trimmed_value ) ) {
			return $trimmed_value;
		}

		return null;
	}

	/**
	 * Check if a value is a valid 6-digit hex color.
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool True if the value is a valid hex color, false otherwise.
	 */
	private static function is_valid_hex( mixed $value ): bool {
		return is_string( $value ) && 1 === preg_match( '/^#([a-fA-F0-9]{6})$/', $value );
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
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
}
