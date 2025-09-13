<?php
/**
 * Abstract base class for Term-related AJAX Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Common
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Common;

/**
 * Provides the shared AJAX logic for querying WordPress terms.
 */
abstract class Abstract_Term_Ajax_Field extends Abstract_Ajax_Select_Field {

	/**
	 * Primary AJAX action name for this field type.
	 *
	 * @var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_terms';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
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

		$search    = isset( $_REQUEST['q'] ) && is_string( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
		$page      = isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) ? (int) max( 1, $_REQUEST['page'] ) : 1;
		$user_args = isset( $_REQUEST['query_args'] ) && is_array( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : [];

		$default_args = [
			'taxonomy'   => 'category',
			'search'     => $search,
			'number'     => 10,
			'hide_empty' => false,
		];

		$query_args = wp_parse_args( $user_args, $default_args );
		$query_args = array_merge(
			$query_args,
			[
				'fields' => 'id=>name',
				'offset' => ( $page - 1 ) * max( 0, intval( $query_args['number'] ) ),
			]
		);

		$terms = get_terms( $query_args );
		if ( ! is_array( $terms ) ) {
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

		$count_args = $query_args;
		unset( $count_args['number'] );
		unset( $count_args['offset'] );

		$total_terms = wp_count_terms( $count_args );
		$total_terms = is_numeric( $total_terms ) ? absint( $total_terms ) : 0;

		$more = ( $page * $query_args['number'] ) < $total_terms;

		wp_send_json_success(
			[
				'items' => $items,
				'more'  => $more,
			]
		);
	}
}
