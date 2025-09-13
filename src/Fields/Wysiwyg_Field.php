<?php
/**
 * WYSIWYG (TinyMCE) Editor Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Creates a WordPress WYSIWYG (TinyMCE) editor field.
 *
 * This class utilizes the core `wp_editor()` function to render the rich text editor.
 */
final class Wysiwyg_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'wysiwyg';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$settings = $this->get_extra( 'editor_settings', [] );
		$settings = is_array( $settings ) ? $settings : [];

		$default_settings = [
			'textarea_name' => $this->get_name(),
			'textarea_rows' => 10,
		];

		$editor_settings = wp_parse_args( $settings, $default_settings );

		// The wp_editor function echoes its output directly.
		ob_start();
		wp_editor( $this->get_value(), $this->get_id(), $editor_settings );
		$editor_contents = ob_get_clean();

		// The output from wp_editor is complex HTML and is considered safe.
		// It should not be escaped further.
		echo $editor_contents; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return is_string( $default_value ) ? $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return is_string( $value ) ? $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_string( $value ) ? $value : null;
	}
}
