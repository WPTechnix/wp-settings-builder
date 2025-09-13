<?php
/**
 * Description Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Description Field Class
 */
final class Description_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'description';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$description = $this->get_description();
		if ( '' !== $description ) {
			printf( '<div %s>%s</div>', $this->get_extra_html_attributes_string(), wp_kses_post( $description ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_render_description_below(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_default_value(): mixed {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function get_value(): mixed {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function sanitize( mixed $value ): mixed {
		return null;
	}
}
