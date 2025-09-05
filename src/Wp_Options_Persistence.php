<?php
/**
 * Class Wp_Options_Persistence
 *
 * This class isolates all database interactions to the WordPress Options API.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Persistence_Interface;

/**
 * Class Wp_Options_Persistence
 *
 * This class isolates all database interactions to the WordPress Options API.
 */
final class Wp_Options_Persistence implements Persistence_Interface {

	/**
	 * Cached settings.
	 *
	 * @var array<string, mixed>
	 */
	private array $storage;

	/**
	 * Constructor.
	 *
	 * @param string $option_name The key for storing all settings data in the `wp_options` table.
	 *
	 * @phpstan-param non-empty-string $option_name
	 */
	public function __construct(
		private string $option_name
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function store( array $data ): bool {
		$this->storage = $data;
		return update_option( $this->option_name, $data );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( string $key, mixed $default_value = null ): mixed {
		$settings = $this->load();
		return $settings[ $key ] ?? $default_value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( string $key, mixed $value ): bool {
		$settings         = $this->load();
		$settings[ $key ] = $value;
		return $this->store( $settings );
	}

	/**
	 * {@inheritdoc}
	 */
	public function load( bool $force = false ): array {
		if ( $force || ! isset( $this->storage ) ) {
			$options       = get_option( $this->option_name, [] );
			$this->storage = is_array( $options ) ? $options : [];
		}

		return $this->storage;
	}
}
