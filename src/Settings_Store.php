<?php
/**
 * Responsible for storing and retrieving settings data.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

/**
 * Stores and retrieves settings data.
 */
class Settings_Store {

	/**
	 * Saved settings data.
	 *
	 * @var array
	 *
	 * @phpstan-var array<string, mixed>
	 */
	protected array $saved_settings = [];

	/**
	 * Class Constructor
	 *
	 * @param string $option_name WP Option that store the settings.
	 *
	 * @phpstan-param non-empty-string $option_name
	 */
	public function __construct(
		protected string $option_name
	) {}

	/**
	 * Gets a saved option value from the database.
	 *
	 * This is the primary method for retrieving a field's current value for rendering.
	 * It intelligently falls back to the field's configured 'default' value if no
	 * saved value exists in the database.
	 *
	 * @param string     $key The specific option key (field ID) to retrieve.
	 * @param mixed|null $default_value A final fallback value if no saved option or field default is found.
	 *
	 * @phpstan-param non-empty-string $key
	 *
	 * @return mixed The saved value, the field's default value, or the provided default.
	 */
	public function get( string $key, mixed $default_value = null ): mixed {
		$this->fetch_settings();
		if ( array_key_exists( $key, $this->saved_settings ) ) {
			return $this->saved_settings[ $key ];
		}

		return $default_value;
	}

	/**
	 * Fetch the stored settings from option.
	 *
	 * @param bool $force Force fetch.
	 */
	public function fetch_settings( bool $force = false ): void {
		if ( ! isset( $this->saved_settings ) || $force ) {
			$settings_from_db     = get_option( $this->option_name, [] );
			$this->saved_settings = is_array( $settings_from_db ) ? $settings_from_db : [];
		}
	}

	/**
	 * Get option name.
	 *
	 * @return string
	 * @phpstan-return non-empty-string
	 */
	public function get_option_name(): string {
		return $this->option_name;
	}
}
