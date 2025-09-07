# 2. Getting Started

This guide will walk you through the entire process of creating a simple, functional settings page for your WordPress plugin or theme. By the end, you'll have a new menu item in the admin dashboard with a page that can save data.

---

## Prerequisites

Before you begin, please make sure you have:

1.  A local WordPress development environment.
2.  [Composer](https://getcomposer.org/) installed on your system.
3.  The framework added to your project (as shown in the next step).

---

## Step 1: Install the Framework

Navigate to your plugin or theme's root directory in your terminal and run the following Composer command:

```bash
composer require wptechnix/wp-settings-builder
```

Next, ensure your main plugin file or your theme's `functions.php` includes the Composer autoloader. This is a crucial step that makes all the framework's classes available to your project.

```php
// In your-plugin.php or functions.php
require_once __DIR__ . '/vendor/autoload.php';
```

---

## Step 2: Create a Settings Page File

While you can put the code anywhere, we recommend creating a dedicated file for your settings page configuration. For this example, let's create a file named `inc/settings-page.php` inside your plugin.

```
your-plugin/
├── inc/
│   └── settings-page.php  <-- We will put our code here
├── vendor/
└── your-plugin.php
```

Now, include this new file in your main plugin file:

```php
// In your-plugin.php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/inc/settings-page.php'; // <-- Add this line
```

---

## Step 3: Write the Code

Open `inc/settings-page.php` and add the following code. This block defines and registers a complete settings page. Read through the comments to understand what each part does.

```php
<?php
/**
 * Registers the settings page for our plugin.
 *
 * @package MyAwesomePlugin
 */

// Always use namespaces to prevent conflicts.
namespace MyAwesomePlugin\Admin;

// Import the main builder class.
use WPTechnix\WP_Settings_Builder\Settings_Builder;

// Step 1: Create a page instance using the Settings_Builder.
// The first argument is the 'option_name' where all data is stored in a single array.
// The second argument is the unique 'page_slug' for the URL.
$page = ( new Settings_Builder() )->create( 'my_plugin_options', 'my-plugin-settings' );

// Step 2: Configure the page's appearance and location in the admin menu.
// These methods are chainable for clean, readable code.
$page->set_page_title( 'My Awesome Plugin Settings' )
        ->set_menu_title( 'My Plugin' )
        ->set_parent_slug( 'options-general.php' ) // Places it under the main "Settings" menu
        ->set_capability( 'manage_options' );      // Only admins can see it

// Step 3: Add a section. Sections are visual containers for grouping related fields.
$page->add_section(
    'general_section',                  // Unique ID for the section
    'API & General Configuration',      // Title displayed on the page
    'Configure the main settings for connecting to our service.' // Optional description
);

// Step 4: Add fields to the section you just created.
$page->add_field(
    'api_key',                          // Unique ID for the field (used for saving/retrieving data)
    'general_section',                  // The ID of the section this field belongs to
    'password',                         // The field type
    'Service API Key',                  // The label for the field
    [                                   // An array of extra options
        'description' => 'Enter your secret API key from your service dashboard.',
        'html_attributes' => [
            'placeholder' => 'sk_xxxxxxxxxxxx',
        ],
    ]
);

$page->add_field(
    'enable_caching',
    'general_section',
    'switch',                           // A modern toggle switch field type
    'Enable Caching',
    [
        'description' => 'Improve performance by enabling the local cache.',
        'default'     => true,          // Sets the default value if none is saved yet
    ]
);

// Step 5: Finalize and register everything with WordPress.
// This is the most important step! It takes all your configuration and
// hooks it into the WordPress admin.
$page->init();
```

---

## Step 4: See the Result

That's it! Save the file and navigate to your WordPress admin dashboard.

You will now see a new menu item under **Settings → My Plugin**. Clicking it will take you to your new settings page. You can enter a value in the API key field, toggle the switch, and click "Save Changes". The framework handles the saving, sanitization, and reloading for you.

---

## Step 5: Retrieving Your Settings

Now that you've saved some data, you need to use it in your plugin's logic. You can retrieve any setting with the static `get_instance()` method and the `get_setting()` method.

```php
use WPTechnix\WP_Settings_Builder\Settings_Builder;

// Get the value of the 'enable_caching' switch.
// We pass the page slug to get the correct instance.
$is_caching_enabled = Settings_Builder::get_instance( 'my-plugin-settings' )
    ->get_setting( 'enable_caching' );

// If the feature is enabled, run its code.
if ( $is_caching_enabled ) {
    // Do something with caching...
}

// Get the API Key. The third argument is a final fallback default.
$api_key = Settings_Builder::get_instance( 'my-plugin-settings' )
    ->get_setting( 'api_key', 'default-key' );

// Now you can use $api_key in an API call.
```

---

## What's Next?

Congratulations! You've successfully created a settings page and learned the basic workflow of the Settings Builder.

*   You know how to create a `Settings_Page` instance.
*   You can configure its title and location.
*   You can add sections and fields.
*   You can retrieve saved settings anywhere in your code.

Now you're ready to explore more advanced configurations, like organizing your page with tabs.

**Next:** [**3. Settings Page Configuration**](./03-settings-page-configuration.md)