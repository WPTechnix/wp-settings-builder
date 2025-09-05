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
 * @phpstan-require-extends \WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field
 */
trait Has_Flatpickr_Trait {

	/**
	 * {@inheritDoc}
	 */
	public static function get_asset_definitions(): array {
		$base_url        = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/';
		$themes_base_url = 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/themes/';

		return [
			[
				'handle'  => 'flatpickr-css',
				'type'    => 'css',
				'src'     => $base_url . 'flatpickr.min.css',
				'version' => '4.6.13',
			],
			[
				'handle'       => 'flatpickr-airbnb-css',
				'type'         => 'css',
				'src'          => $themes_base_url . 'airbnb.min.css',
				'dependencies' => [ 'flatpickr-css' ],
				'version'      => '4.6.13',
			],
			[
				'handle'       => 'flatpickr-js',
				'type'         => 'js',
				'src'          => $base_url . 'flatpickr.min.js',
				'dependencies' => [],
				'version'      => '4.6.13',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-date-picker,
.wptx-time-picker,
.wptx-datetime-picker,
.flatpickr-input {
	background: #fff url('data:image/svg+xml,<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V12Z" stroke="currentColor" stroke-width="1.5"/><path opacity="0.5" d="M7 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path opacity="0.5" d="M17 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path opacity="0.5" d="M2.5 9H21.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18 17C18 17.5523 17.5523 18 17 18C16.4477 18 16 17.5523 16 17C16 16.4477 16.4477 16 17 16C17.5523 16 18 16.4477 18 17Z" fill="currentColor"/><path d="M18 13C18 13.5523 17.5523 14 17 14C16.4477 14 16 13.5523 16 13C16 12.4477 16.4477 12 17 12C17.5523 12 18 12.4477 18 13Z" fill="currentColor"/><path d="M13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16C12.5523 16 13 16.4477 13 17Z" fill="currentColor"/><path d="M13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.5523 12 13 12.4477 13 13Z" fill="currentColor"/><path d="M8 17C8 17.5523 7.55228 18 7 18C6.44772 18 6 17.5523 6 17C6 16.4477 6.44772 16 7 16C7.55228 16 8 16.4477 8 17Z" fill="currentColor"/><path d="M8 13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13C6 12.4477 6.44772 12 7 12C7.55228 12 8 12.4477 8 13Z" fill="currentColor"/></svg>') no-repeat 98% center !important;
}
.flatpickr-input:not([readonly]) {
	cursor: text;
}

CSS;
	}


	/**
	 * Validates a date string against a given format.
	 *
	 * @param string $date_string The date string to validate.
	 * @param string $format      The expected date format.
	 *
	 * @phpstan-param non-empty-string $format
	 *
	 * @return bool True if valid, false otherwise.
	 */
	private static function validate_date_string( string $date_string, string $format ): bool {
		$date_object = DateTime::createFromFormat( $format, $date_string );

		// The createFromFormat function can successfully parse invalid dates (e.g., 2023-02-31 becomes 2023-03-03).
		// This check ensures that the parsed date, when re-formatted, matches the original input string.
		return ! empty( $date_object ) && $date_object->format( $format ) === $date_string;
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
}
