# 3. Settings Page Configuration

Once you've created a `Settings_Page` instance, you can customize its appearance, location, and structure. This guide covers all the methods for configuring your page before you start adding fields.

---

## Core Page Settings

These methods define the fundamental properties of your settings page, such as its title and where it appears in the WordPress admin menu. They are all chainable, allowing for a clean and readable configuration block.

```php
$page->set_page_title( 'My Plugin Settings' )
      ->set_menu_title( 'My Plugin' )
      ->set_parent_slug( 'tools.php' )
      ->set_capability( 'edit_posts' );
```

### Method Reference

*   `set_page_title( string $title )`
    *   **Description:** Sets the main title displayed in the `<h1>` tag at the top of your settings page.
    *   **Default:** `'Settings'`

*   `set_menu_title( string $title )`
    *   **Description:** Sets the text that appears in the admin menu sidebar. It's often shorter than the page title.
    *   **Default:** `'Settings'`

*   `set_capability( string $capability )`
    *   **Description:** Sets the WordPress capability a user must have to view and save this page.
    *   **Default:** `'manage_options'` (Administrator only)
    *   **Common Values:** `'edit_posts'` (Editor), `'publish_posts'` (Author). See the [WordPress Roles and Capabilities](https://wordpress.org/support/article/roles-and-capabilities/) page for a full list.

*   `set_parent_slug( string $slug )`
    *   **Description:** Determines where your settings page will appear in the admin menu. You can make it a top-level menu item or a submenu item under an existing page.
    *   **Default:** `'options-general.php'` (Under the main **Settings** menu)

#### Common `parent_slug` Values

| Slug | Admin Menu Location |
| :--- | :--- |
| `'options-general.php'` | **Settings** (Default) |
| `'themes.php'` | **Appearance** |
| `'plugins.php'` | **Plugins** |
| `'tools.php'` | **Tools** |
| `'users.php'` | **Users** |
| `'edit.php'` | **Posts** |
| `'edit.php?post_type=page'` | **Pages** |
| `'your-plugin-slug'` | Create a top-level menu by using your page's own slug. Requires a separate `add_menu_page()` call, which is outside the scope of this framework but a common WordPress practice. |

---

## Organizing with Tabs and Sections

For complex pages, organizing settings into tabs and sections is essential for a good user experience. The framework makes this straightforward.

The hierarchy is simple:
1.  You add **Tabs** to the page.
2.  You add **Sections** and assign them to a specific tab.

If you don't add any tabs, sections are rendered directly on the page, one after another.

### Adding a Tab

Use the `add_tab()` method to create a navigation tab at the top of your page.

*   `add_tab( string $id, string $title, ?string $icon = null )`
    *   `$id`: A unique, URL-safe identifier for the tab (e.g., `'general'`, `'advanced_settings'`).
    *   `$title`: The text displayed on the tab.
    *   `$icon` (Optional): A [Dashicons](https://developer.wordpress.org/resource/dashicons/) class name (e.g., `'dashicons-admin-generic'`) to display an icon on the tab.

### Adding a Section

Use the `add_section()` method to create a container for your fields.

*   `add_section( string $id, string $title, ?string $description = null, ?string $tab_id = null )`
    *   `$id`: A unique identifier for the section (e.g., `'api_section'`).
    *   `$title`: The heading displayed for the section.
    *   `$description` (Optional): A short paragraph of text displayed below the section title.
    *   `$tab_id` (Optional): The unique ID of the tab this section should appear under. **This is the crucial link between a section and a tab.**

---

### Example: Building a Tabbed Layout

This example shows how to define the structure for a settings page with two tabs: "General" and "Advanced".

```php
// First, define the tabs
$page->add_tab( 'general_tab', 'General', 'dashicons-admin-settings' );
$page->add_tab( 'advanced_tab', 'Advanced', 'dashicons-admin-generic' );

// Next, add sections and assign them to the tabs using the tab's ID

// -- Sections for the 'General' tab --
$page->add_section(
    'api_section',          // Section ID
    'API Settings',         // Section Title
    'Configure your API credentials here.',
    'general_tab'           // Assigns this section to the 'General' tab
);

$page->add_section(
    'display_section',
    'Display Settings',
    'Set the display options.',
    'general_tab'           // Also on the 'General' tab
);

// -- Section for the 'Advanced' tab --
$page->add_section(
    'caching_section',
    'Caching & Performance',
    'Fine-tune the caching behavior.',
    'advanced_tab'          // Assigns this section to the 'Advanced' tab
);

// Now you can start adding fields to 'api_section', 'display_section', etc.
// $page->add_field( ... );
```

### Example: A Simple Layout (No Tabs)

If your page is simple, just omit the `add_tab()` calls. When you add sections, do not provide the fourth `$tab_id` argument.

```php
// No add_tab() calls are needed.

// Add sections directly to the page.
$page->add_section(
    'main_settings',
    'Main Plugin Settings',
    'All important settings are right here.'
    // The tab_id argument is omitted
);

$page->add_section(
    'other_settings',
    'Other Settings'
);

// Fields added to these sections will appear one after another on the same page.
```

With your page structure defined, you are now ready to populate it with fields. The next guides will cover every available field type in detail.

**Next:** [**4. Fields: Basic Inputs**](./04-fields-basic-inputs.md)