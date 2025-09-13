<?php
/**
 * Choice Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Choice Field Class
 */
class Choice_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'choice';

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	#[\Override]
	public function render(): void {
		$options = $this->get_options();

		$fields_html = [];

		$value = $this->get_value();

		foreach ( $options as $option_value => $option_label ) {
			if ( ! is_scalar( $option_label ) ) {
				continue;
			}
			$option_label = (string) $option_label;
			$option_value = (string) $option_value;

			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id = $this->get_id() . '_' . sanitize_key( $option_value );

			$fields_html[] = sprintf(
				// Wrap each radio button in its own label for better accessibility and layout control.
				'<label for="%s" class="wptx-radio-label">
					<input type="radio" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $radio_id ),
				esc_attr( $this->get_name() ),
				esc_attr( $option_value ),
				checked( $value, $option_value, false ),
				$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $option_label )
			);
		}

		// Output the radio buttons, separated by a newline for cleaner HTML source.
		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return is_string( $default_value ) && $this->is_valid_choice( $default_value ) ? $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return is_string( $value ) && $this->is_valid_choice( $value ) ? $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		return is_string( $value ) && $this->is_valid_choice( $value ) ? $value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-radio-label {
	display: block;
	margin-bottom: 10px;
}
.wptx-radio-label:not(:has(:disabled)):not(:has(:read-only)) {
	cursor: pointer;
}
CSS;
	}
}
