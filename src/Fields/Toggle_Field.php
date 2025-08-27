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

		$value = $this->get_value();

		// Build the core toggle switch HTML structure.
		$toggle_html = sprintf(
			'<span class="wptx-toggle">
				<input type="checkbox" id="%s" name="%s" value="1" %s %s />
				<span class="wptx-toggle-slider"></span>
			</span>',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			checked( true, $value, false ),
			$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
		);

		if ( ! empty( $description ) ) {
			// If a description exists, wrap the toggle and the description in a <label>.
			// This makes the description text itself clickable.
			printf(
				'<label for="%s" class="wptx-toggle-label">%s %s</label>',
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


	/**
	 * {@inheritDoc}
	 */
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-toggle-label {
	display: flex;
	align-items: center;
}
.wptx-toggle-label:not(:has(:disabled)):not(:has(:read-only)) {
	cursor: pointer;
}

.wptx-toggle {
	position: relative;
	display: inline-block;
	width: 45px;
	height: 24px;
	flex-shrink: 0;
	margin-right: 0.875em;
}
.wptx-toggle input {
	opacity: 0;
	width: 0;
	height: 0;
}
.wptx-toggle-slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	border-radius: 25px;
	transition: background-color 0.2s;
}
.wptx-toggle-slider:before {
	position: absolute;
	content: "";
	height: 16px;
	width: 16px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	border-radius: 50%;
	transition: transform 0.2s;
}
.wptx-toggle input:checked + .wptx-toggle-slider {
	background-color: var(--wptx-primary, #0073aa);
}
.wptx-toggle input:focus-visible + .wptx-toggle-slider {
	box-shadow: 0 0 0 1px #fff, 0 0 0 3px var(--wptx-accent, #007cba);
}
.wptx-toggle input:checked + .wptx-toggle-slider:before {
	transform: translateX(21px);
}
CSS;
	}
}
