<?php
/**
 * Multiple Posts Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Post_Ajax_Field;
use WP_Post;

/**
 * Creates a Select2-based field for selecting multiple posts via AJAX.
 *
 * @see Abstract_Post_Ajax_Field for the AJAX handler implementation.
 */
final class Posts_Field extends Abstract_Post_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'posts';

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
	#[\Override]
	protected function get_initial_values(): array {
		$post_ids = $this->get_value();
		if ( ! is_array( $post_ids ) || 0 === count( $post_ids ) ) {
			return [];
		}

		$posts = get_posts(
			[
				'post__in'    => array_map( 'intval', $post_ids ),
				'post_type'   => 'any',
				'numberposts' => -1,
				'orderby'     => 'post__in',
			]
		);

		return array_values(
			array_map(
				static fn( WP_Post $post ) => [
					'id'   => $post->ID,
					'text' => $post->post_title,
				],
				$posts
			)
		);
	}
}
