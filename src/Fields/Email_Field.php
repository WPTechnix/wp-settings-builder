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
final class Email_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'email';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$this->render_field( 'email' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_string( $value ) ? sanitize_email( $value ) : null;
	}
}
