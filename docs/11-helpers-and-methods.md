# 11. Helpers and Methods

This final guide covers several useful methods and features that can help you interact with your settings programmatically and add advanced customizations.

---

## Retrieving Setting Values

While building the settings page is the primary function of the framework, you'll frequently need to access your saved settings elsewhere in your plugin or theme's code.

The recommended way to do this is with a combination of the static `get_instance()` method and the `get_setting()` method.

*   `Settings_Builder::get_instance( ?string $page_slug = null )`: This static method retrieves the active `Settings_Page` object for a given page slug.
    *   **Behavior:** If you provide a `$page_slug`, it will return the specific page instance associated with that slug. If the `$page_slug` is `null` or omitted, it will return the **first** settings page instance that was created.
    *   **Best Practice:** To avoid ambiguity, it is strongly recommended to always provide the `$page_slug`, especially if your plugin registers more than one settings page.

*   `->get_setting( string $key, mixed $default_value = null )`: Once you have the page instance, you can use this method to retrieve the value of a specific field.
    *   `$key`: The unique ID of the field you want to retrieve.
    *   `$default_value`: A fallback value to return if the setting has not been saved yet. This overrides the default value defined in the field's configuration, giving you contextual control.

#### Example

Let's assume you have a settings page with the slug `'my-plugin-settings'` and a switch field with the ID `'enable_feature'`.

```php
use WPTechnix\WP_Settings_Builder\Settings_Builder;

function my_plugin_custom_logic() {
    // Get the page instance by its unique slug.
    $settings_page = Settings_Builder::get_instance('my-plugin-settings');

    // Always check if the instance exists before using it.
    if ( $settings_page ) {
        $is_feature_enabled = $settings_page->get_setting( 'enable_feature', false );

        if ( $is_feature_enabled ) {
            // Run the feature's code...
            echo "<!-- Awesome Feature is Enabled -->";
        }
    }
}

add_action( 'wp_footer', 'my_plugin_custom_logic' );
```

---

## Updating Setting Values Programmatically

You can also update a setting's value from your code without requiring the user to submit the settings form. This is useful for things like updating a timestamp, storing data from an API call, or providing a "reset" button.

*   `->set_setting( string $key, mixed $value )`: Sets a new value for a specific field and saves it to the database.
    *   `$key`: The unique ID of the field to update.
    *   `$value`: The new value to save. **Important:** This value will **not** be sanitized by the field's `sanitize()` method, so you must ensure it is clean before calling this method.

#### Example

Imagine you want to create a button that resets the API key.

```php
// In a function that handles a specific admin action:
function my_plugin_handle_reset_action() {
    // ... check nonce and user capabilities ...

    $settings_page = Settings_Builder::get_instance('my-plugin-settings');
    if ( $settings_page ) {
        // Set the 'api_key' field to an empty string.
        $settings_page->set_setting( 'api_key', '' );
    }

    // ... redirect back to the settings page ...
}
```

---

## Custom Validation Callback

For complex validation that goes beyond a field's basic sanitization, you can use the `'validation_callback'` extra. This allows you to run your own custom validation function before the value is saved.

The callback runs during the sanitization process when the user saves the settings page.

*   **Callback Signature:** `function( mixed $raw_value, array $field_config ): bool|string`
    *   `$raw_value`: The raw, un-sanitized value submitted by the user.
    *   `$field_config`: The full configuration array of the field being validated.
*   **Return Value:**
    *   Return `true` if the validation passes.
    *   Return a `string` containing an error message if validation fails. The framework will automatically display this error to the user and prevent the invalid value from being saved.

#### Example: Ensure a license key is in the correct format

```php
$page->add_field( 'license_key', 'api_section', 'text', 'License Key', [
    'description' => 'Must be in the format: XXXX-XXXX-XXXX-XXXX',
    'validation_callback' => function( $value, $field_config ) {
        // Allow empty value
        if ( empty( $value ) ) {
            return true;
        }

        // Use regex to check the format
        if ( ! preg_match('/^([A-Z0-9]{4}-){3}[A-Z0-9]{4}$/', $value) ) {
            // Return an error message string on failure
            return 'The license key is not in a valid format.';
        }

        // Return true on success
        return true;
    },
]);
```

---

## Registering Global Assets

While custom fields can bundle their own CSS and JS, sometimes you need to add a script or style that applies to the entire settings page (e.g., a custom font or a shared utility script).

You can use the `register_asset()` method on the main `Settings_Builder` instance. This must be done **before** you call `create()`.

You can also use this method to override the default CDN-based assets (like Select2 or Flatpickr) with your own locally hosted versions by using the same asset handles.

*   **Default Asset Handles:** `select2-css`, `select2-js`, `flatpickr-css`, `flatpickr-airbnb-css`, `flatpickr-js`.

#### Example: Add a custom stylesheet for your settings page

```php
// In your main settings page registration file
use WPTechnix\WP_Settings_Builder\Settings_Builder;

$builder = new Settings_Builder();

// Register a custom CSS file.
$builder->register_asset(
    'my-plugin-admin-styles',                       // Unique handle
    'css',                                          // 'css' or 'js'
    plugin_dir_url( __FILE__ ) . 'css/admin.css',   // Full URL to the asset
    [],                                             // Dependencies (e.g., ['wp-color-picker'])
    '1.0.0'                                         // Version number
);

// Now create the page
$page = $builder->create( 'my_plugin_options', 'my-plugin-settings' );

// ... proceed with page configuration ...
```

---

## Debugging and Error Handling

The framework is designed to help you catch configuration errors early by throwing an `InvalidArgumentException`. This will cause a fatal error, making the problem immediately obvious. Common causes include:

*   Adding a field to a section that does not exist: `->add_field( 'my_field', 'non_existent_section', ... )`
*   Adding a section to a tab that does not exist: `->add_section( 'my_section', 'My Section', null, 'non_existent_tab' )`
*   Using a field type that has not been registered: `->add_field( 'my_field', 'my_section', 'non_existent_type', ... )`

If you encounter a fatal error, check the stack trace. It will point you directly to the line in your settings page configuration that contains the typo or logical error.

---

## You've Reached the End!

Congratulations on completing the documentation for the WP Settings Builder Framework. You now have the knowledge to build everything from simple option pages to complex, extensible, and user-friendly admin interfaces.

Happy coding