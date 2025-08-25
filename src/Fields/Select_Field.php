<?php
/**
 * Select Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Choices_Trait;

/**
 * Select Field Class
 */
final class Select_Field extends Abstract_Field {

	use Has_Choices_Trait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render( mixed $value, array $attributes ): void {
		$options = $this->get_options();

		$option_elements = [];
		foreach ( $options as $option_value => $option_label ) {
			$option_elements[] = sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( (string) $option_value ),
				selected( (string) $value, (string) $option_value, false ),
				esc_html( (string) $option_label )
			);
		}

		$default_attributes = [ 'class' => 'regular-text' ];
		$merged_attributes  = array_merge( $default_attributes, $attributes );

		printf(
			'<select id="%s" name="%s" %s>%s</select>',
			esc_attr( $this->field_config['id'] ),
			esc_attr( $this->field_config['name'] ),
			$this->build_attributes_string( $merged_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			implode( "\n", $option_elements ) // phpcs:ignore WordPress.Security.EscapeOutput
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
