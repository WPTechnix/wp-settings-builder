# WP Settings Builder Framework

A modern, fluent, and extensible object-oriented framework for creating powerful and professional settings pages in WordPress.

[![Latest Version](https://img.shields.io/packagist/v/wptechnix/wp-settings-builder.svg?style=for-the-badge)](https://packagist.org/packages/wptechnix/wp-settings-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/wptechnix/wp-settings-builder.svg?style=for-the-badge)](https://packagist.org/packages/wptechnix/wp-settings-builder)
[![License](https://img.shields.io/packagist/l/wptechnix/wp-settings-builder.svg?style=for-the-badge)](https://packagist.org/packages/wptechnix/wp-settings-builder)

---

Tired of wrestling with the procedural WordPress Settings API? The WP Settings Builder provides a clean, chainable API that makes building maintainable and feature-rich settings pages a breeze. Go from a simple options page to a complex, tabbed interface with conditional logic without writing messy, hard-to-read code.

## Why Use This Framework?

-   ✅ **Fluent, Modern API**: A clean and readable interface for defining pages, tabs, sections, and fields.
-   🚀 **Comprehensive Field Library**: Over 30 field types included, from basic text inputs to advanced AJAX-powered relationship pickers.
-   🧠 **Built-in Conditional Logic**: Natively show or hide fields based on the values of other fields using a simple and powerful rules engine.
-   ⚙️ **Automatic Asset Management**: CSS and JavaScript for fields like color pickers, date pickers, and Select2 are loaded automatically only when needed.
-   ⚡ **AJAX-Powered Fields**: Easily add searchable fields for Posts, Pages, Users, and Terms that load data on the fly, ensuring excellent performance.
-   🎨 **Flexible Layouts**: Effortlessly create complex tabbed interfaces or simple, single-column settings pages.
-   🔒 **Secure & Robust**: Built-in sanitization for all fields, secure handling of sensitive data, and fully type-hinted for reliability.
-   🧩 **Extensible**: Easily register your own custom field types to extend the framework's capabilities.

## Showcase

Create anything from a simple page to a complex, tabbed interface with icons.

### **Simple Layout**
![Simple Settings Page Example](docs/screenshot-01.png)

### **Tabbed Layout**![Tabbed Settings Page Example](docs/screenshot-02.png)

## Installation

Install the framework into your project using Composer:

```bash
composer require wptechnix/wp-settings-builder
```

Make sure your project includes the Composer autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Getting Started

The following example creates a simple settings page with two fields under the main WordPress "Settings" menu. For a complete walkthrough, see the [Getting Started Guide](./docs/02-getting-started.md).

```php
<?php
use WPTechnix\WP_Settings_Builder\Settings_Builder;

add_action( 'admin_init', function() {

    // Step 1: Create a page instance
    $page = ( new Settings_Builder() )->create( 'my_plugin_options', 'my-plugin-settings' );

    // Step 2: Configure the page
    $page->set_page_title( 'My Plugin Settings' )
         ->set_menu_title( 'My Plugin' )
         ->set_parent_slug( 'options-general.php' );

    // Step 3: Add a section
    $page->add_section( 'general_section', 'General Settings' );

    // Step 4: Add fields
    $page->add_field( 'api_key', 'general_section', 'text', 'API Key', [
        'description' => 'Enter your secret API key.'
    ]);
    
    $page->add_field( 'enable_feature', 'general_section', 'switch', 'Enable Awesome Feature', [
        'default' => true
    ]);

    // Step 5: Build and register the page
    $page->init();

} );
```

---

## Full Documentation

While this README provides a quick overview, the comprehensive documentation will guide you through every feature, from basic setup to advanced customization.

*   **1. Core Concepts**
    *   [Introduction](./docs/01-introduction.md)
    *   [Getting Started](./docs/02-getting-started.md)
    *   [Settings Page Configuration](./docs/03-settings-page-configuration.md)

*   **2. Field Guides**
    *   [Basic Inputs](./docs/04-fields-basic-inputs.md) (Text, Number, Password, etc.)
    *   [Choice and Toggles](./docs/05-fields-choice-and-toggles.md) (Select, Switch, Checkbox, etc.)
    *   [Content and Media](./docs/06-fields-content-and-media.md) (WYSIWYG, Code Editor, Media Library)
    *   [Date and Time](./docs/07-fields-date-and-time.md) (Date, Time, and Range Pickers)
    *   [Relational (AJAX)](./docs/08-fields-relational-ajax.md) (Post, User, and Term Selectors)

*   **3. Advanced Topics**
    *   [Conditional Logic](./docs/09-conditional-logic.md)
    *   [Advanced Guide: Creating Custom Fields](./docs/10-advanced-guide.md)
    *   [Helpers and Methods](./docs/11-helpers-and-methods.md) (Programmatic Get/Set, Validation)

---

## Field Reference Table

This table is a quick reference for all available field types. For detailed examples, see the documentation guides linked above.

| Type | Description | Common `extras` Options |
| :--- | :--- | :--- |
| **Basic Inputs** |
| `text` | A standard single-line text input. | `default`, `html_attributes` |
| `url` | An input for URLs, sanitized with `esc_url_raw`. | `default`, `html_attributes` |
| `email` | An input for email addresses. | `default`, `html_attributes` |
| `password` | A secure input for API keys/passwords. The value is not shown. | `html_attributes` |
| `number` | An HTML5 number input. | `default`, `html_attributes` (e.g., `['min'=>0]` ) |
| `textarea` | A multi-line text area. | `default`, `html_attributes` (e.g., `['rows'=>5]` ) |
| `color` | The native WordPress color picker. | `default` (e.g., `'#RRGGBB'`) |
| **Choice & Toggles** |
| `checkbox` | A standard single checkbox. | `description` (acts as clickable label), `default` (`true`/`false`) |
| `switch` | A modern, styled toggle switch (boolean). | `description` (acts as clickable label), `default` (`true`/`false`) |
| `choice` | Radio buttons for selecting one option. | `options` (`['val'=>'Label']`), `default` |
| `buttons_group` | A styled, segmented button control (like radio buttons). | `options` (`['val'=>'Label']`), `default` |
| `select` | A searchable dropdown (Select2) for a single choice. | `options` (`['val'=>'Label']`), `default` |
| `multi_check` | A group of checkboxes for selecting multiple options. | `options`, `default` (as `array`) |
| `multi_select` | A searchable dropdown (Select2) for multiple choices. | `options`, `default` (as `array`) |
| **Content & Media** |
| `wysiwyg` | The WordPress TinyMCE rich text editor. | `editor_settings` (see `wp_editor()`) |
| `code_editor` | A syntax-highlighting code editor (CodeMirror). | `mode` (e.g., `'text/css'`), `editor_settings` |
| `media` | Opens the WP Media Library to select a single item (stores ID). | `media_settings` (`['library_type'=>'image']`) |
| `media_multiple` | Opens the WP Media Library to select multiple items (stores array of IDs). | `media_settings` |
| **Date & Time Pickers** |
| `date` | A date picker (Flatpickr). | `display_format` (PHP format), `default` |
| `time` | A time picker (Flatpickr). | `display_format` (PHP format), `default` |
| `date_time` | A combination date and time picker. | `display_format` (PHP format), `default` |
| `date_range` | A date range picker (start and end date). | `display_format`, `default` (as `array`) |
| `date_time_range` | A date and time range picker. | `display_format`, `default` (as `array`) |
| **Relational (AJAX)** |
| `post` | AJAX-powered search for a single post/page. | `query_args` (`['post_type'=>'page']`) |
| `posts` | AJAX-powered search for multiple posts/pages. | `query_args` |
| `user` | AJAX-powered search for a single user. | `query_args` (`['role'=>'editor']`) |
| `users` | AJAX-powered search for multiple users. | `query_args` |
| `term` | AJAX-powered search for a single taxonomy term. | `query_args` (`['taxonomy'=>'category']`) |
| `terms` | AJAX-powered search for multiple taxonomy terms. | `query_args` |
| **Helpers** |
| `description` | Displays only text; does not save a value. Used for instructions. | `description` (accepts HTML) |

## Contributing

Contributions are welcome! Please feel free to open a pull request or submit an issue with your suggestions.

## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.