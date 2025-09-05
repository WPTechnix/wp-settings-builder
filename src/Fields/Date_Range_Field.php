<?php
/**
 * Date Range Field Class using Flatpickr.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Flatpickr_Trait;
use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field;

/**
 * Date Range Field Class
 *
 * Renders a text input for display, backed by a hidden input for storage.
 */
final class Date_Range_Field extends Abstract_Field {

	use Has_Flatpickr_Trait;

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'date_range';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $css_handles = [ 'flatpickr-css', 'flatpickr-airbnb-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $js_handles = [ 'flatpickr-js' ];

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
	private const DEFAULT_DISPLAY_FORMAT = 'M j, Y';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$php_display_format = $this->get_display_format();
		$js_display_format  = self::translate_php_to_flatpickr_format( $php_display_format );
		$storage_value      = $this->get_value();
		$default_date       = wp_json_encode( $storage_value );

		$base_options = [
			'mode'          => 'range',
			'dateFormat'    => self::translate_php_to_flatpickr_format( self::STORAGE_FORMAT ),
			'disableMobile' => true,
		];

		$user_options       = $this->get_extra( 'flatpickr_options', [] );
		$merged_options     = array_merge( $user_options, $base_options );
		$placeholder_string = $this->get_extra( 'placeholder', $php_display_format . ' to ' . $php_display_format );

		$hidden_input = sprintf(
			'<input type="hidden" id="%s" name="%s" value="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( (string) wp_json_encode( $storage_value ) )
		);

		// The visible input's `value` is now empty. The JS `onReady` event will populate it.
		$visible_input = sprintf(
			'<input type="text" id="%s_visible" value="" readonly="readonly" class="regular-text wptx-date-range-picker" data-options="%s" data-hidden-id="%s" data-display-format="%s" placeholder="%s" data-default-date="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( (string) wp_json_encode( $merged_options ) ),
			esc_attr( $this->get_id() ),
			esc_attr( $js_display_format ), // Pass translated format to JS.
			esc_attr( $placeholder_string ),
			esc_attr( (string) $default_date )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $hidden_input . $visible_input;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
    if (typeof flatpickr !== 'function') { return; }

    $('.wptx-date-range-picker').each(function() {
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
	 * @return string[]|null
	 */
	public function get_value(): ?array {
		$value = parent::get_value();
		return is_array( $value ) && 2 === count( $value ) ? array_values( array_map( 'strval', $value ) ) : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string[]|null
	 */
	public function get_default_value(): ?array {
		$default_value = parent::get_default_value();
		return is_array( $default_value ) && 2 === count( $default_value ) ? array_values( array_map( 'strval', $default_value ) ) : null;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @return string[]|null
	 */
	public function sanitize( mixed $value ): ?array {
		if ( ! is_string( $value ) || 'null' === $value || '' === $value ) {
			return null;
		}

		$decoded = json_decode( $value, true );

		if ( ! is_array( $decoded ) || 2 !== count( $decoded ) ) {
			return $this->get_default_value();
		}

		list($start_date, $end_date) = array_values( $decoded );

		if (
			is_string( $start_date ) && self::validate_date_string( $start_date, self::STORAGE_FORMAT ) &&
			is_string( $end_date ) && self::validate_date_string( $end_date, self::STORAGE_FORMAT )
		) {
			return [ $start_date, $end_date ];
		}

		return $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_use_inline_title_as_label(): bool {
		return true;
	}

	/**
	 * Gets the display format (in PHP format) from extras.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	private function get_display_format(): string {
		$format = $this->get_extra( 'display_format' );
		if ( is_string( $format ) && ! empty( $format ) ) {
			return $format;
		}
		return self::DEFAULT_DISPLAY_FORMAT;
	}
}
