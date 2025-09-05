<?php
/**
 * Choice Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Choice Field Class
 *
 * @phpstan-template Field_Type of non-empty-string = 'choice'
 */
class Choice_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'choice';

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render(): void {
		$options = $this->get_options();

		$fields_html = [];

		$value = $this->get_value();

		foreach ( $options as $option_value => $option_label ) {
			// Create a unique ID for each radio button for the label's 'for' attribute.
			$radio_id = $this->get_id() . '_' . sanitize_key( (string) $option_value );

			$fields_html[] = sprintf(
				// Wrap each radio button in its own label for better accessibility and layout control.
				'<label for="%s" class="wptx-radio-label">
					<input type="radio" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $radio_id ),
				esc_attr( $this->get_name() ),
				esc_attr( (string) $option_value ),
				checked( $this->get_value(), (string) $option_value, false ),
				$this->get_extra_html_attributes_string(), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( (string) $option_label )
			);
		}

		// Output the radio buttons, separated by a newline for cleaner HTML source.
		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): ?string {
		$value = parent::get_value();

		return $this->is_valid_choice( $value ) ? (string) $value : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return null|string
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();

		return $this->is_valid_choice( $default_value ) ? (string) $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return null|string
	 */
	public function sanitize( mixed $value ): ?string {
		return $this->is_valid_choice( $value ) ? (string) $value : $this->get_default_value();
	}

		/**
		 * {@inheritDoc}
		 */
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
