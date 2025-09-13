<?php
/**
 * Multiple Users Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_User_Ajax_Field;
use WP_User;

/**
 * Creates a Select2-based field for selecting multiple users via AJAX.
 *
 * @see Abstract_User_Ajax_Field for the AJAX handler implementation.
 */
final class Users_Field extends Abstract_User_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'users';

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
		$user_ids = $this->get_value();
		if ( ! is_array( $user_ids ) || 0 === count( $user_ids ) ) {
			return [];
		}

		$users = get_users( [ 'include' => array_map( 'intval', $user_ids ) ] );

		return array_values(
			array_map(
				static fn( WP_User $user ) => [
					'id'   => $user->ID,
					'text' => self::user_display_name( $user ),
				],
				$users
			)
		);
	}
}
