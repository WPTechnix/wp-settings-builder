<?php
/**
 * Defines the contract for a service that persists and retrieves settings data.
 *
 * @package WPTechnix\WP_Settings_Builder\Interfaces
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Interfaces;

/**
 * Interface Persistence_Interface
 */
interface Persistence_Interface {

	/**
	 * Store the full data array into persistence
	 *
	 * @param array<array-key,mixed> $data The data to store.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function store( array $data ): bool;

	/**
	 * Retrieve a single setting by key
	 *
	 * @param non-empty-string $key The setting key.
	 * @param mixed            $default_value The fallback if the key does not exist.
	 *
	 * @return mixed The stored value or the default.
	 */
	public function get( string $key, mixed $default_value = null ): mixed;

	/**
	 * Set a single setting by key
	 *
	 * @param non-empty-string $key   The setting key.
	 * @param mixed            $value The value to set.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function set( string $key, mixed $value ): bool;

	/**
	 * Load all stored settings
	 *
	 * @param bool $force If true, forces reloading from storage.
	 *
	 * @return array<array-key,mixed> The stored settings.
	 */
	public function load( bool $force = false ): array;
}
