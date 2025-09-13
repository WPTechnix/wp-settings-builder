<?php
/**
 * Checkbox Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Checkbox Field Class
 */
class Checkbox_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'checkbox';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {

		$description = $this->get_description();

		$value = $this->get_value();

		// First, build the core <input> element HTML. This is used in both cases.
		$input_html = sprintf(
			'<input type="checkbox" id="%s" name="%s" value="1" %s %s />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			checked( true, $value, false ),
			$this->get_extra_html_attributes_string() // phpcs:ignore WordPress.Security.EscapeOutput
		);

		if ( '' !== $description ) {
			// If a description exists, wrap the input and the description in a <label>.
			// This makes the description text itself clickable, improving UX.
			printf(
				'<label for="%s">%s %s</label>',
				esc_attr( $this->get_id() ),
				$input_html, // phpcs:ignore WordPress.Security.EscapeOutput
				wp_kses_post( $description ) // Sanitize the description, allowing safe HTML.
			);
		} else {
			// If no description exists, just output the input element.
			// The main field title in the <th> still acts as the primary label.
			echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return bool
	 */
	#[\Override]
	public function get_value(): bool {
		$value = $this->cast_checkbox_value( parent::get_value() );
		return null === $value ? $this->get_default_value() : $value;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return bool
	 */
	#[\Override]
	public function get_default_value(): bool {
		$value = $this->cast_checkbox_value( parent::get_default_value() );
		return null === $value ? false : $value;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return bool|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?bool {
		$value = $this->cast_checkbox_value( $value );
		return null === $value ? false : $value;
	}

	/**
	 * Should use inline title as the field label?
	 *
	 * @return bool
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return '' === $this->get_description();
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_render_description_below(): bool {
		return false;
	}

	/**
	 * Cast a value to a boolean if not null.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return bool|null
	 */
	private function cast_checkbox_value( mixed $value ): ?bool {
		if ( null === $value ) {
			return null;
		}
		if ( is_bool( $value ) ) {
			return $value;
		}
		return is_scalar( $value ) && '1' === (string) $value;
	}
}
