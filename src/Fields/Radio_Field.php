<?php
/**
 * Radio Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use InvalidArgumentException;

/**
 * Radio Field Class
 */
final class Radio_Field extends Abstract_Field {

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options     = $this->field_config['extras']['options'] ?? null;
		$html_prefix = $this->field_config['extras']['html_prefix'];

		if ( ! is_array( $options ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Options for field "%s" must be provided as an array.',
					$this->field_config['id']
				)
			);
		}

		$fields_html = [];

		foreach ( $options as $option_value => $option_label ) {
			if ( ! is_string( $option_label ) && ! is_numeric( $option_label ) ) {
				throw new InvalidArgumentException(
					sprintf(
						'Option labels for field "%s" must be provided as strings or numbers.',
						$this->field_config['id']
					)
				);
			}

			$radio_id = $this->field_config['id'] . '_' . sanitize_key( (string) $option_value );

			$fields_html[] = sprintf(
				'<label for="%s" class="%s-radio-label">
					<input type="radio" id="%s" name="%s" value="%s" %s %s />
					%s
				</label>',
				esc_attr( $radio_id ),
				esc_attr( $html_prefix ),
				esc_attr( $radio_id ),
				esc_attr( $this->field_config['name'] ),
				esc_attr( (string) $option_value ),
				checked( (string) $value, (string) $option_value, false ),
				$this->build_attributes_string( $attributes ),
				esc_html( (string) $option_label )
			);
		}

		echo implode( "\n", $fields_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?string {
		$options = $this->field_config['extras']['options'] ?? [];
		$options = is_array( $options ) ? $options : [];

		$default_value = parent::get_default_value();

		if ( is_scalar( $default_value ) && array_key_exists( (string) $default_value, $options ) ) {
			return (string) $default_value;
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		$options = $this->field_config['extras']['options'] ?? [];
		$options = is_array( $options ) ? $options : [];

		return is_scalar( $value ) && array_key_exists( (string) $value, $options )
			? (string) $value
			: $this->get_default_value();
	}
}
