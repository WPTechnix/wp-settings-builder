<?php
/**
 * Abstract base class for Post-related AJAX Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Abstractions
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Abstractions;

use WP_Query;
use WP_Post;

/**
 * Provides the shared AJAX logic for querying WordPress posts.
 */
abstract class Abstract_Post_Ajax_Field extends Abstract_Ajax_Select_Field {

	/**
	 * Primary AJAX action name for this field type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_posts';

	/**
	 * {@inheritDoc}
	 */
	public static function get_ajax_actions(): array {
		return [ self::get_ajax_action_name() => 'ajax_get_posts' ];
	}

	/**
	 * Handles the AJAX request for fetching posts.
	 *
	 * Merges default query arguments with user-supplied arguments from the field definition.
	 * Responds with a JSON object compatible with Select2's AJAX API. The request
	 * exits via wp_send_json_success() on completion.
	 */
	public static function ajax_get_posts(): void {
		check_ajax_referer( 'wptx-ajax-select-' . self::get_ajax_action_name(), '_ajax_nonce' );

		$search    = sanitize_text_field( wp_unslash( $_REQUEST['q'] ?? '' ) );
		$page      = max( 1, absint( $_REQUEST['page'] ?? 1 ) );
		$user_args = isset( $_REQUEST['query_args'] ) && is_array( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : [];

		$default_args = [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			's'              => $search,
			'posts_per_page' => 10,
			'paged'          => $page,
		];

		$query_args           = wp_parse_args( $user_args, $default_args );
		$query_args['fields'] = 'all'; // Required for WP_Post objects.

		$query = new WP_Query( $query_args );

		/** @var WP_Post[] $posts */
		$posts = $query->posts;

		$items = array_map(
			static fn( WP_Post $post ) => [
				'id'   => $post->ID,
				'text' => $post->post_title,
			],
			$posts
		);
		unset( $posts );

		$more = ( $query->max_num_pages > $page );

		wp_send_json_success(
			[
				'items' => $items,
				'more'  => $more,
			]
		);
	}
}
