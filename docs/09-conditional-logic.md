# 9. Conditional Logic

Conditional logic is a powerful feature that allows you to create dynamic and interactive settings pages. It lets you show or hide certain fields based on the values of other fields, creating a cleaner, more intuitive user experience.

Best of all, this is a native feature of the framework. **You do not need to write any custom JavaScript.**

---

## How It Works

You can make any field conditional by adding a `'conditions'` key to its `$extras` array. The framework will then automatically:

1.  Wrap the field's HTML in a container with special `data-` attributes.
2.  Use a built-in script to monitor the form for changes.
3.  Show or hide the field in real-time as the conditions are met or unmet.

---

## The `conditions` Array Structure

The `'conditions'` array has a simple but powerful structure with two main keys: `'relation'` and `'rules'`.

```php
'conditions' => [
    'relation' => 'AND', // or 'OR'
    'rules'    => [
        // ... one or more rule arrays go here ...
    ],
],
```

*   `'relation'` (string): Determines how multiple rules are combined.
    *   `'AND'`: **All** rules must be true for the field to be shown.
    *   `'OR'`: **Any** of the rules can be true for the field to be shown.
    *   (If you only have one rule, the relation doesn't matter).

*   `'rules'` (array): An array of one or more "rule" arrays. Each rule defines a single check to perform.

### The Rule Array Structure

Each rule inside the `'rules'` array is an associative array with three keys:

*   `'field'` (string): The unique **ID** of the field you want to check the value of.
*   `'operator'` (string): The comparison operator to use.
*   `'value'` (mixed): The value to compare against.

---

## Available Operators

| Operator | Description | Works With | Example `value` |
| :--- | :--- | :--- | :--- |
| `=` or `==` | **Equals**. The source field's value must equal the rule's value. (Coercive, so `"1"` equals `1`). | String, Number, Boolean | `'standard'`, `true`, `10` |
| `!=` | **Not Equals**. The source field's value must not equal the rule's value. | String, Number, Boolean | `'standard'`, `false` |
| `>` | **Greater Than**. The source field's value must be numerically greater. | Number | `100` |
| `>=` | **Greater Than or Equal To**. | Number | `100` |
| `<` | **Less Than**. The source field's value must be numerically smaller. | Number | `50` |
| `<=` | **Less Than or Equal To**. | Number | `50` |
| `IN` | **Is one of**. The source field's value must be present in the rule's array of values. | String, Number | `['standard', 'express']` |
| `NOT IN` | **Is not one of**. The source field's value must not be present in the rule's array. | String, Number | `['none', 'disabled']` |

---

## Practical Examples

### Example 1: Simple Show/Hide with a Switch

This is the most common use case. A text field is shown only when a `switch` is turned on.

```php
// The controlling field
$page->add_field( 
    'enable_welcome_message', 
    'general_section', 
    'switch', 
    'Enable Welcome Message', 
    [
        'default' => false,
    ]
);

// The conditional field
$page->add_field( 
    'welcome_message_text', 
    'general_section',
    'text', 
    'Custom Welcome Message', 
    [
        'description' => 'This field only appears if the switch above is enabled.',
        'conditions' => [
            'rules' => [
                [
                    'field'    => 'enable_welcome_message', // Check the switch's ID
                    'operator' => '=',
                    'value'    => true, // Check if it's true (on)
                ],
            ],
        ],
    ]
);
```

### Example 2: Multiple Conditions with `AND`

Show a field only if a feature is enabled **AND** a specific layout is chosen.

```php
$page->add_field( 
    'layout_mode', 
    'display_section', 
    'choice', 
    'Layout Mode', 
    [
        'default' => 'standard',
        'options' => ['standard' => 'Standard', 'image' => 'Image Header'],
    ]
);

$page->add_field( 
    'header_image', 
    'display_section', 
    'media', 
    'Header Image', 
    [
        'description' => 'Appears only if the switch is on AND the layout is "Image Header".',
        'conditions' => [
            'relation' => 'AND',
            'rules'    => [
                [
                    'field'    => 'enable_welcome_message', // Rule 1
                    'operator' => '=',
                    'value'    => true,
                ],
                [
                    'field'    => 'layout_mode', // Rule 2
                    'operator' => '=',
                    'value'    => 'image',
                ],
            ],
        ],
    ]
);
```

### Example 3: Multiple Conditions with `OR`

Show a field if the delivery method is "Express" **OR** "Priority".

```php
$page->add_field( 
    'delivery_method', 
    'shipping_section', 
    'choice', 
    'Delivery Method', 
    [
        'default' => 'standard',
        'options' => ['standard' => 'Standard', 'express' => 'Express', 'priority' => 'Priority'],
    ]
);

$page->add_field( 
    'rush_handling_notes', 
    'shipping_section', 
    'textarea', 
    'Rush Handling Notes', 
    [
        'description' => 'Appears for Express or Priority shipping.',
        'conditions' => [
            'relation' => 'OR',
            'rules'    => [
                [ 'field' => 'delivery_method', 'operator' => '=', 'value' => 'express' ],
                [ 'field' => 'delivery_method', 'operator' => '=', 'value' => 'priority' ],
            ],
        ],
    ]
);
```

### Example 4: Using the `IN` Operator (A better `OR`)

The example above can be written more cleanly using the `IN` operator. This is the recommended way to check against multiple possible values.

```php
$page->add_field( 
    'rush_handling_notes', 
    'shipping_section', 
    'textarea', 
    'Rush Handling Notes',
    [
        'description' => 'Appears for Express or Priority shipping.',
        'conditions' => [
            'rules' => [
                [
                    'field'    => 'delivery_method',
                    'operator' => 'IN',
                    'value'    => ['express', 'priority'], // Check if the value is in this array
                ],
            ],
        ],
    ]
);
```

The `IN` operator also works perfectly with `multi_select` or `multi_check` fields, showing a field if any of the selected options are in the `value` array.

### Example 5: Numeric Comparison

Show a "High Volume Warning" if the number of items exceeds 100.

```php
$page->add_field( 
    'item_count', 
    'inventory_section', 
    'number', 
    'Item Count', 
    [
        'default' => 50,
    ]
);

$page->add_field( 
    'high_volume_warning', 
    'inventory_section', 
    'description', 
    'High Volume Warning', 
    [
        'description' => '<strong style="color: red;">Warning: High item counts may impact performance.</strong>',
        'conditions'  => [
            'rules' => [
                [
                    'field'    => 'item_count',
                    'operator' => '>',
                    'value'    => 100,
                ],
            ],
        ],
    ]
);
```

With conditional logic, you can build sophisticated, user-friendly interfaces that adapt to your user's choices. The next guide will cover advanced topics, including how to create and register your own custom field types.

**Next:** [**10. Advanced Guide**](./10-advanced-guide.md)