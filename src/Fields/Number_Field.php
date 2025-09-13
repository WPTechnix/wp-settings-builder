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
final class Number_Field extends Text_Field {

	/**
	 * Regex Pattern for safe numeric strings
	 *
	 * @var string
	 */
	public const REGEX_PATTERN = '/^-?\d+(\.\d+)?$/';

	/**
	 * Field Type.
	 *
	 * @var non-empty-string
	 */
	protected static string $type = 'number';

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render(): void {
		$this->render_field( 'number' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_default_value(): string {
		$default_value = parent::get_default_value();
		// Stricter check for integer or float-like strings.
		if ( is_numeric( $default_value ) && 1 === preg_match( self::REGEX_PATTERN, $default_value ) ) {
			return $default_value;
		}
		return '';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	#[\Override]
	public function get_value(): string {
		$value = parent::get_value();
		if ( is_numeric( $value ) && 1 === preg_match( self::REGEX_PATTERN, $value ) ) {
			return $value;
		}
		return $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return non-empty-string|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): ?string {
		// Stricter check for integer or float-like strings.
		if ( is_numeric( $value ) && 1 === preg_match( self::REGEX_PATTERN, (string) $value ) ) {
			return (string) $value;
		}
		return null;
	}
}
