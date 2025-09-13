<?php
/**
 * Password Field Class
 *
 * Handles sensitive inputs like API keys, ensuring values are not exposed in HTML
 * and are not accidentally erased on save.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Password Field Class
 *
 * This class provides a secure and user-friendly way to handle password and API key fields.
 * 1.  It never renders the saved value directly into the HTML.
 * 2.  It displays a placeholder of asterisks (*) with the same length as the saved value,
 *     giving the user a visual confirmation that a value is set and an idea of its length.
 * 3.  The sanitization logic is robust, preventing accidental erasure by recognizing the
 *     placeholder as a "no-change" signal.
 */
final class Password_Field extends Text_Field {

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'password';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$current_value = $this->get_value();
		$display_value = '';

		// If a value is already saved, create a placeholder of equal length.
		if ( '' !== $current_value ) {
			$display_value = str_repeat( '*', strlen( $current_value ) );
		}

		printf(
			'<input type="password" id="%s" name="%s" value="%s" autocomplete="new-password" %s />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $display_value ),
			$this->get_extra_html_attributes_string( [ 'class' => 'regular-text' ] ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	/**
	 * Sanitizes the password field value with dynamic placeholder detection.
	 *
	 * This method contains critical logic to prevent accidental data loss when using a
	 * variable-length placeholder. It assumes a "no-change" state if and only if:
	 * 1. An old value already exists.
	 * 2. The submitted value consists *exclusively* of asterisks.
	 * 3. The length of the submitted asterisk string exactly matches the length of the old value.
	 *
	 * If these conditions are met, the old value is preserved. Otherwise, the submitted
	 * value is taken as the new intended value, allowing for both updates and clearing.
	 *
	 * @param mixed $value The raw input value from the form submission.
	 *
	 * @return string|null The sanitized value ready for persistence.
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {

		if ( ! is_scalar( $value ) ) {
			return null;
		}

		$submitted_value = (string) $value;

		$old_value = $this->get_value();

		// Check for the "no-change" signal. This is our key logic.
		if (
			// 1. There must be an old value to compare against.
			'' !== $old_value &&
			// 2. The submitted value must consist *only* of one or more asterisks (more idiomatic check).
			1 === preg_match( '/^\*+$/', $submitted_value ) &&
			// 3. The length of the submitted placeholder must exactly match the old value's length.
			strlen( $submitted_value ) === strlen( $old_value )
		) {
			// The user did not change the field. Return the original value to preserve it.
			return $old_value;
		}

		// If the conditions are not met, the user has intentionally submitted a new value.
		// This could be a new key, or an empty string to clear the key. We honor this intent.
		// Passwords should not be sanitized further than casting to a string.
		return $submitted_value;
	}
}
