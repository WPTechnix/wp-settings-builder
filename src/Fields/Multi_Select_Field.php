<?php
/**
 * Multi Select Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Select2_Trait;

/**
 * Handles rendering and sanitization for multi-select dropdown fields.
 */
final class Multi_Select_Field extends Multi_Check_Field {

	use Has_Select2_Trait;

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'multi_select';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $css_handles = [ 'select2-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $js_handles = [ 'select2-js' ];

	/**
	 * {@inheritDoc}
	 *
	 * @throws \InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	public function render(): void {
		$options          = $this->get_options();
		$current_values   = $this->get_value();
		$field_name_array = $this->get_name() . '[]'; // Append [] for array submission.

		$option_elements = [];
		foreach ( $options as $option_value => $option_label ) {
			$is_selected       = in_array( (string) $option_value, $current_values, true );
			$option_elements[] = sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( (string) $option_value ),
				selected( true, $is_selected, false ),
				esc_html( (string) $option_label )
			);
		}

		$default_attributes = [
			'multiple' => 'multiple',
			'class'    => 'regular-text wptx-multi-select2-field',
		];

		printf(
			'<select id="%s" name="%s" %s>%s</select>',
			esc_attr( $this->get_id() ),
			esc_attr( $field_name_array ),
			$this->get_extra_html_attributes_string( $default_attributes ), // phpcs:ignore WordPress.Security.EscapeOutput
			implode( "\n", $option_elements ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}
