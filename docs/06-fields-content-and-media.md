# 6. Fields: Content and Media

This guide covers fields designed for handling rich content, custom code, and assets from the WordPress Media Library. These fields provide powerful, integrated experiences for content management directly within your settings page.

---

## WYSIWYG Editor

*   **Type:** `wysiwyg`
*   **Description:** Renders the standard WordPress TinyMCE rich text editor. This is the same editor you use for posts and pages, providing a familiar interface for users to create formatted content.
*   **Sanitization:** `wp_kses_post()` to ensure the HTML is safe and compliant.

#### Key `extra`: `editor_settings`

You can customize nearly every aspect of the editor by passing an array to the `'editor_settings'` key. This array is passed directly to the WordPress `wp_editor()` function.

**Common `editor_settings` options:**

*   `'textarea_rows'` (int): The number of rows (height) for the editor.
*   `'media_buttons'` (bool): Whether to display the "Add Media" button.
*   `'teeny'` (bool): Set to `true` to output a minimal editor with only the most basic controls.
*   `'quicktags'` (bool): Whether to display the Text/Visual tabs.

For a complete list of options, see the official [WordPress Developer documentation for `wp_editor()`](https://developer.wordpress.org/reference/functions/wp_editor/).

#### Example

```php
$page->add_field( 'site_footer_bio', 'content_section', 'wysiwyg', 'Footer Biography', [
    'description' => 'This content will appear in the site footer.',
    'default'     => '<h2>About Us</h2><p>We are an awesome company.</p>',
    'editor_settings' => [
        'textarea_rows' => 8,
        'media_buttons' => false, // Don't allow images in the bio
        'teeny'         => true,  // Use the minimal "teeny" editor
    ],
]);
```

---

## Code Editor

*   **Type:** `code_editor`
*   **Description:** Provides a powerful, syntax-highlighting code editor powered by the CodeMirror instance built into WordPress core. It's perfect for custom CSS, JavaScript, HTML snippets, or any other code.
*   **Sanitization:** The value is saved as a plain string.

#### Key `extras`

*   `'mode'` (string): **(Required)** Specifies the language for syntax highlighting. The value should be a valid MIME type.
    *   **Common Values:** `'text/css'`, `'application/javascript'`, `'text/html'`, `'application/json'`.
*   `'editor_settings'` (array): An array of advanced settings passed directly to the CodeMirror instance. You can control things like indentation, line numbers, and themes. See the [CodeMirror documentation](https://codemirror.net/doc/manual.html#config) for available options.

#### Example

```php
$page->add_field( 'header_scripts', 'advanced_section', 'code_editor', 'Header Scripts', [
    'description' => 'Add scripts to the <head> of your site. Use with caution.',
    'mode'        => 'text/html',
    'html_attributes' => [
        'rows' => 10,
    ],
    'editor_settings' => [
        'lineNumbers' => true,
        'indentUnit'  => 4,
    ],
]);
```

---

## Media (Single)

*   **Type:** `media`
*   **Description:** Renders a button that opens the WordPress Media Library, allowing the user to select or upload a **single** item. It displays a preview of the selected item.
*   **Value Saved:** The integer ID of the selected attachment (e.g., `123`).

#### Key `extra`: `media_settings`

Use the `'media_settings'` array to customize the Media Library modal window.

*   `'title'` (string): The title displayed at the top of the media modal.
*   `'button_text'` (string): The text on the primary "Select" button in the modal.
*   `'library_type'` (string): Restrict the library to a specific media type. Common values are `'image'`, `'video'`, `'audio'`. Leave empty to allow all types.

#### Example

```php
$page->add_field( 'site_logo', 'branding_section', 'media', 'Site Logo', [
    'description' => 'Upload or select the primary logo for the website.',
    'media_settings' => [
        'title'        => 'Choose a Logo',
        'button_text'  => 'Use this Logo',
        'library_type' => 'image', // Only allow images to be selected
    ],
]);
```

---

## Media (Multiple)

*   **Type:** `media_multiple`
*   **Description:** Similar to the `media` field, but allows the user to select **multiple** items from the Media Library. Previews for all selected items are displayed.
*   **Value Saved:** An indexed array of attachment IDs (e.g., `[123, 456, 789]`).

#### Key `extra`: `media_settings`

The `'media_settings'` options work the same as for the single media field but are best phrased for a multi-selection context.

#### Example

```php
$page->add_field( 'homepage_gallery', 'content_section', 'media_multiple', 'Homepage Gallery Images', [
    'description' => 'Select multiple images to display in the homepage gallery.',
    'media_settings' => [
        'title'       => 'Build Your Gallery',
        'button_text' => 'Add to Gallery',
    ],
]);
```

These fields provide a robust way to manage complex content. The next guide will explore the various date and time picker fields available in the framework.

**Next:** [**7. Fields: Date and Time**](./07-fields-date-and-time.md)