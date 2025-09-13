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
	 * @var null|array<array-key,mixed>
	 */
	private ?array $storage = null;

	/**
	 * Constructor.
	 *
	 * @param non-empty-string $option_name The key for storing all settings data in the `wp_options` table.
	 */
	public function __construct(
		private string $option_name
	) {
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function store( array $data ): bool {
		$this->storage = $data;
		return update_option( $this->option_name, $data );
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function get( string $key, mixed $default_value = null ): mixed {
		$settings = $this->load();
		return $settings[ $key ] ?? $default_value;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function set( string $key, mixed $value ): bool {
		$settings         = $this->load();
		$settings[ $key ] = $value;
		return $this->store( $settings );
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function load( bool $force = false ): array {
		if ( $force || null === $this->storage ) {
			$options       = get_option( $this->option_name, [] );
			$this->storage = is_array( $options ) ? $options : [];
		}

		return $this->storage;
	}
}
