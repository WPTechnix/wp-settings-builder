<?php
/**
 * Provides common functionality for fields that use the Select2 library.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Traits
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Traits;

/**
 * Provides common functionality for fields using the Select2 library.
 *
 * @phpstan-require-extends \WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field
 */
trait Has_Select2_Trait {

	/**
	 * {@inheritDoc}
	 */
	public static function get_asset_definitions(): array {
		$base_url = 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/';

		return [
			[
				'handle'  => 'select2-css',
				'type'    => 'css',
				'src'     => $base_url . 'css/select2.min.css',
				'version' => '4.0.13',
			],
			[
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
		// Target both single and multi-select fields with a shared class or comma-separated selector.
		$('.wptx-select2-field:not(.select2-hidden-accessible), .wptx-multi-select2-field:not(.select2-hidden-accessible)').select2({
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
/* Base container styles for both single and multi-select */
.wptx-select2-container.select2-container {
	min-width: 300px !important;
	width: 100% !important;
}

/* Specific styling for the selection box to match WP admin inputs */
.wptx-select2-container .select2-selection--single,
.wptx-select2-container .select2-selection--multiple {
	border: 1px solid var(--wptx-border-color, #8c8f94) !important;
	min-height: 30px !important; /* Match WP default input height */
	box-shadow: 0 0 0 transparent; /* Override default Select2 shadow */
}

/* Vertical alignment for single select text */
.wptx-select2-container .select2-selection--single .select2-selection__rendered {
	padding-left: 8px !important;
	line-height: 28px !important;
}

/* Vertical alignment for single select arrow */
.wptx-select2-container .select2-selection--single .select2-selection__arrow {
	height: 28px !important;
}

/* Focus outline to match WP admin styles */
.wptx-select2-container.select2-container--focus .select2-selection--single,
.wptx-select2-container.select2-container--focus .select2-selection--multiple {
    border-color: var(--wptx-accent, #007cba) !important;
    box-shadow: 0 0 0 1px var(--wptx-accent, #007cba) !important;
}

/* Dropdown option hover/highlight background */
.wptx-select2-container .select2-results__option--highlighted[aria-selected] {
	background-color: var(--wptx-primary, #0073aa) !important;
	color: #fff !important;
}
CSS;
	}
}
