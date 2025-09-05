<?php
/**
 * Abstract base class for AJAX-powered Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Abstractions
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Abstractions;

use InvalidArgumentException;
use WPTechnix\WP_Settings_Builder\Fields\Traits\Has_Ajax_Select2_Trait;

/**
 * Provides the core rendering and sanitization logic for any AJAX-based Select2 field.
 */
abstract class Abstract_Ajax_Select_Field extends Abstract_Field {

	use Has_Ajax_Select2_Trait;

	/**
	 * Primary AJAX action name for this field type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_items';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $css_handles = [ 'select2-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var array
	 *
	 * @phpstan-var list<non-empty-string>
	 */
	protected static $js_handles = [ 'select2-js' ];

	/**
	 * Whether the field allows multiple selections.
	 *
	 * This must be defined in the concrete child class.
	 *
	 * @var bool
	 */
	protected bool $is_multiple = false;

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvalidArgumentException When primary AJAX action name is not defined.
	 */
	public function render(): void {
		$ajax_action = static::get_ajax_action_name();

		if ( empty( $ajax_action ) ) {
			throw new InvalidArgumentException( 'Primary AJAX action name is not defined.' );
		}

		$nonce          = wp_create_nonce( 'wptx-ajax-select-' . $ajax_action );
		$query_args     = $this->get_extra( 'query_args', [] );
		$initial_values = $this->get_initial_values();

		$default_attributes = [
			'class'           => 'regular-text wptx-ajax-select2',
			'data-action'     => 'wptx_' . $ajax_action,
			'data-nonce'      => $nonce,
			'data-query-args' => wp_json_encode( $query_args ),
		];

		if ( $this->is_multiple ) {
			$default_attributes['multiple'] = 'multiple';
			$field_name                     = $this->get_name() . '[]';
		} else {
			$field_name                               = $this->get_name();
			$default_attributes['data-initial-value'] = wp_json_encode( $initial_values[0] ?? null );
		}

		printf(
			'<select id="%s" name="%s" %s>',
			esc_attr( $this->get_id() ),
			esc_attr( $field_name ),
			$this->get_extra_html_attributes_string( $default_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		// For multiple-select fields, pre-populate the <option> tags.
		if ( $this->is_multiple && ! empty( $initial_values ) ) {
			foreach ( $initial_values as $item ) {
				printf(
					'<option value="%s" selected="selected">%s</option>',
					esc_attr( (string) $item['id'] ),
					esc_html( $item['text'] )
				);
			}
		}

		echo '</select>';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): mixed {
		$value = parent::get_value();
		if ( $this->is_multiple ) {
			return is_array( $value ) ? $value : null;
		}
		return is_scalar( $value ) ? $value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): mixed {
		$default_value = parent::get_default_value();
		if ( $this->is_multiple ) {
			return is_array( $default_value ) ? $default_value : null;
		}
		return is_scalar( $default_value ) ? $default_value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): mixed {
		if ( $this->is_multiple ) {
			if ( ! is_array( $value ) ) {
				return [];
			}
			return array_values( array_unique( array_map( 'absint', $value ) ) );
		}
		return ! empty( $value ) ? absint( $value ) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_use_inline_title_as_label(): bool {
		return true;
	}

	/**
	 * Retrieves the initial selected values to pre-populate the field.
	 *
	 * This must be implemented by the concrete child class.
	 *
	 * @return array
	 *
	 * @phpstan-return list<array{id: int, text: string}>
	 */
	abstract protected function get_initial_values(): array;

	/**
	 * Gets the primary AJAX action name for this field type.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	protected static function get_ajax_action_name(): string {
		return static::$primary_ajax_action;
	}
}
