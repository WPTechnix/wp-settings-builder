<?php
/**
 * Abstract base class for AJAX-powered Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Common
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Common;

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
	 * @var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_items';

	/**
	 * CSS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $css_handles = [ 'select2-css' ];

	/**
	 * JS handle to enqueue.
	 *
	 * @var list<non-empty-string>
	 */
	protected static array $js_handles = [ 'select2-js', 'select2-locale' ];

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
	#[\Override]
	public function render(): void {
		$ajax_action = static::get_ajax_action_name();

		$nonce          = wp_create_nonce( 'wptx-ajax-select-' . $ajax_action );
		$query_args     = $this->get_extra( 'query_args', [] );
		$initial_values = $this->get_initial_values();

		$default_attributes = [
			'class'           => 'regular-text wptx-ajax-select2',
			'data-action'     => 'wptx_' . $ajax_action,
			'data-nonce'      => $nonce,
			'data-query-args' => (string) wp_json_encode( $query_args ),
		];

		$field_name = $this->is_multiple ? $this->get_name() . '[]' : $this->get_name();

		if ( $this->is_multiple ) {
			$default_attributes['multiple'] = 'multiple';
		}

		printf(
			'<select id="%s" name="%s" %s>',
			esc_attr( $this->get_id() ),
			esc_attr( $field_name ),
			$this->get_extra_html_attributes_string( $default_attributes ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		// For multiple-select fields, pre-populate the <option> tags.
		if ( $this->is_multiple && 0 < count( $initial_values ) ) {
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
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function get_default_value(): int|array|null {
		$default_value = parent::get_default_value();
		if ( $this->is_multiple ) {
			return is_array( $default_value ) ? $this->keep_valid_ids( $default_value ) : [];
		}
		if ( is_numeric( $default_value ) ) {
			$default_value = (int) $default_value;
			return 0 < $default_value ? $default_value : null;
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function get_value(): int|array|null {
		$value = parent::get_value();
		if ( $this->is_multiple ) {
			return is_array( $value ) ? $this->keep_valid_ids( $value ) : $this->get_default_value();
		}
		if ( is_numeric( $value ) ) {
			$value = (int) $value;
			return 0 < $value ? $value : $this->get_default_value();
		}
		return $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): int|array|null {
		if ( $this->is_multiple ) {
			return is_array( $value ) ? $this->keep_valid_ids( $value ) : null;
		}
		if ( is_numeric( $value ) ) {
			$value = (int) $value;
			return 0 < $value ? $value : null;
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
	 * Retrieves the initial selected values to pre-populate the field.
	 *
	 * This must be implemented by the concrete child class.
	 *
	 * @return list<array{id: int, text: string}>
	 */
	abstract protected function get_initial_values(): array;

	/**
	 * Gets the primary AJAX action name for this field type.
	 *
	 * @return non-empty-string
	 */
	protected static function get_ajax_action_name(): string {
		return static::$primary_ajax_action;
	}

	/**
	 * Keeps only valid IDs in the given array.
	 *
	 * @param array<array-key,mixed> $ids Array of IDs.
	 *
	 * @return list<positive-int>
	 */
	private function keep_valid_ids( array $ids ): array {
		return array_values(
			array_unique(
				array_filter(
					array_map( fn( $v ) => is_scalar( $v ) ? (int) $v : 0, $ids ),
					fn( $id ) =>  0 < $id,
				)
			)
		);
	}
}
