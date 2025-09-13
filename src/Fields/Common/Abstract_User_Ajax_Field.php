<?php
/**
 * Abstract base class for User-related AJAX Select2 fields.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Common
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Common;

use WP_User;
use WP_User_Query;

/**
 * Provides the shared AJAX logic for querying WordPress users.
 */
abstract class Abstract_User_Ajax_Field extends Abstract_Ajax_Select_Field {

	/**
	 * Primary AJAX action name for this field type.
	 *
	 * @var non-empty-string
	 */
	protected static string $primary_ajax_action = 'get_users';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_ajax_actions(): array {
		return [ self::get_ajax_action_name() => 'ajax_get_users' ];
	}

	/**
	 * Handles the AJAX request for fetching users.
	 *
	 * Merges default query arguments with user-supplied arguments from the field definition.
	 * Responds with a JSON object compatible with Select2's AJAX API. The request
	 * exits via wp_send_json_success() on completion.
	 */
	public static function ajax_get_users(): void {
		check_ajax_referer( 'wptx-ajax-select-' . self::get_ajax_action_name(), '_ajax_nonce' );

		$search    = isset( $_REQUEST['q'] ) && is_string( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
		$page      = isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) ? (int) max( 1, $_REQUEST['page'] ) : 1;
		$user_args = isset( $_REQUEST['query_args'] ) && is_array( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : [];

		$default_args = [
			'search'         => '*' . esc_attr( $search ) . '*',
			'paged'          => $page,
			'number'         => 10,
			'search_columns' => [ 'ID', 'user_login', 'user_email', 'user_nicename' ],
		];

		$query_args           = wp_parse_args( $user_args, $default_args );
		$query_args['fields'] = 'all'; // Required for WP_User objects.

		$query = new WP_User_Query( $query_args );

		$items = array_map(
			static fn( WP_User $user ) => [
				'id'   => $user->ID,
				'text' => self::user_display_name( $user ),
			],
			$query->get_results()
		);

		$more = ( ( $page * $query_args['number'] ) < $query->get_total() );

		wp_send_json_success(
			[
				'items' => $items,
				'more'  => $more,
			]
		);
	}

	/**
	 * Generates a display name for a user.
	 *
	 * Provides a fallback mechanism for generating a user-friendly display name,
	 * prioritizing display_name, then full name, and finally the login name.
	 *
	 * @param WP_User $user The user object.
	 *
	 * @return string The user's display name.
	 */
	protected static function user_display_name( WP_User $user ): string {
		$display_name = $user->display_name;

		if ( '' === trim( $display_name ) ) {
			$display_name = trim( sprintf( '%s %s', $user->first_name, $user->last_name ) );
		}

		if ( '' === $display_name ) {
			$display_name = $user->user_login;
		}

		return $display_name;
	}
}
