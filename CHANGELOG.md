# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [1.0.0] - 2025-09-05

### Features
- Introduced `Settings_Builder` and `Settings_Page` classes for a modern, chainable configuration experience.
- Built an extensible field system on an `Abstract_Field` class, allowing developers to create and register custom fields.
- Decoupled services for rendering (`Html_Renderer`), data persistence (`Wp_Options_Persistence`), sanitization (`Sanitizer`), and asset loading (`Asset_Loader`).
- Added page customization methods: `set_page_title()`, `set_menu_title()`, `set_parent_slug()`, `set_capability()`.
- Added support for tabbed interfaces (`add_tab()`) with Dashicons and sections (`add_section()`) with titles and descriptions.
- Added 30+ field types:
    - Basic inputs: `text`, `textarea`, `number`, `password`, `email`, `url`.
    - Choice & toggles: `checkbox`, `switch`, `choice`, `buttons_group`, `multi_check`.
    - Select2 dropdowns: `select`, `multi_select`.
    - Content & media: `wysiwyg`, `code_editor`, `media`, `media_multiple`, `description`.
    - Flatpickr date & time pickers: `date`, `time`, `date_time`, `date_range`, `date_time_range`.
    - AJAX relational fields: `post`/`posts`, `user`/`users`, `term`/`terms`.
- Added programmatic access via `get_setting()`/`set_setting()` methods and custom validation callbacks.
- Added automatic asset enqueuing and global asset registration with `Settings_Builder::register_asset()`.
- Complete initial documentation with examples for all public methods and field types.

### Bug Fixes
- None for this initial release.

### Chores
- Initial release of the framework.
