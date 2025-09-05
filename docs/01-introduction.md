# 1. Introduction

Welcome to the documentation for the WP Settings Builder Framework!

This framework was created to solve a common problem for WordPress developers: building and maintaining settings pages is often a tedious and messy process. The native WordPress Settings API is powerful but procedural, leading to code that can be hard to read, maintain, and extend over time.

The WP Settings Builder abstracts away this complexity behind a modern, fluent, and object-oriented API. It allows you to define your settings pages in a structured, readable way, letting you focus on what your plugin does best, not on boilerplate admin UI code.

---

## Key Features

This framework is designed to be both simple to start with and powerful enough for complex plugins.

*   ✅ **Fluent, Modern API**: A clean and readable interface for defining pages, tabs, sections, and fields in a chainable, logical sequence.
*   🚀 **Comprehensive Field Library**: Over 30 field types are included out-of-the-box, from simple text inputs to advanced AJAX-powered relationship pickers.
*   🧠 **Built-in Conditional Logic**: Natively show or hide fields based on the values of other fields using a simple and powerful rules engine, without writing any JavaScript.
*   ⚙️ **Automatic Asset Management**: CSS and JavaScript for fields like color pickers, date pickers, and Select2 are loaded automatically only when needed, keeping your admin pages fast.
*   ⚡ **AJAX-Powered Fields**: Easily add searchable fields for Posts, Pages, Users, and Terms that load data on the fly, ensuring excellent performance even with thousands of items.
*   🎨 **Flexible Layouts**: Effortlessly create complex tabbed interfaces with Dashicons or simple, single-column settings pages.
*   🔒 **Secure & Robust**: Comes with built-in sanitization for all standard field types, secure handling of sensitive data (like password/API key fields), and is fully type-hinted for modern PHP development.
*   🧩 **Extensible**: The framework is built on a solid foundation of interfaces and abstract classes, making it straightforward to register your own custom field types.

---

## Core Concepts

Understanding a few core concepts will help you master the framework quickly.

*   **`Settings_Builder`**: This is the main entry point to the framework. Its primary job is to `create()` a new `Settings_Page` instance for you.

*   **`Settings_Page`**: This is the object you will interact with the most. It holds all the configuration for your page and provides the fluent methods like `set_page_title()`, `add_tab()`, `add_section()`, and `add_field()`.

*   **The Hierarchy**: Your page is built in a logical hierarchy:
    *   A **Page** can have multiple **Tabs**.
    *   A **Tab** can contain multiple **Sections**.
    *   A **Section** can contain multiple **Fields**.
    *   *(If you don't define tabs, sections are added directly to the page).*

*   **`init()`**: This is the final and most important method you'll call on your `Settings_Page` instance. It takes all of your configuration, builds the necessary objects behind the scenes, and registers all the required hooks with WordPress to make your page come to life.

---

## How to Read These Docs

We recommend reading the documentation in order, as each guide builds upon the concepts of the previous one. However, if you're an experienced developer, feel free to jump to the section that interests you most.

*   **Getting Started** will walk you through creating your very first, simple settings page.
*   **Settings Page Configuration** covers all the methods for customizing your page, menu item, tabs, and sections.
*   The **Fields** guides are a detailed reference for every available field type and their specific options.
*   **Conditional Logic** and the **Advanced Guide** cover the framework's most powerful features.

Ready to dive in? Let's get your first page built.

**Next:** [**2. Getting Started**](./02-getting-started.md)