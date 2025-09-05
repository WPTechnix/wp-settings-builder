<?php
/**
 * Description Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Description Field Class
 */
final class Description_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'description';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$description = $this->get_description();
		if ( ! empty( $description ) ) {
			printf( '<div %s>%s</div>', $this->get_extra_html_attributes_string(), wp_kses_post( $description ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_render_description_below(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): mixed {
		// No value to sanitize.
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): mixed {
		return null;
	}
}
