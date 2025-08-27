<?php
/**
 * Buttons Group Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Buttons Group Field Class
 *
 * @phpstan-type Field_Type 'buttons_group'
 *
 * @phpstan-extends Choice_Field<Field_Type>
 */
final class Buttons_Group_Field extends Choice_Field {

	/**
	 * Field type
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'buttons_group';

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render(): void {
		$options = $this->get_options();

		$buttons_html = [];

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id = $this->get_id() . '_' . sanitize_key( (string) $option_value );

			$buttons_html[] = sprintf(
				'<label for="%s" class="wptx-button-group-label">
					<input type="radio" id="%s" name="%s" value="%s" class="screen-reader-text" %s %s />
					<span class="wptx-button-group-text">%s</span>
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $radio_id ),
				esc_attr( $this->get_name() ),
				esc_attr( (string) $option_value ),
				checked( $this->get_value(), (string) $option_value, false ),
				$this->get_extra_html_attributes_string(),
				esc_html( $option_label )
			);
		}

		// Wrap the buttons in a container for styling (e.g., with flexbox).
		echo '<div class="wptx-button-group-container">';
		echo implode( "\n", $buttons_html ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}
}
