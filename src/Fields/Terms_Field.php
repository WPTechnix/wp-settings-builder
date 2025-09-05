<?php
/**
 * Multiple Terms Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Term_Ajax_Field;
use WP_Term;

/**
 * Creates a Select2-based field for selecting multiple terms via AJAX.
 *
 * @see Abstract_Term_Ajax_Field for the AJAX handler implementation.
 */
final class Terms_Field extends Abstract_Term_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'terms';

	/**
	 * Whether the field allows multiple selections.
	 *
	 * This must be defined in the concrete child class.
	 *
	 * @var bool
	 */
	protected bool $is_multiple = true;

	/**
	 * {@inheritDoc}
	 */
	protected function get_initial_values(): array {
		$term_ids = $this->get_value();
		if ( empty( $term_ids ) || ! is_array( $term_ids ) ) {
			return [];
		}

		$terms = get_terms(
			[
				'include'    => array_map( 'absint', $term_ids ),
				'hide_empty' => false,
				'orderby'    => 'include',
			]
		);

		return is_array( $terms ) ? array_values(
			array_map(
				static fn( WP_Term $term ) => [
					'id'   => $term->term_id,
					'text' => $term->name,
				],
				$terms
			)
		) : [];
	}
}
