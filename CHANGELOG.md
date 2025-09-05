# Changelog

## 1.0.0 (2025-09-05)

* feat: upload settings builder framework ([184f599](https://github.com/WPTechnix/wp-settings-builder/commit/184f599))

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

---

## [1.0.0] - 2025-09-05

### Added

#### Core Framework & API
- **Fluent, Object-Oriented API**: Introduced `Settings_Builder` and `Settings_Page` classes for a modern, chainable configuration experience.
- **Extensible Field System**: Built on an `Abstract_Field` class, allowing developers to create and register their own custom field types using `Settings_Builder::register_field_class()`.
- **Robust Architecture**: Decoupled services for rendering (`Html_Renderer`), data persistence (`Wp_Options_Persistence`), sanitization (`Sanitizer`), and asset loading (`Asset_Loader`).

#### Page & Layout Configuration
- **Page Customization**: Full control over the settings page location and appearance with `set_page_title()`, `set_menu_title()`, `set_parent_slug()`, and `set_capability()`.
- **Tabbed Interfaces**: Ability to organize complex pages with `add_tab()`, including support for [Dashicons](https://developer.wordpress.org/resource/dashicons/).
- **Sections**: Group related fields together using `add_section()`, with support for titles and descriptions.

#### Comprehensive Field Library (30+ Field Types)
- **Basic Inputs**:
  - `text`: Standard text field.
  - `textarea`: Multi-line text area.
  - `number`: HTML5 number input with attribute support (`min`, `max`, `step`).
  - `password`: Secure field for API keys that masks the value and prevents accidental erasure on save.
  - `email`: HTML5 email input with `sanitize_email` validation.
  - `url`: HTML5 url input with `esc_url_raw` validation.
- **Choice & Toggles**:
  - `checkbox`: Standard checkbox.
  - `switch`: Modern, styled toggle switch.
  - `choice`: A group of radio buttons.
  - `buttons_group`: A styled, segmented button control alternative to radio buttons.
  - `multi_check`: A group of checkboxes for multiple selections.
- **Select2-Powered Dropdowns**:
  - `select`: A searchable, single-selection dropdown.
  - `multi_select`: A searchable, multi-selection dropdown.
- **Content & Media**:
  - `wysiwyg`: The full WordPress TinyMCE editor, with customizable settings.
  - `code_editor`: The WordPress core CodeMirror editor with syntax highlighting for various languages.
  - `media`: A single media selector that opens the WordPress Media Library and shows an image preview.
  - `media_multiple`: A multi-media selector that opens the WordPress Media Library and shows previews.
  - `description`: A pseudo-field for displaying read-only instructions or HTML content.
- **Flatpickr-Powered Date & Time Pickers**:
  - `date`: A calendar for selecting a single date.
  - `time`: A user-friendly time picker.
  - `date_time`: A combination calendar and time picker.
  - `date_range`: A single interface for selecting a start and end date.
  - `date_time_range`: A single interface for selecting a start and end date/time.
- **AJAX-Powered Relational Fields**:
  - `post` / `posts`: AJAX-searchable field to select one or more posts, pages, or CPTs.
  - `user` / `users`: AJAX-searchable field to select one or more WordPress users.
  - `term` / `terms`: AJAX-searchable field to select one or more taxonomy terms (categories, tags, etc.).

#### Advanced Developer Features
- **Programmatic Access**: `get_setting()` and `set_setting()` methods on the `Settings_Page` instance allow for reading and writing option values from anywhere in the code.
- **Custom Validation**: Added a `'validation_callback'` extra for any field, allowing developers to run custom, complex validation logic before a value is saved.

#### Asset Management
- **Automatic Asset Enqueuing**: The framework detects which fields are being used and only enqueues the necessary CSS and JavaScript, keeping admin pages fast.
- **Global Asset Registration**: Added `Settings_Builder::register_asset()` to allow developers to add their own page-wide stylesheets or scripts.
- **Asset Overriding**: Developers can use `register_asset()` with default handles (e.g., `select2-js`) to override the CDN-based libraries with their own local versions.

#### Documentation
- Complete initial documentation covering every public method and field type, including runnable examples and guides for getting started and creating custom fields.
