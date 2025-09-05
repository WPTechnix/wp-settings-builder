<?php
/**
 * Provides common functionality for fields that use the Select2 library with AJAX.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Traits
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Traits;

/**
 * Provides common functionality for fields using the Select2 library with AJAX.
 *
 * @phpstan-require-extends \WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field
 */
trait Has_Ajax_Select2_Trait {

	use Has_Select2_Trait; // Reuse the existing asset definitions and base CSS.

	/**
	 * {@inheritDoc}
	 */
	public static function get_js_contents(): string {
		// This JS is more complex as it handles AJAX, nonces, and passing query args.
		return <<<'JS'
jQuery(function($) {
    if (typeof jQuery.fn.select2 !== 'function') {
        return;
    }

    $('.wptx-ajax-select2').each(function() {
        const $select = $(this);
        const options = {
            width: '100%',
            allowClear: true,
            containerCssClass: 'wptx-select2-container',
            dropdownCssClass: 'wptx-select2-container',
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: $select.data('action'),
                        _ajax_nonce: $select.data('nonce'),
                        q: params.term, // search term
                        page: params.page || 1,
                        query_args: $select.data('query-args') || {}
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.items,
                        pagination: {
                            more: data.data.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
        };

        // For single-select fields, if there's an initial value, show it.
        if (!$select.prop('multiple')) {
            const initialValue = $select.data('initial-value');
            if (initialValue && initialValue.id) {
                const option = new Option(initialValue.text, initialValue.id, true, true);
                $select.append(option).trigger('change');
            }
        }
        
        $select.select2(options);
    });
});
JS;
	}
}
