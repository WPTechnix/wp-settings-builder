<?php
/**
 * Email Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Email Field Class
 */
class Email_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'email';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$this->render_field( 'email' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		$default_value     = $this->get_default_value();
		$sanitized_default = is_string( $default_value ) ? sanitize_email( $default_value ) : null;
		return is_string( $value ) ? sanitize_email( $value ) : $sanitized_default;
	}
}
