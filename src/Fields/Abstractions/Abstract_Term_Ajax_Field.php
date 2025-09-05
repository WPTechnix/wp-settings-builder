<?php
/**
 * Abstract base class for Term-related AJAX Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Abstractions
 * @since 1.0.0
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Abstractions;

/**
 * Provides the shared AJAX logic for querying WordPress terms.
 *
 * @since 1.0.0
 */
abstract class Abstract_Term_Ajax_Field extends Abstract_Ajax_Select_Field {

	/**
	 * Primary AJAX action name for this field type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_terms';

	/**
	 * {@inheritDoc}
	 */
	public static function get_ajax_actions(): array {
		return [ self::get_ajax_action_name() => 'ajax_get_terms' ];
	}

	/**
	 * Handles the AJAX request for fetching terms.
	 *
	 * Merges default query arguments with user-supplied arguments from the field definition.
	 * Responds with a JSON object compatible with Select2's AJAX API. The request
	 * exits via wp_send_json_success() on completion.
	 */
	public static function ajax_get_terms(): void {
		check_ajax_referer( 'wptx-ajax-select-' . self::get_ajax_action_name(), '_ajax_nonce' );

		$search    = sanitize_text_field( wp_unslash( $_REQUEST['q'] ?? '' ) );
		$page      = max( 1, absint( $_REQUEST['page'] ?? 1 ) );
		$user_args = isset( $_REQUEST['query_args'] ) && is_array( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : [];

		$default_args = [
			'taxonomy'   => 'category',
			'search'     => $search,
			'number'     => 10,
			'paged'      => $page,
			'hide_empty' => false,
		];

		$query_args           = wp_parse_args( $user_args, $default_args );
		$query_args['fields'] = 'id=>name'; // Must be id=>name for this implementation.

		$terms = get_terms( $query_args );
		if ( is_wp_error( $terms ) ) {
			wp_send_json_success(
				[
					'items' => [],
					'more'  => false,
				]
			);
		}

		$items = [];
		foreach ( $terms as $term_id => $term_name ) {
			$items[] = [
				'id'   => $term_id,
				'text' => $term_name,
			];
		}

		if ( isset( $query_args['taxonomy'] ) && is_string( $query_args['taxonomy'] ) ) {
			$total_terms = get_terms( array_merge( $query_args, [ 'count' => true ] ) );
			$total_terms = is_wp_error( $total_terms ) ? 0 : $total_terms;
		} else {
			$total_terms = 0;
		}

		$more = ( $page * $query_args['number'] ) < $total_terms;

		wp_send_json_success(
			[
				'items' => $items,
				'more'  => $more,
			]
		);
	}
}
