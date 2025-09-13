# 10. Advanced Guide: Creating Custom Fields

While the framework includes a comprehensive library of fields, you may have unique requirements for your project. The framework is designed from the ground up to be extensible, allowing you to create and register your own custom field types with their own rendering logic, sanitization, and assets.

This guide will walk you through the entire process of creating a custom "Star Rating" field.

---

## Anatomy of a Field Class

Every field in the framework is a PHP class that extends the `\WPTechnix\WP_Settings_Builder\Fields\Abstractions\Abstract_Field` class. When creating your own, you'll need to implement a few key methods.

*   `protected static string $type`: This is the most important property. It's the unique string identifier you will use when calling `add_field()`. For our example, it will be `'star_rating'`.

*   `public function render(): void`: This method is responsible for echoing the HTML markup for your field. You have access to all field properties via `$this->get_id()`, `$this->get_name()`, `$this->get_value()`, etc.

*   `public function sanitize( mixed $value ): mixed`: This method receives the raw, user-submitted value from the form. Your job is to validate and clean this value before it's saved to the database.

*   **Asset Methods**: These static methods allow you to bundle CSS and JavaScript directly with your field class. They are automatically collected and enqueued only when your field is used on a page.
    *   `get_css_contents(): string`: Return a string of CSS.
    *   `get_js_contents(): string`: Return a string of JavaScript.
    *   `get_enqueued_styles(): array`: Return an array of registered stylesheet handles to enqueue.
    *   `get_enqueued_scripts(): array`: Return an array of registered script handles to enqueue.
    *   `get_asset_definitions(): array`: For registering external assets from a CDN or file path.

---

## Tutorial: Creating a Star Rating Field

Let's build a 1-to-5 star rating field. It will render as a set of interactive stars and save the selected rating as an integer.

### Step 1: Create the Field Class File

First, create a new PHP file for your field. A good practice is to create a `Fields` directory within your own plugin's source folder.

`my-plugin/inc/Fields/Star_Rating_Field.php`

Create the basic class structure, extending `Abstract_Field`.

```php
<?php
namespace MyAwesomePlugin\Fields;

use WPTechnix\WP_Settings_Builder\Fields\Common\Abstract_Field;

final class Star_Rating_Field extends Abstract_Field {

    /**
     * The unique type identifier for this field.
     */
    protected static string $type = 'star_rating';

    /**
     * Renders the HTML for the star rating field.
     */
    public function render(): void {
        // We will add the HTML here.
    }

    /**
     * Sanitizes the submitted value to ensure it's a valid integer.
     */
    public function sanitize( mixed $value ): ?int {
        // We will add the sanitization logic here.
    }

    /**
     * Returns the inline CSS for styling the stars.
     */
    public static function get_css_contents(): string {
        // We will add the CSS here.
    }
}
```

### Step 2: Implement the `render()` Method

We will render a set of radio buttons within labels. The CSS will then transform these into stars. This is a highly accessible approach because it's still a standard form element under the hood.

```php
// Inside Star_Rating_Field class

public function render(): void {
    $current_value = $this->get_value();
    $max_rating = $this->get_extra('max', 5); // Allow customizing the max rating
    $field_name = $this->get_name();
    $field_id_base = $this->get_id();

    echo '<div class="wptx-star-rating-wrapper">';

    // The stars are rendered in reverse order to achieve the hover effect with CSS sibling selectors.
    for ( $i = $max_rating; $i >= 1; $i-- ) {
        $radio_id = "{$field_id_base}-{$i}";
        printf(
            '<input type="radio" id="%s" name="%s" value="%d" %s class="screen-reader-text" />',
            esc_attr($radio_id),
            esc_attr($field_name),
            $i,
            checked($i, $current_value, false)
        );
        printf(
            '<label for="%s" title="%d star%s">&#9733;</label>',
            esc_attr($radio_id),
            $i,
            $i > 1 ? 's' : ''
        );
    }

    echo '</div>';
}
```

### Step 3: Implement the `sanitize()` Method

The sanitization logic is simple: we need to ensure the saved value is an integer and falls within our allowed range (1 to 5).

```php
// Inside Star_Rating_Field class

public function sanitize( mixed $value ): ?int {
    $max_rating = $this->get_extra('max', 5);
    $rating = absint($value);

    // If the rating is valid, return it. Otherwise, return null to clear it.
    if ( $rating >= 1 && $rating <= $max_rating ) {
        return $rating;
    }

    return null;
}
```

### Step 4: Add the CSS with `get_css_contents()`

This CSS will hide the radio buttons and style the labels to look and behave like interactive stars.

```php
// Inside Star_Rating_Field class

public static function get_css_contents(): string {
    return <<<'CSS'
.wptx-star-rating-wrapper {
    display: inline-flex;
    flex-direction: row-reverse; /* Important for the hover effect */
    font-size: 2em;
}
.wptx-star-rating-wrapper label {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}
.wptx-star-rating-wrapper > input:checked ~ label,
.wptx-star-rating-wrapper:not(:checked) > label:hover,
.wptx-star-rating-wrapper:not(:checked) > label:hover ~ label {
    color: #ffb700;
}
CSS;
}
```

### Step 5: Register Your New Field

Now that the class is complete, you need to tell the `Settings_Builder` about it. The best place to do this is right before you initialize your settings page.

Use the `register_field_class()` method on your `Settings_Builder` instance.

```php
// In your main settings page registration file (e.g., inc/settings-page.php)

use MyAwesomePlugin\Fields\Star_Rating_Field; // <-- Import your new class
use WPTechnix\WP_Settings_Builder\Settings_Builder;

function my_plugin_register_settings_page(): void {

    $builder = new Settings_Builder();

    // Register your custom field class BEFORE creating the page.
    $builder->register_field_class( Star_Rating_Field::class );

    // Now, create the page using the same builder instance.
    $page = $builder->create( 'my_plugin_options', 'my-plugin-settings' );
    
    // ... configure your page and add other fields ...
}
```

### Step 6: Use Your New Field

You can now use your custom field just like any other field by using its static `$type` property (`'star_rating'`).

```php
// Add the star rating field to a section
$page->add_field(
    'product_rating',
    'product_section',
    'star_rating', // <-- Use the unique type you defined
    'Product Quality',
    [
        'description' => 'Rate the quality of this product from 1 to 5 stars.',
        'default' => 4,
        'max' => 5, // Custom extra we built into our render method
    ]
);
```

That's it! You now have a fully functional, reusable Star Rating field that is seamlessly integrated into the framework, complete with its own styling and sanitization. You can use this same pattern to create any custom field you can imagine.

The final guide will cover some helper methods and miscellaneous features.

**Next:** [**11. Helpers and Methods**](./11-helpers-and-methods.md)
