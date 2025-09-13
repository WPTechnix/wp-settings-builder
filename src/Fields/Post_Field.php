<?php
/**
 * Single Post Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Post_Ajax_Field;
use WP_Post;

/**
 * Creates a Select2-based field for selecting a single post via AJAX.
 *
 * @see Abstract_Post_Ajax_Field for the AJAX handler implementation.
 */
final class Post_Field extends Abstract_Post_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'post';

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
		$post_id = $this->get_value();
		if ( ! is_numeric( $post_id ) ) {
			return [];
		}

		$post = get_post( intval( $post_id ) );

		return $post instanceof WP_Post ? [
			[
				'id'   => $post->ID,
				'text' => $post->post_title,
			],
		] : [];
	}
}
