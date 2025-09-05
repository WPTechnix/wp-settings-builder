# 7. Fields: Date and Time

This guide covers the advanced date and time fields, all of which are powered by the lightweight and powerful [Flatpickr](https://flatpickr.js.org/) library. These fields provide a beautiful and user-friendly interface for selecting dates, times, and ranges.

The framework automatically handles enqueuing all necessary styles and scripts when you use any of these fields.

---

## Core Concepts

A key principle for these fields is the separation of **display format** from **storage format**.

*   **Storage Format:** Dates and times are always saved to the database in a standardized, machine-readable format (e.g., `YYYY-MM-DD HH:MM:SS`). This ensures data consistency and makes it easy to perform reliable database queries.
*   **Display Format:** You can define a separate, human-readable format for how the date or time is displayed to the user in the admin interface (e.g., "December 25, 2025").

The framework handles the translation between these two formats automatically.

---

## Common `extras`

The following keys in the `$extras` array are used to configure the date and time fields:

*   `'default'` (string|array): The default value. The format must match the field's specific **storage format** (e.g., `'2025-12-25'` for a date field, or `['2025-12-25', '2025-12-31']` for a date range field).
*   `'display_format'` (string): A [PHP `date()` format string](https://www.php.net/manual/en/datetime.format.php) that defines how the value should appear to the user. The framework automatically translates this to the correct Flatpickr format string. If not provided, a sensible default is used.
*   `'flatpickr_options'` (array): An associative array of advanced options passed directly to the Flatpickr JavaScript instance. This allows for deep customization. See the [Flatpickr Options documentation](https://flatpickr.js.org/options/) for a complete list.
    *   **Common examples:** `['minDate' => 'today']`, `['time_24hr' => true]`.

---

## Date

*   **Type:** `date`
*   **Description:** A field for selecting a single calendar date.
*   **Storage Format:** A string in `Y-m-d` format (e.g., `'2025-10-26'`).

#### Example

```php
$page->add_field( 'start_date', 'event_section', 'date', 'Event Start Date', [
    'description' => 'The date the event begins.',
    'default'     => date('Y-m-d'), // Defaults to today
    'display_format' => 'F j, Y', // Displays as "October 26, 2025"
    'flatpickr_options' => [
        'minDate' => 'today', // Disables selection of past dates
    ],
]);
```

---

## Time

*   **Type:** `time`
*   **Description:** A field for selecting a single time of day.
*   **Storage Format:** A string in 24-hour `H:i:s` format (e.g., `'14:30:00'`).

#### Example

```php
$page->add_field( 'opening_time', 'hours_section', 'time', 'Opening Time', [
    'description' => 'Select the store\'s opening time.',
    'default'     => '09:00:00',
    'display_format' => 'h:i K', // Displays as "09:00 AM"
]);
```

---

## Date & Time

*   **Type:** `date_time`
*   **Description:** A combination field for selecting both a date and a time.
*   **Storage Format:** A string in `Y-m-d H:i:s` format (e.g., `'2025-11-27 09:00:00'`).

#### Example

```php
$page->add_field( 'sale_starts', 'event_section', 'date_time', 'Sale Starts On', [
    'description' => 'The exact date and time the sale will become active.',
    'display_format' => 'M j, Y @ h:i A', // Displays as "Nov 27, 2025 @ 09:00 AM"
    'flatpickr_options' => [
        'enableSeconds' => false,
        'minuteIncrement' => 15,
    ],
]);
```

---

## Date Range

*   **Type:** `date_range`
*   **Description:** Allows the user to select a start date and an end date in a single interface.
*   **Storage Format:** A JSON-encoded array containing two `Y-m-d` strings.
    *   Example: `["2025-07-04","2025-07-11"]`

#### Example

```php
$page->add_field( 'vacation_period', 'booking_section', 'date_range', 'Vacation Period', [
    'description' => 'Select the start and end dates of the booking.',
    // The default value must be a PHP array of strings
    'default' => ['2025-08-01', '2025-08-15'],
    'display_format' => 'M j, Y',
]);
```

---

## Date & Time Range

*   **Type:** `date_time_range`
*   **Description:** Allows the user to select a start date/time and an end date/time.
*   **Storage Format:** A JSON-encoded array containing two `Y-m-d H:i:s` strings.
    *   Example: `["2025-12-24 18:00:00","2025-12-26 09:00:00"]`

#### Example

```php
$page->add_field( 'maintenance_window', 'advanced_section', 'date_time_range', 'Maintenance Window', [
    'description' => 'The website may be unavailable during this period.',
    'display_format' => 'M j, h:i K',
]);
```

With these fields, you can capture any date or time-based data you need. The next guide covers relational fields, which allow you to link to other WordPress content like posts, users, and terms.

**Next:** [**8. Fields: Relational (AJAX)**](./08-fields-relational-ajax.md)