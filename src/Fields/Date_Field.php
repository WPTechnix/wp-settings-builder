<?php
/**
 * Date Field Class using Flatpickr.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Flatpickr_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Date Field Class
 *
 * Renders a text input enhanced with the Flatpickr date picker.
 */
final class Date_Field extends Abstract_Field {

	use Has_Flatpickr_Trait;

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'date';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $css_handles = [ 'flatpickr-css', 'flatpickr-airbnb-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $js_handles = [ 'flatpickr-js', 'flatpickr-locale' ];

	/**
	 * The standardized format for database storage.
	 *
	 * @var string
	 */
	private const STORAGE_FORMAT = 'Y-m-d';

	/**
	 * The default display format (in PHP format) if not specified by the user.
	 *
	 * @var string
	 */
	private const DEFAULT_DISPLAY_FORMAT = 'F j, Y';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$php_display_format = $this->get_display_format();

		$base_options = [
			'altInput'      => true,
			'altFormat'     => self::translate_php_to_flatpickr_format( $php_display_format ),
			'dateFormat'    => self::translate_php_to_flatpickr_format( self::STORAGE_FORMAT ),
			'disableMobile' => true,
		];

		$locale = self::get_flatpickr_locale();
		if ( false !== $locale ) {
			$base_options['locale'] = $locale;
		}

		$user_options   = $this->get_extra( 'flatpickr_options', [] );
		$user_options   = is_array( $user_options ) ? $user_options : [];
		$merged_options = array_merge( $user_options, $base_options );

		$default_attributes = [
			'class'        => 'regular-text wptx-date-picker',
			'data-options' => wp_json_encode( $merged_options ),
		];

		printf(
			'<input type="text" id="%s" name="%s" value="%s" %s />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_value() ),
			$this->get_extra_html_attributes_string( $default_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
    if (typeof flatpickr === 'function') {
        $('.wptx-date-picker').each(function() {
            const options = $(this).data('options') || {};
            flatpickr(this, options);
        });
    }
});
JS;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		return is_string( $default_value ) ? $default_value : '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		return is_string( $value ) ? $value : $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		if ( ! is_string( $value ) ) {
			return null;
		}

		$trimmed_value = trim( $value );
		if ( self::validate_date_string( $trimmed_value, self::STORAGE_FORMAT ) ) {
			return $trimmed_value;
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return true;
	}

	/**
	 * Gets the display format from extras, with a fallback to the default.
	 *
	 * @return non-empty-string
	 */
	private function get_display_format(): string {
		$format = $this->get_extra( 'display_format' );
		if ( is_string( $format ) && '' !== $format ) {
			return $format;
		}
		return self::DEFAULT_DISPLAY_FORMAT;
	}
}
