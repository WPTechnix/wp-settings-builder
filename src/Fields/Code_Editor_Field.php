<?php
/**
 * Code Editor Field Class using WordPress's built-in CodeMirror instance.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare( strict_types=1 );

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Code_Editor_Field
 *
 * Renders a textarea that is enhanced by the WordPress Code Editor API.
 * This class correctly supports multiple editors with different language modes on the same page.
 */
final class Code_Editor_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'code_editor';

	/**
	 * Renders the textarea element that will be replaced by the code editor.
	 */
	#[\Override]
	public function render(): void {
		$default_attributes = [
			'class' => 'widefat',
			'style' => 'height: 200px;', // Provide a sensible default height.
		];

		$value = $this->get_value();

		printf(
			'<textarea id="%s" name="%s" %s>%s</textarea>',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			$this->get_extra_html_attributes_string( $default_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_textarea( $value )
		);
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

	/**
	 * Gets the code editor mode (e.g., 'text/css') required by this specific field instance.
	 *
	 * This method is specific to the Code_Editor_Field and is used by the Asset_Loader.
	 *
	 * @return non-empty-string
	 */
	public function get_mode(): string {
		$mode = $this->get_extra( 'mode' );

		return is_string( $mode ) && '' !== $mode ? $mode : 'text/html';
	}
}
