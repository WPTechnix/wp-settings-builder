<?php
/**
 * Single Term Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Term_Ajax_Field;
use WP_Term;

/**
 * Creates a Select2-based field for selecting a single term via AJAX.
 *
 * @see Abstract_Term_Ajax_Field for the AJAX handler implementation.
 */
final class Term_Field extends Abstract_Term_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'term';

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
	 */
	#[\Override]
	protected function get_initial_values(): array {
		$term_id = $this->get_value();
		if ( ! is_numeric( $term_id ) ) {
			return [];
		}

		$term = get_term( intval( $term_id ) );

		return $term instanceof WP_Term ? [
			[
				'id'   => $term->term_id,
				'text' => $term->name,
			],
		] : [];
	}
}
