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
 * @require-extends \WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
trait Has_Select2_Trait {

	/**
	 * Select2 library version.
	 *
	 * @var non-empty-string
	 */
	private static string $select2_version = '4.0.13';

	/**
	 * Select2 locale key.
	 *
	 * @var non-empty-string|false|null
	 */
	private static null|false|string $select2_locale = null;

	/**
	 * Get the asset definitions for this field.
	 *
	 * @return array
	 * @phpstan-return list<Asset>
	 * @psalm-return list<Asset>
	 */
	#[\Override]
	public static function get_asset_definitions(): array {
		$base_url = 'https://cdn.jsdelivr.net/npm/select2@' . self::$select2_version . '/dist/';

		return [
			[
				'handle'       => 'select2-css',
				'type'         => 'css',
				'src'          => $base_url . 'css/select2.min.css',
				'version'      => self::$select2_version,
				'dependencies' => [],
			],
			[
				'handle'       => 'select2-js',
				'type'         => 'js',
				'src'          => $base_url . 'js/select2.full.min.js',
				'dependencies' => [ 'jquery' ],
				'version'      => self::$select2_version,
			],
			[
				'handle'       => 'select2-locale',
				'type'         => 'js',
				'src'          => function () use ( $base_url ): string|false {
					$locale = self::get_select2_locale();
					if ( false !== $locale ) {
						return "{$base_url}js/i18n/{$locale}.js";
					}
					return false;
				},
				'dependencies' => [ 'select2-js' ],
				'version'      => self::$select2_version,
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_js_contents(): string {

		$locale = self::get_select2_locale();

		$locale_option = false !== $locale ? "language: '$locale'," : '';

		return <<<JS
jQuery(function($) {
	if (jQuery().select2) {
		$('.wptx-select2-field:not(.select2-hidden-accessible), .wptx-multi-select2-field:not(.select2-hidden-accessible)').select2({
			 width: '100%',
			 allowClear: true,
			 containerCssClass: 'wptx-select2-container',
			 dropdownCssClass: 'wptx-select2-container',
			 $locale_option
		});
	}
});
JS;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
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


	/**
	 * Determines the appropriate Select2 locale key from the WordPress locale.
	 *
	 * Implements a multi-tiered fallback system:
	 * 1. Checks for specific, high-priority mappings (e.g., pt_BR -> pt-BR).
	 * 2. Falls back to the generic two-letter language code if available (e.g., fr_CA -> fr).
	 * 3. Returns null if no match is found.
	 *
	 * @return string|false The locale key for Select2, or false if not needed/supported.
	 */
	protected static function get_select2_locale(): string|false {

		if ( null !== self::$select2_locale ) {
			return self::$select2_locale;
		}

		$wp_locale = get_user_locale();

		if ( str_starts_with( $wp_locale, 'en_' ) ) {
			self::$select2_locale = false;
			return self::$select2_locale;
		}

		// The complete list of available Select2 locale keys.
		$supported_locales = [
			'af',
			'ar',
			'az',
			'bg',
			'bn',
			'bs',
			'ca',
			'cs',
			'da',
			'de',
			'dsb',
			'el',
			'en',
			'eo',
			'es',
			'et',
			'eu',
			'fa',
			'fi',
			'fr',
			'gl',
			'he',
			'hi',
			'hr',
			'hsb',
			'hu',
			'hy',
			'id',
			'is',
			'it',
			'ja',
			'ka',
			'km',
			'ko',
			'lt',
			'lv',
			'mk',
			'ms',
			'nb',
			'ne',
			'nl',
			'pa',
			'pl',
			'ps',
			'pt-BR',
			'pt',
			'ro',
			'ru',
			'sk',
			'sl',
			'sq',
			'sr-Cyrl',
			'sr',
			'sv',
			'te',
			'th',
			'tk',
			'tr',
			'uk',
			'vi',
			'zh-CN',
			'zh-TW',
		];

		// Tier 1: High-priority map for specific WordPress locales that differ from Select2's key.
		$locale_map = [
			'pt_BR'       => 'pt-BR',   // Portuguese (Brazil).
			'zh_TW'       => 'zh-TW',   // Chinese (Taiwan).
			'zh_HK'       => 'zh-TW',   // Chinese (Hong Kong) -> Fallback to Traditional Chinese.
			'zh_CN'       => 'zh-CN',   // Chinese (China).
			'sr_RS'       => 'sr-Cyrl', // Serbian (Cyrillic). WP default for sr_RS is Cyrillic.
			'sr_RS@latin' => 'sr',      // Serbian (Latin).
			'nb_NO'       => 'nb',      // Norwegian Bokmål.
		];

		if ( isset( $locale_map[ $wp_locale ] ) ) {
			self::$select2_locale = $locale_map[ $wp_locale ];
			return self::$select2_locale;
		}

		// Tier 2: Fallback to the primary two-letter language code.
		$language_code = substr( $wp_locale, 0, 2 );
		if ( in_array( $language_code, $supported_locales, true ) ) {
			self::$select2_locale = $language_code;
			return self::$select2_locale;
		}

		// Tier 3: No match found.
		self::$select2_locale = false;

		return self::$select2_locale;
	}
}
