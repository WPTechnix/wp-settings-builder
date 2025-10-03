# Changelog

## <small>1.0.1 (2025-10-03)</small>

* fix: text field sanitization logic ([ffb7d53](https://github.com/WPTechnix/wp-settings-builder/commit/ffb7d53))
* chore: update project description in README.md and composer.json ([3630adf](https://github.com/WPTechnix/wp-settings-builder/commit/3630adf))

## 1.0.0 (2025-09-05)

### Features

- Introduced `Settings_Builder` and `Settings_Page` classes for a modern, chainable configuration experience.
- Added an extensible field system via the `Abstract_Field` class, enabling custom field registration.
- Decoupled core services:
    - `Html_Renderer` (markup rendering)
    - `Wp_Options_Persistence` (option storage)
    - `Sanitizer` (data validation)
    - `Asset_Loader` (script/style management)
- Added page configuration methods:
    - `set_page_title()`
    - `set_menu_title()`
    - `set_parent_slug()`
    - `set_capability()`
- Added tabbed UI support with `add_tab()` (including Dashicons), and section-based layout with `add_section()`.
- Implemented 30+ field types:
    - **Basic inputs:** `text`, `textarea`, `number`, `password`, `email`, `url`
    - **Choices & toggles:** `checkbox`, `switch`, `choice`, `buttons_group`, `multi_check`
    - **Select2 dropdowns:** `select`, `multi_select`
    - **Content & media:** `wysiwyg`, `code_editor`, `media`, `media_multiple`, `description`
    - **Date/time pickers (Flatpickr):** `date`, `time`, `date_time`, `date_range`, `date_time_range`
    - **AJAX-driven relations:** `post`, `posts`, `user`, `users`, `term`, `terms`
- Enabled programmatic setting access via `get_setting()` / `set_setting()`.
- Added support for custom validation callbacks.
- Enabled automatic asset enqueuing and global asset registration using `Settings_Builder::register_asset()`.

### Documentation

- Added full initial documentation with examples for all public methods and field types.
