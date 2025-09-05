<?php
/**
 * Number Field Class
 *
 * @package WPTechnix\WP_Settings_Builder\Fields
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields;

/**
 * Number Field Class
 */
class Number_Field extends Text_Field {

	/**
	 * Regex Pattern for safe numeric strings
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	public const REGEX_PATTERN = '/^-?\d+(\.\d+)?$/';

	/**
	 * Field Type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected static string $type = 'number';

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		$this->render_field( 'number' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_value(): ?string {
		$value = parent::get_value();
		if ( is_numeric( $value ) && ! empty( preg_match( self::REGEX_PATTERN, $value ) ) ) {
			return $value;
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_value(): ?string {
		$default_value = parent::get_default_value();
		// Stricter check for integer or float-like strings.
		if ( is_numeric( $default_value ) && ! empty( preg_match( self::REGEX_PATTERN, $default_value ) ) ) {
			return $default_value;
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sanitize( mixed $value ): ?string {
		// Stricter check for integer or float-like strings.
		if ( is_numeric( $value ) && ! empty( preg_match( self::REGEX_PATTERN, (string) $value ) ) ) {
			return (string) $value;
		}
		return $this->get_default_value();
	}
}
