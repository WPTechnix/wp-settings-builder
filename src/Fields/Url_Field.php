<?php
/**
 * URL Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * URL Field Class
 */
final class Url_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'url';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$this->render_field( 'url' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return $this->is_like_url( $default_value ) ? $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return $this->is_like_url( $value ) ? $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_string( $value ) ? esc_url_raw( $value ) : null;
	}

	/**
	 * Check if the given value is like a URL.
	 *
	 * @param string $value The value to check.
	 * @return bool True if the value is like a URL, false otherwise.
	 */
	private function is_like_url( string $value ): bool {
		return false !== filter_var( $value, FILTER_VALIDATE_URL );
	}
}
