<?php
/**
 * Provides common functionality for fields that have a list of options,
 * like 'select' and 'radio'.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Traits
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Traits;

use InvalidArgumentException;

/**
 * Provides common functionality for fields that have a list of options,
 * like 'select' and 'radio'.
 */
trait Has_Choices_Trait {

	/**
	 * Retrieves and validates the options array for the field.
	 *
	 * @return array
	 *
	 * @phpstan-return array<string|int, string|int>
	 *
	 * @throws InvalidArgumentException If options are not a valid array.
	 */
	private function get_options(): array {
		$options = $this->field_config['extras']['options'] ?? null;

		if ( ! is_array( $options ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'The "options" extra must be provided as an array for the "%s" field.',
					$this->field_config['id']
				)
			);
		}
		return $options;
	}

	/**
	 * Checks if a given value is a valid key in the options array.
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool
	 */
	private function is_valid_choice( mixed $value ): bool {
		if ( ! is_scalar( $value ) ) {
			return false;
		}

		try {
			$options = $this->get_options();
			return array_key_exists( (string) $value, $options );
		} catch ( InvalidArgumentException ) {
			return false;
		}
	}
}
