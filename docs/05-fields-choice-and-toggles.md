# 5. Fields: Choice and Toggles

This guide covers fields designed for making selections, from simple on/off toggles to complex, searchable multi-select dropdowns.

A key `extra` for many of these fields is the `'options'` array. This is an associative array where the `key` is the value that gets saved to the database, and the `value` is the user-facing label.

```php
'options' => [
    'low'    => 'Low Priority',
    'medium' => 'Medium Priority',
    'high'   => 'High Priority',
]
```

---

## Checkbox

*   **Type:** `checkbox`
*   **Description:** A standard single checkbox. Ideal for boolean (true/false) settings.
*   **Value Saved:** `true` if checked, `false` if unchecked.
*   **Key Feature:** The `'description'` text is automatically wrapped in a `<label>` tag with the checkbox, making the entire text clickable, which improves accessibility and user experience.

#### Example

```php
$page->add_field( 
    'open_links_in_new_tab', 
    'general_section', 
    'checkbox', 
    'New Tab Behavior', 
    [
        'description' => 'Enable this to open all external links in a new browser tab.',
        'default'     => true,
    ]
);
```

---

## Switch

*   **Type:** `switch`
*   **Description:** A modern, styled toggle switch that is functionally identical to a checkbox. It provides a better user experience for simple on/off settings.
*   **Value Saved:** `true` if on, `false` if off.
*   **Key Feature:** Like the checkbox, the `'description'` is a clickable label.

#### Example

```php
$page->add_field( 
    'enable_dark_mode', 
    'display_section', 
    'switch', 
    'Dark Mode', 
    [
        'description' => 'Activate the dark mode theme for the front-end.',
        'default'     => false,
    ]
);
```

---

## Choice (Radio Buttons)

*   **Type:** `choice`
*   **Description:** A group of radio buttons, used when a user must select one option from a small set of choices.
*   **Required `extra`:** `'options'` (array)

#### Example

```php
$page->add_field( 
    'thumbnail_position', 
    'display_section', 
    'choice', 
    'Thumbnail Position', 
    [
        'description' => 'Choose where to display the featured image on single posts.',
        'default'     => 'above_title',
        'options'     => [
            'above_title' => 'Above the Post Title',
            'below_title' => 'Below the Post Title',
            'no_thumb'    => 'Do Not Display',
        ],
    ]
);
```

---

## Buttons Group

*   **Type:** `buttons_group`
*   **Description:** A visually appealing alternative to radio buttons, styled as a segmented button control. It's functionally identical to the `choice` field.
*   **Required `extra`:** `'options'` (array)

#### Example

```php
$page->add_field( 
    'content_alignment', 
    'display_section', 
    'buttons_group', 
    'Content Alignment', 
    [
        'description' => 'Select the default text alignment for your content.',
        'default'     => 'left',
        'options'     => [
            'left'   => 'Left',
            'center' => 'Center',
            'right'  => 'Right',
        ],
    ]
);
```

---

## Select

*   **Type:** `select`
*   **Description:** A searchable dropdown menu for selecting a single option. This field is enhanced with the [Select2](https://select2.org/) library for a better user experience, especially with long lists.
*   **Assets:** Automatically enqueues Select2 scripts and styles.
*   **Required `extra`:** `'options'` (array)

#### Example

```php
$page->add_field( 
    'font_selection', 
    'display_section', 
    'select', 
    'Primary Font', 
    [
        'description' => 'Choose a font from the Google Fonts library.',
        'default'     => 'roboto',
        'options'     => [
            'roboto'    => 'Roboto',
            'open_sans' => 'Open Sans',
            'lato'      => 'Lato',
            'montserrat'=> 'Montserrat',
        ],
    ]
);
```

---

## Multi-Check

*   **Type:** `multi_check`
*   **Description:** A group of checkboxes that allows the user to select multiple options.
*   **Value Saved:** An indexed array of the selected option keys (e.g., `['dashboard', 'media']`).
*   **Required `extra`:** `'options'` (array)

#### Example

```php
$page->add_field( 
    'post_types_to_search',
    'general_section', 
    'multi_check', 
    'Searchable Content', 
    [
        'description' => 'Select which post types should be included in search results.',
        'default'     => ['post', 'page'],
        'options'     => [
            'post'    => 'Posts',
            'page'    => 'Pages',
            'product' => 'Products',
            'docs'    => 'Documentation',
        ],
    ]
);
```

---

## Multi-Select

*   **Type:** `multi_select`
*   **Description:** A searchable, multi-select dropdown menu, also powered by Select2. It's the best way to handle multiple selections from a long list of options.
*   **Assets:** Automatically enqueues Select2 scripts and styles.
*   **Value Saved:** An indexed array of the selected option keys.
*   **Required `extra`:** `'options'` (array)

#### Example

```php
$page->add_field( 
    'user_roles_access', 
    'advanced_section', 
    'multi_select', 
    'Roles with Access', 
    [
        'description' => 'Select which user roles can access the premium features.',
        'default'     => ['administrator', 'editor'],
        'options'     => [
            'administrator' => 'Administrator',
            'editor'        => 'Editor',
            'author'        => 'Author',
            'contributor'   => 'Contributor',
            'subscriber'    => 'Subscriber',
        ],
    ]
);
```

Now that you've mastered choice-based fields, the next section will cover fields for handling rich content, code, and media from the WordPress library.

**Next:** [**6. Fields: Content and Media**](./06-fields-content-and-media.md)