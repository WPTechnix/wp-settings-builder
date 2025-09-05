# 4. Fields: Basic Inputs

These are the foundational fields for collecting simple text, numeric, and sensitive data. They cover the most common use cases for any settings page.

All fields are added to a section using the `add_field()` method. The common parameters are:

*   `add_field( string $id, string $section_id, string $type, string $title, array $extras = [] )`

This guide focuses on the `$type` and the available `$extras` for each basic input field.

---

## Common `extras`

The following keys can be used in the `$extras` array for most basic input fields:

*   `'description'` (string): Text displayed below the field to provide context or instructions.
*   `'default'` (mixed): The default value for the field if no value has been saved yet.
*   `'html_attributes'` (array): An associative array of HTML attributes to add to the input element (e.g., `['placeholder' => 'Enter text...']`).
*   `'conditions'` (array): An array of rules to conditionally show or hide this field. (See the [Conditional Logic](./09-conditional-logic.md) guide for details).

---

## Text

*   **Type:** `text`
*   **Description:** A standard single-line text input field. Ideal for names, titles, or short phrases.
*   **Sanitization:** `sanitize_text_field()`

#### Example

```php
$page->add_field(
    'company_name', 
    'general_section', 
    'text', 
    'Company Name',
    [
        'description' => 'Enter the name of your business.',
        'default'     => 'My Awesome Company',
        'html_attributes' => [
            'placeholder' => 'e.g., Stark Industries',
        ],
    ]
);
```

---

## URL

*   **Type:** `url`
*   **Description:** An input specifically for URLs. It uses the `url` input type, which may provide special validation or keyboard layouts on mobile devices.
*   **Sanitization:** `esc_url_raw()`

#### Example

```php
$page->add_field( 
    'privacy_policy_url', 
    'general_section', 
    'url', 
    'Privacy Policy URL', 
    [
        'description' => 'Link to your website\'s privacy policy.',
        'html_attributes' => [
            'placeholder' => 'https://example.com/privacy',
        ],
    ]
);
```

---

## Email

*   **Type:** `email`
*   **Description:** An input for email addresses. It uses the `email` input type for browser-level validation.
*   **Sanitization:** `sanitize_email()`

#### Example

```php
$page->add_field( 
    'support_email', 
    'general_section', 
    'email', 
    'Support Email', 
    [
        'description' => 'The email address where customers can reach you for support.',
        'default'     => get_option('admin_email'),
    ]
);
```

---

## Number

*   **Type:** `number`
*   **Description:** An input for numeric values. You can use HTML attributes to set constraints.
*   **Sanitization:** Validates that the input is a valid integer or float.

#### Example

```php
$page->add_field( 
    'items_per_page',
    'display_section',
    'number',
    'Items Per Page',
    [
        'description' => 'How many items to show on a single page.',
        'default'     => 10,
        'html_attributes' => [
            'min'  => 1,
            'max'  => 50,
            'step' => 1,
        ],
    ]
);
```

---

## Password

*   **Type:** `password`
*   **Description:** A secure field designed for sensitive data like API keys or passwords.
*   **Key Features:**
    *   The saved value is **never** rendered in the HTML, preventing it from being exposed in the page source.
    *   If a value is saved, the field displays a placeholder of asterisks (`********`) with the same length as the saved value. This gives the user a visual confirmation that a value is set without revealing it.
    *   To clear the value, the user must delete the asterisks and save an empty field.
    *   If the user doesn't touch the field, the original value is preserved.
*   **Sanitization:** The value is saved as a plain string.

#### Example

```php
$page->add_field( 
    'api_secret_key', 
    'api_section', 
    'password', 
    'API Secret Key', 
    [
        'description' => 'Your secret key is stored securely and is never shown here.',
        'html_attributes' => [
            'autocomplete' => 'new-password', // Helps prevent browser auto-fill
        ],
    ]
);
```

---

## Textarea

*   **Type:** `textarea`
*   **Description:** A field for longer, multi-line text. Ideal for custom CSS, bio text, or detailed notes.
*   **Sanitization:** `sanitize_textarea_field()`

#### Example

```php
$page->add_field( 
    'custom_css', 
    'advanced_section', 
    'textarea', 
    'Custom CSS', 
    [
        'description' => 'Add custom CSS rules here. They will be loaded on the front-end.',
        'html_attributes' => [
            'rows'        => 8,
            'cols'        => 50,
            'placeholder' => '.my-class { color: red; }',
        ],
    ]
);
```

---

## Color

*   **Type:** `color`
*   **Description:** Provides the native WordPress color picker for easy and consistent color selection.
*   **Assets:** Automatically enqueues the `wp-color-picker` scripts and styles.
*   **Sanitization:** Validates that the input is a valid 3 or 6-digit hex color code (e.g., `#f00` or `#ff0000`).

#### Example

```php
$page->add_field(
    'primary_brand_color', 
    'display_section', 
    'color', 
    'Primary Brand Color', 
    [
        'description' => 'Select the primary color for your theme.',
        'default'     => '#3858e9',
    ]
);
```

With these basic fields, you can build the foundation of most settings pages. The next guide will explore fields designed for making choices, like toggles, radio buttons, and dropdowns.

**Next:** [**5. Fields: Choice and Toggles**](./05-fields-choice-and-toggles.md)