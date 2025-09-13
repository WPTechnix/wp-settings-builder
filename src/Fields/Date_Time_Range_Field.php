<?php
/**
 * Date Time Range Field Class using Flatpickr.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Flatpickr_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

/**
 * Date Time Range Field Class
 *
 * Renders a text input for display, backed by a hidden input for storage.
 */
final class Date_Time_Range_Field extends Abstract_Field {

	use Has_Flatpickr_Trait;

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'date_time_range';

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
	private const STORAGE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * The default display format (in PHP format) if not specified by the user.
	 *
	 * @var string
	 */
	private const DEFAULT_DISPLAY_FORMAT = 'M j, Y h:i A';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$php_display_format = $this->get_display_format();
		$js_display_format  = self::translate_php_to_flatpickr_format( $php_display_format );
		$js_date            = (string) wp_json_encode( $this->get_value() );

		$base_options = [
			'mode'          => 'range',
			'enableTime'    => true,
			'dateFormat'    => self::translate_php_to_flatpickr_format( self::STORAGE_FORMAT ),
			'disableMobile' => true,
		];

		$locale = self::get_flatpickr_locale();
		if ( false !== $locale ) {
			$base_options['locale'] = $locale;
		}

		$user_options   = $this->get_extra( 'flatpickr_options', [] );
		$merged_options = array_merge( is_array( $user_options ) ? $user_options : [], $base_options );

		$hidden_input = sprintf(
			'<input type="hidden" id="%s" name="%s" value="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $js_date )
		);

		// The visible input's `value` is now empty. The JS `onReady` event will populate it.
		$visible_input = sprintf(
			'<input type="text" id="%s_visible" value="" readonly="readonly" class="regular-text wptx-datetime-range-picker" data-options="%s" data-hidden-id="%s" data-display-format="%s" data-default-date="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( (string) wp_json_encode( $merged_options ) ),
			esc_attr( $this->get_id() ),
			esc_attr( $js_display_format ), // Pass translated format to JS.
			esc_attr( $js_date )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $hidden_input . $visible_input;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
    if (typeof flatpickr !== 'function') { return; }

    $('.wptx-datetime-range-picker').each(function() {
        const visibleInput = $(this);
        const options = visibleInput.data('options') || {};
        const hiddenInput = $('#' + visibleInput.data('hidden-id'));
        const displayFormat = visibleInput.data('display-format');

        options.defaultDate = JSON.parse(visibleInput.attr('data-default-date'));

        const updateInputs = (selectedDates, instance) => {
            if (selectedDates.length === 2) {
                const startDate = instance.formatDate(selectedDates[0], options.dateFormat);
                const endDate = instance.formatDate(selectedDates[1], options.dateFormat);
                hiddenInput.val(JSON.stringify([startDate, endDate]));

                const formattedStart = instance.formatDate(selectedDates[0], displayFormat);
                const formattedEnd = instance.formatDate(selectedDates[1], displayFormat);
                visibleInput.val(formattedStart + ' to ' + formattedEnd);
            }
        };

        options.onReady = (selectedDates, dateStr, instance) => {
            updateInputs(selectedDates, instance);
        };
        options.onChange = (selectedDates, dateStr, instance) => {
            updateInputs(selectedDates, instance);
        };
        options.onClose = (selectedDates, dateStr, instance) => {
            if (visibleInput.val() === '') {
                hiddenInput.val('null');
            }
        };
        flatpickr(visibleInput, options);
    });
});
JS;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array{0:non-empty-string, 1:non-empty-string}|null
	 */
	#[\Override]
	public function get_default_value(): ?array {
		$default_value = parent::get_default_value();
		return self::validate_date_range( $default_value, self::STORAGE_FORMAT ) ? $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array{0:non-empty-string, 1:non-empty-string}|null
	 */
	#[\Override]
	public function get_value(): ?array {
		$value = parent::get_value();
		return self::validate_date_range( $value, self::STORAGE_FORMAT ) ? $value : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array{0:non-empty-string, 1:non-empty-string}|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?array {
		if ( ! is_string( $value ) || 'null' === $value || '' === $value ) {
			return null;
		}

		$decoded = json_decode( $value, true );
		return self::validate_date_range( $decoded, self::STORAGE_FORMAT ) ? $decoded : null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return true;
	}

	/**
	 * Gets the display format (in PHP format) from extras.
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
