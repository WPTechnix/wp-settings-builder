# 8. Fields: Relational (AJAX)

Relational fields are one of the most powerful features of the WP Settings Builder. They allow you to create connections to other types of content within WordPress, such as posts, pages, users, or taxonomy terms.

All relational fields are powered by AJAX, which means they are incredibly fast and efficient, even on sites with thousands of posts or users. They use the Select2 library to provide a searchable, user-friendly interface that loads results on the fly as the user types.

---

## Core Concept: `query_args`

The behavior of all relational fields is controlled by the `'query_args'` key in the `$extras` array. This array is passed directly to the underlying WordPress query function (`WP_Query`, `WP_User_Query`, or `get_terms`), giving you precise control over which items are available for selection.

---

## Post (Single)

*   **Type:** `post`
*   **Description:** A searchable dropdown for selecting a **single** post, page, or custom post type.
*   **Value Saved:** The integer ID of the selected post (e.g., `42`).

#### Key `extra`: `query_args`

The `'query_args'` array is passed to `WP_Query`. You can use any valid `WP_Query` parameter.

*   **Most Common Use:** `'post_type'` to specify what to search for. By default, it searches for `'post'`.

#### Example: Select a single Page

```php
$page->add_field( 'featured_page', 'homepage_section', 'post', 'Featured Page', [
    'description' => 'Select a page to feature on the homepage.',
    'query_args' => [
        'post_type' => 'page',
    ],
]);
```

---

## Posts (Multiple)

*   **Type:** `posts`
*   **Description:** A searchable dropdown for selecting **multiple** posts, pages, or custom post types.
*   **Value Saved:** An indexed array of post IDs (e.g., `[42, 128, 310]`).

#### Key `extra`: `query_args`

Works identically to the single `post` field.

#### Example: Select multiple "Product" CPT items

```php
$page->add_field( 'related_products', 'product_section', 'posts', 'Related Products', [
    'description' => 'Select products to show as related items.',
    'query_args' => [
        'post_type' => 'product',
        'post_status' => 'publish',
    ],
]);
```

---

## User (Single)

*   **Type:** `user`
*   **Description:** A searchable dropdown for selecting a **single** WordPress user.
*   **Value Saved:** The integer ID of the selected user.

#### Key `extra`: `query_args`

The `'query_args'` array is passed to `WP_User_Query`. You can use any of its valid parameters.

*   **Most Common Use:** `'role'` or `'role__in'` to filter users by their role.

#### Example: Select a single user with the "Editor" role

```php
$page->add_field( 'content_approver', 'workflow_section', 'user', 'Content Approver', [
    'description' => 'This user will be responsible for approving new content.',
    'query_args' => [
        'role' => 'editor',
    ],
]);
```

---

## Users (Multiple)

*   **Type:** `users`
*   **Description:** A searchable dropdown for selecting **multiple** WordPress users.
*   **Value Saved:** An indexed array of user IDs.

#### Example: Select multiple users from different roles

```php
$page->add_field( 'project_team', 'project_section', 'users', 'Project Team', [
    'description' => 'Assign team members to this project.',
    'query_args' => [
        'role__in' => ['editor', 'author'],
    ],
]);
```

---

## Term (Single)

*   **Type:** `term`
*   **Description:** A searchable dropdown for selecting a **single** taxonomy term (e.g., a category or a tag).
*   **Value Saved:** The integer ID of the selected term.

#### Key `extra`: `query_args`

The `'query_args'` array is passed to `get_terms()`. You can use any of its valid parameters.

*   **Most Common Use:** `'taxonomy'` **(Required)** to tell the field which taxonomy to search within.

#### Example: Select a single "Category"

```php
$page->add_field( 'default_category', 'general_section', 'term', 'Default Post Category', [
    'description' => 'New posts will be assigned to this category by default.',
    'query_args' => [
        'taxonomy' => 'category',
        'hide_empty' => false, // Show empty categories
    ],
]);
```

---

## Terms (Multiple)

*   **Type:** `terms`
*   **Description:** A searchable dropdown for selecting **multiple** taxonomy terms.
*   **Value Saved:** An indexed array of term IDs.

#### Example: Select multiple "post_tag" terms

```php
$page->add_field( 'post_tags', 'post_section', 'terms', 'Default Tags', [
    'description' => 'These tags will be automatically added to new posts.',
    'query_args' => [
        'taxonomy' => 'post_tag',
    ],
]);
```

These fields simplify the process of creating dynamic, interconnected settings. Next, we will explore one of the framework's most powerful features: conditional logic.

**Next:** [**9. Conditional Logic**](./09-conditional-logic.md)