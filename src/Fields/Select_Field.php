<?php
/**
 * Select Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Select2_Trait;

/**
 * Select Field Class
 */
class Select_Field extends Choice_Field {

	use Has_Select2_Trait;

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'select';

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
		$options = $this->get_options();

		$option_elements = [];
		foreach ( $options as $option_value => $option_label ) {
			$option_elements[] = sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( (string) $option_value ),
				selected( $this->get_value(), (string) $option_value, false ),
				esc_html( (string) $option_label )
			);
		}

		printf(
			'<select id="%s" name="%s" %s>%s</select>',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			$this->get_extra_html_attributes_string( [ 'class' => 'regular-text wptx-select2-field' ] ), // phpcs:ignore WordPress.Security.EscapeOutput
			implode( "\n", $option_elements ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}
