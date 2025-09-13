<?php
/**
 * Select Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Select2_Trait;

/**
 * Select Field Class
 */
final class Select_Field extends Choice_Field {

	use Has_Select2_Trait;

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'select';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $css_handles = [ 'select2-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $js_handles = [ 'select2-js', 'select2-locale' ];

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When options are not provided as an array or invalid options are found.
	 */
	#[\Override]
	public function render(): void {
		$options = $this->get_options();

		$option_elements = [];
		foreach ( $options as $option_value => $option_label ) {
			if ( ! is_scalar( $option_label ) ) {
				continue;
			}
			$option_label = (string) $option_label;
			$option_value = (string) $option_value;

			$option_elements[] = sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $option_value ),
				selected( $this->get_value(), $option_value, false ),
				esc_html( $option_label )
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
