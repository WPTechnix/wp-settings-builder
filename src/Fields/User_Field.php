<?php
/**
 * Single User Selection Field.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_User_Ajax_Field;

/**
 * Creates a Select2-based field for selecting a single user via AJAX.
 *
 * @see Abstract_User_Ajax_Field for the AJAX handler implementation.
 */
final class User_Field extends Abstract_User_Ajax_Field {

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'user';

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
	protected function get_initial_values(): array {
		$user_id = $this->get_value();
		if ( ! is_numeric( $user_id ) ) {
			return [];
		}

		$user = get_user_by( 'id', absint( $user_id ) );

		return ! empty( $user ) ? [
			[
				'id'   => $user->ID,
				'text' => self::user_display_name( $user ),
			],
		] : [];
	}
}
