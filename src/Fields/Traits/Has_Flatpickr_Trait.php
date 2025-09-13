<?php
/**
 * Provides common functionality for fields that use the Flatpickr library.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Traits
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Traits;

use DateTime;

/**
 * Provides common functionality for fields using the Flatpickr library.
 *
 * @require-extends \WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
trait Has_Flatpickr_Trait {

	/**
	 * Flatpickr library version.
	 *
	 * @var non-empty-string
	 */
	private static string $flatpickr_version = '4.6.13';

	/**
	 * Flatpickr locale key.
	 *
	 * @var non-empty-string|false|null
	 */
	private static null|false|string $flatpickr_locale = null;

	/**
	 * Get the asset definitions for this field.
	 *
	 * @return array
	 * @phpstan-return list<Asset>
	 * @psalm-return list<Asset>
	 */
	#[\Override]
	public static function get_asset_definitions(): array {
		$base_url = 'https://cdn.jsdelivr.net/npm/flatpickr@' . self::$flatpickr_version . '/dist/';

		// Define the core assets.
		$assets = [
			[
				'handle'  => 'flatpickr-css',
				'type'    => 'css',
				'src'     => "{$base_url}flatpickr.min.css",
				'version' => self::$flatpickr_version,
			],
			[
				'handle'       => 'flatpickr-airbnb-css',
				'type'         => 'css',
				'src'          => "{$base_url}themes/airbnb.min.css",
				'dependencies' => [ 'flatpickr-css' ],
				'version'      => self::$flatpickr_version,
			],
			[
				'handle'       => 'flatpickr-js',
				'type'         => 'js',
				'src'          => "{$base_url}flatpickr.min.js",
				'dependencies' => [],
				'version'      => self::$flatpickr_version,
			],
			[
				'handle'       => 'flatpickr-locale',
				'type'         => 'js',
				'src'          => function () use ( $base_url ): string|false {
					$locale = self::get_flatpickr_locale();
					if ( false !== $locale ) {
						return "{$base_url}l10n/{$locale}.min.js";
					}
					return false;
				},
				'dependencies' => [ 'flatpickr-js' ],
				'version'      => self::$flatpickr_version,
			],
		];

		return $assets;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-date-picker,
.wptx-time-picker,
.wptx-datetime-picker,
.flatpickr-input {
	background: #fff url('data:image/svg+xml,<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V12Z" stroke="currentColor" stroke-width="1.5"/><path opacity="0.5" d="M7 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path opacity="0.5" d="M17 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path opacity="0.5" d="M2.5 9H21.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18 17C18 17.5523 17.5523 18 17 18C16.4477 18 16 17.5523 16 17C16 16.4477 16.4477 16 17 16C17.5523 16 18 16.4477 18 17Z" fill="currentColor"/><path d="M18 13C18 13.5523 17.5523 14 17 14C16.4477 14 16 13.5523 16 13C16 12.4477 16.4477 12 17 12C17.5523 12 18 12.4477 18 13Z" fill="currentColor"/><path d="M13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16C12.5523 16 13 16.4477 13 17Z" fill="currentColor"/><path d="M13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.5523 12 13 12.4477 13 13Z" fill="currentColor"/><path d="M8 17C8 17.5523 7.55228 18 7 18C6.44772 18 6 17.5523 6 17C6 16.4477 6.44772 16 7 16C7.55228 16 8 16.4477 8 17Z" fill="currentColor"/><path d="M8 13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13C6 12.4477 6.44772 12 7 12C7.55228 12 8 12.4477 8 13Z" fill="currentColor"/></svg>') no-repeat right center !important;
	background-position: right 0.75rem center !important;
}

[dir="rtl"] .wptx-date-picker,
[dir="rtl"] .wptx-time-picker,
[dir="rtl"] .wptx-datetime-picker,
[dir="rtl"] .flatpickr-input {
	background-position: left 0.75rem center !important;
}

.flatpickr-input:not([readonly]) {
	cursor: text;
}

CSS;
	}

	/**
	 * Validates a date string against a given format.
	 *
	 * @param mixed            $date_string The date string to validate.
	 * @param non-empty-string $format      The expected date format.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	private static function validate_date_string( mixed $date_string, string $format ): bool {
		if ( ! is_string( $date_string ) || '' === $date_string ) {
			return false;
		}

		$date_object = DateTime::createFromFormat( $format, $date_string );

		// The createFromFormat function can successfully parse invalid dates (e.g., 2023-02-31 becomes 2023-03-03).
		// This check ensures that the parsed date, when re-formatted, matches the original input string.
		return $date_object instanceof DateTime && $date_object->format( $format ) === $date_string;
	}

	/**
	 * Validates a time string against a given format.
	 *
	 * @param mixed            $time_string The time string to validate.
	 * @param non-empty-string $format      The expected time format.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	private function validate_time_string( mixed $time_string, string $format ): bool {
		return is_string( $time_string ) && self::validate_date_string( gmdate( 'Y-m-d' ) . ' ' . $time_string, 'Y-m-d ' . $format );
	}

	/**
	 * Validate a date range array against a given format.
	 *
	 * @param mixed            $date_range The date range array to validate.
	 * @param non-empty-string $format The expected date format.
	 *
	 * @phpstan-assert array{0: non-empty-string, 1: non-empty-string} $date_range
	 * @psalm-assert array{0: non-empty-string, 1: non-empty-string} $date_range
	 */
	private static function validate_date_range( mixed $date_range, string $format ): bool {

		return is_array( $date_range ) &&
				2 === count( $date_range ) &&
				isset( $date_range[0] ) && self::validate_date_string( $date_range[0], $format ) &&
				isset( $date_range[1] ) && self::validate_date_string( $date_range[1], $format );
	}

	/**
	 * Translates a PHP date format string to a Flatpickr-compatible format string.
	 *
	 * @param string $php_format The PHP date format string.
	 *
	 * @return string The Flatpickr date format string.
	 */
	private static function translate_php_to_flatpickr_format( string $php_format ): string {
		$replacements = [
			// Day.
			'd' => 'd', // Day of the month, 2 digits with leading zeros (01 to 31).
			'j' => 'j', // Day of the month without leading zeros (1 to 31).
			'D' => 'D', // A textual representation of a day, three letters (Mon through Sun).
			'l' => 'l', // A full textual representation of the day of the week (Sunday through Saturday).
			'w' => 'w', // Numeric representation of the day of the week (0 (for Sunday) through 6 (for Saturday)).
			// Month.
			'm' => 'm', // Numeric representation of a month, with leading zeros (01 through 12).
			'n' => 'n', // Numeric representation of a month, without leading zeros (1 through 12).
			'F' => 'F', // A full textual representation of a month (January through December).
			'M' => 'M', // A short textual representation of a month, three letters (Jan through Dec).
			// Year.
			'Y' => 'Y', // A full numeric representation of a year, 4 digits (e.g. 1999 or 2003).
			'y' => 'y', // A two digit representation of a year (e.g. 99 or 03).
			// Time.
			'H' => 'H', // 24-hour format of an hour with leading zeros (00 through 23).
			'G' => 'J', // 24-hour format of an hour without leading zeros (0 through 23) -> Flatpickr uses J.
			'h' => 'h', // 12-hour format of an hour with leading zeros (01 through 12).
			'g' => 'h', // 12-hour format of an hour without leading zeros (1 through 12) -> Flatpickr uses h for both.
			'i' => 'i', // Minutes with leading zeros (00 to 59).
			's' => 'S', // Seconds with leading zeros (00 through 59) -> Flatpickr uses S.
			'A' => 'K', // Uppercase Ante meridiem and Post meridiem (AM or PM) -> Flatpickr uses K.
			'a' => 'K', // Lowercase Ante meridiem and Post meridiem (am or pm) -> Flatpickr uses K.
		];

		// Use strtr for efficient single-character replacement.
		return strtr( $php_format, $replacements );
	}


	/**
	 * Determines the appropriate Flatpickr locale key from the WordPress locale.
	 *
	 * Implements a multi-tiered fallback system:
	 * 1. Checks for specific, high-priority mappings (e.g., de_AT -> at).
	 * 2. Falls back to the generic two-letter language code if available (e.g., fr_CA -> fr).
	 * 3. Returns null if no match is found.
	 *
	 * @return non-empty-string|false The locale key for Flatpickr, or false if not needed/supported.
	 */
	private static function get_flatpickr_locale(): string|false {

		if ( null !== self::$flatpickr_locale ) {
			return self::$flatpickr_locale;
		}

		$wp_locale = get_user_locale();

		// English is the default, no translation needed.
		if ( str_starts_with( $wp_locale, 'en_' ) ) {
			self::$flatpickr_locale = false;
			return false;
		}

		// The complete list of available Flatpickr locale keys.
		$supported_locales = [
			'ar-dz',
			'ar',
			'at',
			'az',
			'be',
			'bg',
			'bn',
			'bs',
			'cat',
			'ckb',
			'cs',
			'cy',
			'da',
			'de',
			'eo',
			'es',
			'et',
			'fa',
			'fi',
			'fo',
			'fr',
			'ga',
			'gr',
			'he',
			'hi',
			'hr',
			'hu',
			'hy',
			'id',
			'is',
			'it',
			'ja',
			'ka',
			'km',
			'ko',
			'kz',
			'lt',
			'lv',
			'mk',
			'mn',
			'ms',
			'my',
			'nl',
			'nn',
			'no',
			'pa',
			'pl',
			'pt',
			'ro',
			'ru',
			'si',
			'sk',
			'sl',
			'sq',
			'sr-cyr',
			'sr',
			'sv',
			'th',
			'tr',
			'uk',
			'uz',
			'uz_latn',
			'vn',
			'zh-tw',
			'zh',
		];

		// Tier 1: High-priority map for specific WordPress locales.
		$locale_map = [
			'de_AT'       => 'at',      // German (Austria) -> Austrian.
			'zh_TW'       => 'zh-tw',   // Chinese (Taiwan) -> Traditional Chinese.
			'zh_HK'       => 'zh-tw',   // Chinese (Hong Kong) -> Traditional Chinese.
			'zh_CN'       => 'zh',      // Chinese (China) -> Simplified Chinese.
			'ar_DZ'       => 'ar-dz',   // Arabic (Algeria).
			'sr_RS'       => 'sr-cyr',  // Serbian (Cyrillic). Note: WP default for sr_RS is Cyrillic.
			'sr_RS@latin' => 'sr',      // Serbian (Latin).
			'uz_UZ@latin' => 'uz_latn', // Uzbek (Latin).
			'nn_NO'       => 'nn',      // Norwegian Nynorsk.
			'nb_NO'       => 'no',      // Norwegian Bokmål.
		];

		if ( isset( $locale_map[ $wp_locale ] ) ) {
			self::$flatpickr_locale = $locale_map[ $wp_locale ];
			return self::$flatpickr_locale;
		}

		// Tier 2: Fallback to the primary two-letter language code.
		$language_code = substr( $wp_locale, 0, 2 );
		if ( in_array( $language_code, $supported_locales, true ) ) {
			self::$flatpickr_locale = $language_code;
			return self::$flatpickr_locale;
		}

		// Tier 3: No match found.
		self::$flatpickr_locale = false;
		return self::$flatpickr_locale;
	}
}
