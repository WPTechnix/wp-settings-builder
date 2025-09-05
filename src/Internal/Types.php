<?php

/**
 * Defines shared PHPStan types for the library to ensure type safety and consistency.
 *
 * This file does not contain any executable code but is essential for static analysis.
 *
 * @package WPTechnix\WP_Settings_Builder\Internal
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Internal;

/**
 * A container for PHPStan type definitions. This class is not meant to be instantiated.
 *
 * @phpstan-type Field_Extras array<string, mixed>
 *
 * @phpstan-type Field_Config array{
 *  id: non-empty-string,
 *  title: non-empty-string,
 *  section: non-empty-string,
 *  type: non-empty-string,
 *  name: non-empty-string,
 *  extras: Field_Extras
 * }
 *
 * @phpstan-type Section_Config array{
 *  id: non-empty-string,
 *  title: non-empty-string,
 *  description: non-empty-string|null,
 *  tab: non-empty-string|null
 * }
 *
 * @phpstan-type Tab_Config array{
 *  id: non-empty-string,
 *  title: non-empty-string,
 *  icon: string
 * }
 *
 * @phpstan-type Asset array{
 *   handle: non-empty-string,
 *   type: 'css'|'js',
 *   src: string,
 *   version?: false|null|non-empty-string,
 *   dependencies?: non-empty-string[],
 * }
 *
 * @phpstan-type Fields_Map array<non-empty-string, Field_Config>
 * @phpstan-type Sections_Map array<non-empty-string, Section_Config>
 * @phpstan-type Tabs_Map array<non-empty-string, Tab_Config>
 */
final class Types {

	/**
	 * This class is a virtual container for types and should not be instantiated.
	 */
	private function __construct() {
	}
}
