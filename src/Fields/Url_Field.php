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
class Url_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'url';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$this->render_field( 'url' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		$default_value     = $this->get_default_value();
		$sanitized_default = is_string( $default_value ) ? esc_url_raw( $default_value ) : null;

		return is_string( $value ) ? esc_url_raw( $value ) : $sanitized_default;
	}
}
