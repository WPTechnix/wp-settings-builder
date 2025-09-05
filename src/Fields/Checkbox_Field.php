<?php
/**
 * Checkbox Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Checkbox Field Class
 */
class Checkbox_Field extends Abstract_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'checkbox';

	/**
	 * {@inheritDoc}
	 */
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

		if ( ! empty( $description ) ) {
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
	 * Cast a value to a boolean.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return bool|null
	 */
	private function cast_value_to_bool( mixed $value ): ?bool {
		if ( null === $value ) {
			return null;
		}
		if ( is_bool( $value ) ) {
			return $value;
		}
		return is_scalar( $value ) && '1' === (string) $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): ?bool {
		return $this->cast_value_to_bool( parent::get_value() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?bool {
		return $this->cast_value_to_bool( parent::get_default_value() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): mixed {
		if ( null === $value ) {
			return null;
		}
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_scalar( $value ) ) {
			return '1' === (string) $value;
		}

		return false;
	}

	/**
	 * Should use inline title as the field label?
	 *
	 * @return bool
	 */
	public function should_use_inline_title_as_label(): bool {
		return empty( $this->get_description() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_render_description_below(): bool {
		return false;
	}
}
