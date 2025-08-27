<?php
/**
 * Select Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Select Field Class
 */
class Select_Field extends Choice_Field {

	/**
	 * Field type
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'select';

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


	/**
	 * {@inheritDoc}
	 */
	public static function get_asset_definitions(): array {
		// The URLs for the Select2 library are placeholders.
		// Replace them with the actual URLs where you are hosting these assets.
		$base_url = 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/';

		return [
			'select2-css' => [
				'handle'  => 'select2-css',
				'type'    => 'css',
				'src'     => $base_url . 'css/select2.min.css',
				'version' => '4.0.13',
			],
			'select2-js'  => [
				'handle'       => 'select2-js',
				'type'         => 'js',
				'src'          => $base_url . 'js/select2.full.min.js',
				'dependencies' => [ 'jquery' ],
				'version'      => '4.0.13',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
	if (jQuery().select2) {
		$('.wptx-select2-field:not(.select2-hidden-accessible)').select2({
			 width: '100%',
			 allowClear: true,
			 containerCssClass: 'wptx-select2-container',
			 dropdownCssClass: 'wptx-select2-container'			 
		});
	}
});
JS;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_css_contents(): string {
		return <<<'CSS'
/* Base container styles */
.wptx-select2-container.select2-container {
	min-width: 300px;
	width: 100% !important;
}
.wptx-select2-container .select2-selection--multiple {
	border: 1px solid var(--wptx-border-color, #8c8f94);
	min-height: 30px; /* Match WP default input height */
}

/* Dropdown option hover/highlight background */
.wptx-select2-container .select2-results__option--highlighted[aria-selected] {
	background-color: var(--wptx-primary, #0073aa) !important;
	color: #fff !important;
}
CSS;
	}
}
