<?php
/**
 * Toggle Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Toggle Field Class
 *
 * Renders a checkbox styled as a toggle switch.
 */
final class Toggle_Field extends Checkbox_Field {

	/**
	 * Field type
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'toggle';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$description = $this->get_description();

		$html_prefix = $this->get_html_prefix();

		$value = $this->get_value();

		// Build the core toggle switch HTML structure.
		$toggle_html = sprintf(
			'<span class="%s-toggle">
				<input type="checkbox" id="%s" name="%s" value="1" %s %s />
				<span class="%s-toggle-slider"></span>
			</span>',
			esc_attr( $html_prefix ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			checked( true, $value, false ),
			$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_attr( $html_prefix )
		);

		if ( ! empty( $description ) ) {
			// If a description exists, wrap the toggle and the description in a <label>.
			// This makes the description text itself clickable.
			printf(
				'<label for="%s">%s %s</label>',
				esc_attr( $this->get_id() ),
				$toggle_html, // phpcs:ignore WordPress.Security.EscapeOutput
				wp_kses_post( $description )
			);
		} else {
			// If no description exists, just output the toggle structure.
			// The main field title in the <th> will act as the label.
			echo $toggle_html; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
