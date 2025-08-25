<?php
/**
 * Buttons Group Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Buttons Group Field Class
 */
final class Buttons_Group_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options     = $this->get_options();
		$html_prefix = $this->field_config['extras']['html_prefix'];

		$buttons_html = [];

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id   = $this->field_config['id'] . '_' . sanitize_key( (string) $option_value );
			$is_checked = ( (string) $value === (string) $option_value );

			// Determine the classes for the label to control its appearance.
			$label_classes   = [
				$html_prefix . '-button-group-label',
				$is_checked ? $html_prefix . '-button-group-label-active' : '',
			];
			$label_class_str = trim( implode( ' ', $label_classes ) );

			$buttons_html[] = sprintf(
				'<label for="%s" class="%s">
					<input type="radio" id="%s" name="%s" value="%s" class="screen-reader-text" %s %s />
					<span class="%s-button-group-text">%s</span>
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $label_class_str ),
				esc_attr( $radio_id ),
				esc_attr( $this->field_config['name'] ),
				esc_attr( (string) $option_value ),
				checked( true, $is_checked, false ),
				$this->build_attributes_string( $attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_attr( $html_prefix ),
				esc_html( (string) $option_label )
			);
		}

		// Wrap the buttons in a container for styling (e.g., with flexbox).
		printf(
			'<div class="%s-button-group" role="radiogroup">%s</div>',
			esc_attr( $html_prefix ),
			implode( "\n", $buttons_html ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();

		return $this->is_valid_choice( $default_value )
			? (string) $default_value
			: null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		return $this->is_valid_choice( $value )
			? (string) $value
			: $this->get_default_value();
	}
}
