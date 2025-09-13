<?php
/**
 * Buttons Group Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use InvalidArgumentException;

/**
 * Buttons Group Field Class
 */
final class Buttons_Group_Field extends Choice_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'buttons_group';

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	#[\Override]
	public function render(): void {
		$options = $this->get_options();

		$buttons_html = [];

		foreach ( $options as $option_value => $option_label ) {
			if ( ! is_scalar( $option_label ) ) {
				continue;
			}
			$option_label = (string) $option_label;
			$option_value = (string) $option_value;

			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id = $this->get_id() . '_' . sanitize_key( $option_value );

			$buttons_html[] = sprintf(
				'<label for="%s" class="wptx-button-group-label">
					<input type="radio" id="%s" name="%s" value="%s" class="screen-reader-text" %s %s />
					<span class="wptx-button-group-text">%s</span>
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $radio_id ),
				esc_attr( $this->get_name() ),
				esc_attr( $option_value ),
				checked( $this->get_value(), $option_value, false ),
				$this->get_extra_html_attributes_string(),
				esc_html( $option_label )
			);
		}

		// Wrap the buttons in a container for styling (e.g., with flexbox).
		echo '<div class="wptx-button-group">';
		echo implode( "\n", $buttons_html ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-button-group {
	display: inline-flex;
	align-items: center;
	border: 1px solid var(--wptx-border-color, #aeb2b9);
	border-radius: 4px;
	overflow: hidden; /* Ensures the inner corners are clipped by the border-radius */
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.wptx-button-group-label {
	display: inline-block;
	padding: 6px 12px;
	font-size: 13px;
	line-height: 1.5;
	cursor: pointer;
	text-align: center;
	white-space: nowrap;
	background-color: #f6f7f7;
	color: #222;
	border-inline-end: 1px solid var(--wptx-border-color, #aeb2b9);
	transition: background-color 0.1s ease-in-out, color 0.1s ease-in-out;
}

.wptx-button-group-label:last-of-type {
	border-inline-end: none;
}

.wptx-button-group-label:not(:has(:checked)):hover {
	background-color: #e0e1e2;
}

.wptx-button-group-label:has(:checked) {
	background-color: var(--wptx-primary, #0073aa);
	color: #fff; /* Simplified from variable */
	border-color: transparent;
}

/* Use the accent color for the focus ring for proper theme integration */
.wptx-button-group-label:focus-within {
	box-shadow: 0 0 0 1px var(--wptx-accent, #007cba);
	z-index: 1;
	position: relative;
}
CSS;
	}
}
