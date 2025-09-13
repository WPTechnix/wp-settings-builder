<?php
/**
 * Provides a Loader for static assets.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Asset_Loader_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;
use WPTechnix\WP_Settings_Builder\Fields\Code_Editor_Field;
use InvalidArgumentException;

/**
 * Class Asset_Loader
 *
 * @phpstan-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Asset from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Fields_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Asset_Loader implements Asset_Loader_Interface {

	/**
	 * The handle for the main JavaScript file.
	 *
	 * @var string
	 */
	private const MAIN_JS_HANDLE = 'wp-settings-builder-main-js';

	/**
	 * The handle for the main CSS file.
	 *
	 * @var string
	 */
	private const MAIN_CSS_HANDLE = 'wp-settings-builder-main-css';

	/**
	 * Asset Registry.
	 *
	 * @var array
	 * @phpstan-var array<string, Asset>
	 * @psalm-var array<string, Asset>
	 */
	private static array $registry = [];

	/**
	 * An array of registered handles.
	 *
	 * @var list<string>
	 */
	private static array $registered_handles = [];

	/**
	 * Class Constructor
	 *
	 * @param Field_Factory_Interface $field_factory The field factory.
	 */
	public function __construct(
		private Field_Factory_Interface $field_factory
	) {
		self::add_registry(
			[
				'handle'       => self::MAIN_JS_HANDLE,
				'src'          => '',
				'type'         => 'js',
				'dependencies' => [ 'jquery', 'wp-hooks' ],
				'version'      => null,
			]
		);

		self::add_registry(
			[
				'handle'       => self::MAIN_CSS_HANDLE,
				'src'          => '',
				'type'         => 'css',
				'dependencies' => [ 'wp-admin' ],
				'version'      => null,
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function enqueue( Page_Definition_Interface $definition ): void {
		$this->maybe_register_assets();

		$active_field_configs = $definition->get_active_fields();
		if ( 0 === count( $active_field_configs ) ) {
			return;
		}

		$this->collect_and_enqueue_assets( $active_field_configs );
	}

	/**
	 * Register script or a style.
	 *
	 * @param array $asset    The asset to register.
	 * @param bool  $override If true, the asset will be overridden if it already exists in the Loader.
	 * @phpstan-param Asset $asset
	 * @psalm-param Asset $asset
	 */
	public static function add_registry( array $asset, bool $override = false ): void {
		$handle = $asset['handle'];
		if ( $override || ! isset( self::$registry[ $handle ] ) ) {
			self::$registry[ $handle ] = $asset;
		}
	}

	/**
	 * Register assets if they haven't been registered already.
	 */
	private function maybe_register_assets(): void {
		foreach ( self::$registry as $asset ) {
			$handle = $asset['handle'];
			if ( in_array( $handle, self::$registered_handles, true ) ) {
				continue;
			}

			$src     = is_callable( $asset['src'] ) ? ( $asset['src'] )() : $asset['src'];
			$version = array_key_exists( 'version', $asset ) ? $asset['version'] : false;
			$deps    = $asset['dependencies'] ?? [];
			if ( false === $src ) {
				continue;
			}

			if ( 'css' === $asset['type'] ) {
				wp_register_style( $handle, $src, $deps, $version );
			} else {
				wp_register_script( $handle, $src, $deps, $version, true );
			}

			self::$registered_handles[] = $handle;
		}
		self::$registry = [];
	}

	/**
	 * Collects all required assets for the active field types and enqueues them.
	 *
	 * @param array $active_field_configs A map of all field configurations on the page.
	 *
	 * @phpstan-param array<string, Field_Config> $active_field_configs
	 * @psalm-param array<string, Field_Config> $active_field_configs
	 */
	private function collect_and_enqueue_assets( array $active_field_configs ): void {
		$js_handles        = [];
		$css_handles       = [];
		$unique_inline_css = [];
		$unique_inline_js  = [];
		$inline_css        = $this->get_default_inline_css() . "\n";
		$inline_js         = $this->get_default_inline_js();

		// Handle Code Editors first using the new, safer logic.
		$this->enqueue_code_editors( $active_field_configs );

		foreach ( $this->get_active_field_classes( $active_field_configs ) as $class_name ) {
			$css_handles = array_merge( $css_handles, $class_name::get_enqueued_styles() );
			$js_handles  = array_merge( $js_handles, $class_name::get_enqueued_scripts() );

			$css_to_add = trim( $class_name::get_css_contents() );
			$js_to_add  = trim( $class_name::get_js_contents() );

			// Use a hash of the content as the key to prevent duplicates.
			if ( '' !== $css_to_add ) {
				$unique_inline_css[ md5( $css_to_add ) ] = $css_to_add;
			}
			if ( '' !== $js_to_add ) {
				$unique_inline_js[ md5( $js_to_add ) ] = $js_to_add;
			}
		}

		// Implode the unique values to create the final inline scripts and styles.
		if ( 0 < count( $unique_inline_css ) ) {
			$inline_css .= implode( "\n", $unique_inline_css );
		}
		if ( 0 < count( $unique_inline_js ) ) {
			$inline_js .= implode( "\n", $unique_inline_js );
		}

		$this->dispatch_enqueue_calls( $js_handles, $css_handles, $inline_js, $inline_css );
	}

	/**
	 * Gets the fully qualified class names for a list of field type identifiers.
	 *
	 * @param array $active_field_configs  An array of fields config present on current page/tab.
	 * @phpstan-param array<string, Field_Config> $active_field_configs
	 * @psalm-param array<string, Field_Config> $active_field_configs
	 *
	 * @return list<class-string<Field_Interface>>
	 */
	private function get_active_field_classes( array $active_field_configs ): array {

		$active_field_types = array_values( array_unique( array_column( $active_field_configs, 'type' ) ) );

		$registered_fields = $this->field_factory->get_registered_fields();
		$active_classes    = [];

		foreach ( $active_field_types as $type ) {
			if ( isset( $registered_fields[ $type ] ) ) {
				$active_classes[] = $registered_fields[ $type ];
			}
		}

		return $active_classes;
	}

	/**
	 * Dispatches the collected assets to the WordPress enqueueing functions.
	 *
	 * @param list<non-empty-string> $js_handles The script handles to enqueue.
	 * @param list<non-empty-string> $css_handles The style handles to enqueue.
	 * @param string                 $inline_js The inline JavaScript to add.
	 * @param string                 $inline_css The inline CSS to add.
	 */
	private function dispatch_enqueue_calls( array $js_handles, array $css_handles, string $inline_js, string $inline_css ): void {

		wp_enqueue_media();

		wp_enqueue_script( self::MAIN_JS_HANDLE );
		if ( '' !== $inline_js ) {
			wp_add_inline_script( self::MAIN_JS_HANDLE, $inline_js, 'after' );
		}

		wp_enqueue_style( self::MAIN_CSS_HANDLE );
		if ( '' !== $inline_css ) {
			wp_add_inline_style( self::MAIN_CSS_HANDLE, $inline_css );
		}

		foreach ( array_unique( $js_handles ) as $handle ) {
			wp_enqueue_script( $handle );
		}

		foreach ( array_unique( $css_handles ) as $handle ) {
			wp_enqueue_style( $handle );
		}
	}

	/**
	 * Finds all code editor fields on the page and enqueues the necessary assets.
	 *
	 * This method iterates through each Code_Editor_Field instance, calls wp_enqueue_code_editor
	 * for its specific mode, and then generates a unique initialization script for that instance
	 * using the clean settings object returned by the API. This is the correct way to support
	 * multiple editors with different modes on the same page.
	 *
	 * @param array $field_configs The configurations for all fields on the page.
	 * @phpstan-param array<string, Field_Config> $field_configs
	 * @psalm-param array<string, Field_Config> $field_configs
	 */
	private function enqueue_code_editors( array $field_configs ): void {
		$code_editor_fields = [];
		foreach ( $field_configs as $config ) {
			try {
				$field_object = $this->field_factory->create( $config );
				if ( $field_object instanceof Code_Editor_Field ) {
					$code_editor_fields[] = $field_object;
				}
			} catch ( InvalidArgumentException ) {
				continue;
			}
		}

		if ( 0 === count( $code_editor_fields ) ) {
			return;
		}

		$init_scripts = '';

		// Loop through each field instance individually.
		foreach ( $code_editor_fields as $field ) {
			$mode = $field->get_mode();

			// Call wp_enqueue_code_editor for this specific field's mode.
			// This returns a clean, correct settings object for this mode.
			$settings = wp_enqueue_code_editor( [ 'type' => $mode ] );

			// If enqueuing was successful, proceed to generate its initialization script.
			if ( false !== $settings ) {
				$custom_settings = $field->get_extra( 'editor_settings', [] );

				// The base settings are now correct. We only need to merge the user's custom settings.
				// We target the 'codemirror' key specifically, as this is where user overrides belong.
				if ( isset( $custom_settings ) && is_array( $custom_settings ) && isset( $settings['codemirror'] ) && is_array( $settings['codemirror'] ) ) {
					$settings['codemirror'] = array_merge( $settings['codemirror'], $custom_settings );
				}

				$settings_json = wp_json_encode( $settings );

				$init_scripts .= sprintf(
					'wp.codeEditor.initialize( "%s", %s );',
					$field->get_id(),
					is_string( $settings_json ) ? $settings_json : '{}'
				);
			}
		}

		// If any scripts were generated, add them to the page in a single inline script tag.
		if ( '' !== $init_scripts ) {
			wp_add_inline_script( 'code-editor', 'jQuery( function() { ' . $init_scripts . ' } );' );
		}
	}

	/**
	 * Returns the default inline JS.
	 *
	 * @return non-empty-string
	 */
	private function get_default_inline_js(): string {
		return <<<'JS'
jQuery(function($) {
    const form = $('.wptx-settings-form');
    if (!form.length) return;

    /**
     * Gets the value of a form field. It handles standard inputs, checkboxes,
     * radio buttons, and multi-selects.
     * @param {string} fieldId The unique ID of the field.
     * @returns {any} The value of the field.
     */
    function getFieldValue(fieldId) {
        const el = $('#' + fieldId);

        if (el.length) {
            if (el.is(':checkbox')) {
                return el.is(':checked');
            }
            // Works for text, select, textarea, etc.
            return el.val();
        }

        // Fallback for radio buttons which share a name but not an ID.
        const radio = $(`input[name$="[${fieldId}]"]:checked`);
        if (radio.length) {
            return radio.val();
        }

        return null;
    }

    /**
     * Evaluates a single condition object against the current form state.
     * @param {object} condition The condition rule to evaluate.
     * @returns {boolean} True if the condition is met, false otherwise.
     */
    function evaluateCondition(condition) {
        const sourceValue = getFieldValue(condition.field);
        const conditionValue = condition.value;

        if (sourceValue === null) {
            return false; // Source field not found or has no value.
        }

        const sourceNumber = parseFloat(sourceValue);
        const conditionNumber = parseFloat(conditionValue);

        switch (condition.operator.toLowerCase()) {
            case '=': case '==':
                // Use coercive comparison to handle "1" == 1 etc.
                return sourceValue == conditionValue;
            case '!=':
                return sourceValue != conditionValue;
            case '>':
                return !isNaN(sourceNumber) && !isNaN(conditionNumber) && sourceNumber > conditionNumber;
            case '>=':
                return !isNaN(sourceNumber) && !isNaN(conditionNumber) && sourceNumber >= conditionNumber;
            case '<':
                return !isNaN(sourceNumber) && !isNaN(conditionNumber) && sourceNumber < conditionNumber;
            case '<=':
                return !isNaN(sourceNumber) && !isNaN(conditionNumber) && sourceNumber <= conditionNumber;
            case 'in':
                if (!Array.isArray(conditionValue)) return false;
                const sourceArrayIn = Array.isArray(sourceValue) ? sourceValue : [sourceValue];
                return sourceArrayIn.some(val => conditionValue.map(String).includes(String(val)));
            case 'not in':
                if (!Array.isArray(conditionValue)) return false;
                const sourceArrayNotIn = Array.isArray(sourceValue) ? sourceValue : [sourceValue];
                return !sourceArrayNotIn.some(val => conditionValue.map(String).includes(String(val)));
            default:
                return false;
        }
    }

    /**
     * Checks all conditional fields on the page and toggles their visibility.
     */
    function checkAllConditions() {
        $('.wptx-conditional-wrapper').each(function() {
            const wrapper = $(this);
            const conditionsData = wrapper.data('conditions');
            if (!conditionsData || !Array.isArray(conditionsData.rules)) return;

            const relation = (conditionsData.relation || 'AND').toUpperCase();
            let isVisible = (relation === 'AND'); // Start true for AND, false for OR.

            for (const rule of conditionsData.rules) {
                const result = evaluateCondition(rule);
                if (relation === 'AND') {
                    if (!result) {
                        isVisible = false;
                        break;
                    }
                } else { // OR
                    if (result) {
                        isVisible = true;
                        break;
                    }
                }
            }

            const fieldRow = wrapper.closest('tr');
            if (fieldRow.length) {
                 if (isVisible) {
                    fieldRow.removeClass( 'wptx-hide' );
                } else {
                    fieldRow.addClass('wptx-hide');
                }
            }
        });
    }

    // Listen for changes on any input, select, or textarea within the form.
    form.on('change keyup', 'input, select, textarea', checkAllConditions);

    // Run on page load to set the initial state.
    checkAllConditions();

    // Trigger change on Select2 fields after they initialize to ensure correct state.
    setTimeout(function() {
        $('.wptx-select2-field, .wptx-multi-select2-field, .wptx-ajax-select2').trigger('change');
    }, 500);
});
JS;
	}

		/**
		 * Returns the default inline CSS with color variables for admin schemes.
		 *
		 * @return non-empty-string
		 */
	private function get_default_inline_css(): string {
		return <<<'CSS'
/*
 * Defines base variables and overrides them for each official WordPress
 * admin color scheme to ensure UI components match the user's preference.
 *
 * --wptx-primary: The main brand/solid color for elements.
 * --wptx-accent: A brighter color for focus rings, highlights, and hovers.
 */
body.wp-admin {
    --wptx-border-color: #8c8f94;

    /* Fallback and default "Fresh" scheme colors */
    --wptx-primary: #0073aa;
    --wptx-accent: #007cba;
}

/* Overrides for specific admin color schemes */
body.admin-color-light {
    --wptx-primary: #006699;
    --wptx-accent: #008ec2;
}
body.admin-color-modern {
    --wptx-primary: #3858e9;
    --wptx-accent: #3858e9; /* This scheme's primary is already bright */
}
body.admin-color-blue {
    --wptx-primary: #096484;
    --wptx-accent: #0a7e9e;
}
body.admin-color-coffee {
    --wptx-primary: #59524c;
    --wptx-accent: #c7a589;
}
body.admin-color-ectoplasm {
    --wptx-primary: #523f6d;
    --wptx-accent: #a3b745;
}
body.admin-color-midnight {
    --wptx-primary: #e14d43;
    --wptx-accent: #dd3629;
}
body.admin-color-ocean {
    --wptx-primary: #738e96;
    --wptx-accent: #9ebaa0;
}
body.admin-color-sunrise {
    --wptx-primary: #dd823b;
    --wptx-accent: #d54e21;
}

.wptx-settings-form {
	max-width: 970px;
}

/* Helper styles for tab navigation */
.nav-tab-wrapper .nav-tab {
    display: flex;
    align-items: center;
}

.nav-tab-wrapper .dashicons {
    opacity: 0.85;
    font-size: 1.125em;
    height: 1.125em;
    width: 1.125em;
    margin-inline-end: 0.5em;
    margin-bottom: -0.175em;
}
.wptx-hide {
	display: none !important;
}
CSS;
	}
}
