<?php
/**
 * Creates a simple settings page without tabs.
 *
 * @package My_Plugin\Admin
 */

declare( strict_types=1 );

namespace My_Plugin\Admin;

use WPTechnix\WP_Settings_Builder\Settings_Builder;
use WPTechnix\WP_Settings_Builder\Settings_Page;

/**
 * Class Plugin_Settings_Simple
 *
 * A concrete example of a non-tabbed settings page using the WP Settings Builder.
 */
final class Plugin_Settings_Simple {

    /**
     * The unique option name where all settings are stored.
     *
     * @var string
     */
    private const OPTION_NAME = 'wptx_settings_no_tabs';

    /**
     * The unique page slug for the settings page.
     *
     * @var string
     */
    private const PAGE_SLUG = 'wptx-settings-no-tabs';

    /**
     * Initializes the settings page by building its structure and hooking into WordPress.
     */
    public function init(): void {

        // 1. Create a new settings page instance.
        $page = ( new Settings_Builder() )->create( self::OPTION_NAME, self::PAGE_SLUG );

        // 2. Configure the page properties.
        $page->set_page_title( 'All Fields example' )
             ->set_menu_title( 'Simple Settings' )
             ->set_parent_slug( 'options-general.php' ) // Places it under the main "Settings" menu
             ->set_capability( 'manage_options' );

        // 3. Add a single section to hold all the fields.
        $page->add_section(
            'main_section',
            'Comprehensive Field Examples',
            'This section demonstrates every available field type with detailed descriptions and practical configurations.'
        );

        // 4. Add all the fields to the section.
        $this->add_all_fields( $page );

        // 5. Build and register the page with WordPress.
        $page->init();
    }

    /**
     * Adds one of each field type to the settings page.
     *
     * @param Settings_Page $page The settings page instance.
     */
    private function add_all_fields( Settings_Page $page ): void {

        $section_id = 'main_section';

        // Description Field.
        $page->add_field(
            'description_field_1',
            $section_id,
            'description',
            'Descriptive Text',
            [
                'description' => 'The <strong>Description Field</strong> is used to display instructional text, notices, or separators. It does not save any data. You can use basic HTML for formatting.',
            ]
        );

        // Text Field.
        $page->add_field(
            'text_field',
            $section_id,
            'text',
            'Text Field',
            [
                'description'     => 'A standard input for single-line text. Ideal for names, titles, or short phrases.',
                'default'         => 'Default Value',
                'html_attributes' => [
                    'placeholder' => 'Enter your business name',
                ],
            ]
        );

        // URL Field.
        $page->add_field(
            'url_field',
            $section_id,
            'url',
            'URL Field',
            [
                'description'     => 'Specifically for URLs. The input will be sanitized using <code>esc_url_raw()</code>.',
                'default'         => 'https://wptechnix.com',
                'html_attributes' => [
                    'placeholder' => 'https://example.com',
                ],
            ]
        );

        // Email Field.
        $page->add_field(
            'email_field',
            $section_id,
            'email',
            'Email Field',
            [
                'description'     => 'For email addresses. The input is validated and sanitized as a proper email.',
                'default'         => 'admin@example.com',
                'html_attributes' => [
                    'placeholder' => 'your-email@address.com',
                ],
            ]
        );

        // Number Field.
        $page->add_field(
            'number_field',
            $section_id,
            'number',
            'Number Field with Validation',
            [
                'description'         => 'For numeric values. You can use <code>html_attributes</code> to set min, max, and step values. This example also includes a custom validation callback.',
                'default'             => 10,
                'html_attributes'     => [
                    'min'  => 0,
                    'max'  => 100,
                    'step' => 5,
                ],
                'validation_callback' => static function ( $value ) {
                    if ( (int) $value > 50 ) {
                        return 'The number cannot be greater than 50.';
                    }

                    return true;
                },
            ]
        );

        // Password Field.
        $page->add_field(
            'password_field',
            $section_id,
            'password',
            'Password / API Key',
            [
                'description' => 'A secure field for sensitive data like passwords or API keys. The value is never displayed in the HTML. If a value is saved, asterisks of the same length are shown as a placeholder.',
            ]
        );

        // Textarea Field.
        $page->add_field(
            'textarea_field',
            $section_id,
            'textarea',
            'Textarea Field',
            [
                'description'     => 'A field for longer, multi-line text. You can control its size with <code>rows</code> and <code>cols</code> in <code>html_attributes</code>.',
                'default'         => "This is some default text spanning multiple lines.\nIt's great for custom CSS, bio text, or notes.",
                'html_attributes' => [
                    'rows'        => 6,
                    'placeholder' => 'Enter your custom CSS here...',
                ],
            ]
        );

        // Checkbox Field.
        $page->add_field(
            'checkbox_field',
            $section_id,
            'checkbox',
            'Checkbox Field',
            [
                'description' => 'Enable this to activate a specific feature. The description text is clickable.',
                'default'     => true,
            ]
        );

        // Switch Field.
        $page->add_field(
            'switch_field',
            $section_id,
            'switch',
            'Switch Toggle',
            [
                'description' => 'A modern, styled toggle switch. Functionally identical to a checkbox.',
                'default'     => false,
            ]
        );

        // Color Field.
        $page->add_field(
            'color_field',
            $section_id,
            'color',
            'Color Picker',
            [
                'description' => 'Uses the native WordPress color picker for selecting a hex color value.',
                'default'     => '#3858e9',
            ]
        );

        // Choice (Radio) Field.
        $page->add_field(
            'choice_field',
            $section_id,
            'choice',
            'Choice (Radio Buttons)',
            [
                'description' => 'For selecting a single option from a list. Use the <code>options</code> extra to define choices.',
                'default'     => 'medium',
                'options'     => [
                    'low'    => 'Low Priority',
                    'medium' => 'Medium Priority',
                    'high'   => 'High Priority',
                ],
            ]
        );

        // Buttons Group Field.
        $page->add_field(
            'buttons_group_field',
            $section_id,
            'buttons_group',
            'Buttons Group',
            [
                'description' => 'A visually appealing alternative to radio buttons, styled as a segmented control.',
                'default'     => 'grid',
                'options'     => [
                    'list' => 'List View',
                    'grid' => 'Grid View',
                    'card' => 'Card View',
                ],
            ]
        );

        // Select Field.
        $page->add_field(
            'select_field',
            $section_id,
            'select',
            'Select Dropdown (Select2)',
            [
                'description' => 'A searchable dropdown for selecting a single option. Enhanced with the Select2 library.',
                'default'     => 'usa',
                'options'     => [
                    'usa'    => 'United States',
                    'canada' => 'Canada',
                    'uk'     => 'United Kingdom',
                    'au'     => 'Australia',
                ],
            ]
        );

        // Multi-Check Field.
        $page->add_field(
            'multi_check_field',
            $section_id,
            'multi_check',
            'Multi-Checkbox Field',
            [
                'description' => 'Allows selecting multiple options using checkboxes.',
                'default'     => [ 'analytics', 'marketing' ],
                'options'     => [
                    'analytics'   => 'Analytics Cookies',
                    'marketing'   => 'Marketing Cookies',
                    'preferences' => 'Preference Cookies',
                    'necessary'   => 'Necessary Cookies',
                ],
            ]
        );

        // Multi-Select Field.
        $page->add_field(
            'multi_select_field',
            $section_id,
            'multi_select',
            'Multi-Select Dropdown (Select2)',
            [
                'description' => 'A searchable dropdown for selecting multiple options. Enhanced with Select2.',
                'default'     => [ 'editor', 'author' ],
                'options'     => [
                    'administrator' => 'Administrator',
                    'editor'        => 'Editor',
                    'author'        => 'Author',
                    'contributor'   => 'Contributor',
                    'subscriber'    => 'Subscriber',
                ],
            ]
        );

        // WYSIWYG Editor.
        $page->add_field(
            'wysiwyg_field',
            $section_id,
            'wysiwyg',
            'WYSIWYG Editor',
            [
                'description'     => 'The classic WordPress rich text editor. You can customize it via <code>editor_settings</code>.',
                'default'         => '<h2>Welcome!</h2><p>This is the default content for the rich text editor. You can <strong>bold</strong> text, add lists, and more.</p>',
                'editor_settings' => [
                    'teeny'         => true,
                    'media_buttons' => false,
                    'textarea_rows' => 7,
                ],
            ]
        );

        // Code Editor (CSS).
        $page->add_field(
            'code_editor_css',
            $section_id,
            'code_editor',
            'Code Editor (CSS)',
            [
                'description' => 'A syntax-highlighting code editor powered by CodeMirror. This instance is configured for CSS.',
                'default'     => "body {\n\tfont-family: sans-serif;\n\tcolor: #333;\n}",
                'mode'        => 'text/css', // Sets the language mode
            ]
        );

        // Code Editor (JS).
        $page->add_field(
            'code_editor_js',
            $section_id,
            'code_editor',
            'Code Editor (JavaScript)',
            [
                'description'     => 'Another code editor instance, this time for JavaScript. You can also pass custom CodeMirror settings via <code>editor_settings</code>.',
                'default'         => "document.addEventListener('DOMContentLoaded', () => {\n\tconsole.log('DOM Ready!');\n});",
                'mode'            => 'application/javascript',
                'editor_settings' => [
                    'indentUnit' => 4,
                    'tabSize'    => 4,
                ],
            ]
        );

        // Date Field.
        $page->add_field(
            'date_field',
            $section_id,
            'date',
            'Date Picker',
            [
                'description'    => 'A user-friendly date picker powered by Flatpickr. Dates are stored in <code>Y-m-d</code> format. Use <code>display_format</code> to change how it appears to the user.',
                'default'        => '2025-12-25',
                'display_format' => 'M j, Y', // PHP date format for display.
            ]
        );

        // Time Field.
        $page->add_field(
            'time_field',
            $section_id,
            'time',
            'Time Picker',
            [
                'description'       => 'A time picker. Time is stored in <code>H:i:s</code> format. Customize the display with <code>display_format</code> and other options with <code>flatpickr_options</code>.',
                'default'           => '14:30:00',
                'display_format'    => 'h:i K', // e.g., 02:30 PM
                'flatpickr_options' => [
                    'time_24hr' => false,
                ],
            ]
        );

        // Date & Time Field.
        $page->add_field(
            'datetime_field',
            $section_id,
            'date_time',
            'Date & Time Picker',
            [
                'description'    => 'A combination date and time picker. Stored as <code>Y-m-d H:i:s</code>.',
                'default'        => '2026-01-01 10:00:00',
                'display_format' => 'M j, Y @ h:i A',
            ]
        );

        // Date Range Field.
        $page->add_field(
            'date_range_field',
            $section_id,
            'date_range',
            'Date Range Picker',
            [
                'description'    => 'Allows selecting a start and end date. The value is stored as a JSON array: <code>["YYYY-MM-DD","YYYY-MM-DD"]</code>.',
                'default'        => [ '2025-07-04', '2025-07-11' ],
                'display_format' => 'M j, Y',
            ]
        );

        // Date & Time Range Field.
        $page->add_field(
            'datetime_range_field',
            $section_id,
            'date_time_range',
            'Date & Time Range Picker',
            [
                'description'    => 'Allows selecting a start and end date/time. The value is stored as a JSON array: <code>["YYYY-MM-DD HH:MM:SS","YYYY-MM-DD HH:MM:SS"]</code>.',
                'default'        => [ '2025-11-27 09:00:00', '2025-11-28 17:00:00' ],
                'display_format' => 'M j, h:i K',
            ]
        );

        // Media (Single).
        $page->add_field(
            'media_field',
            $section_id,
            'media',
            'Media (Single Image)',
            [
                'description'    => 'Opens the WordPress Media Library to select a single item. Stores the attachment ID. Use <code>media_settings</code> to customize the library.',
                'media_settings' => [
                    'title'        => 'Choose a Logo',
                    'button_text'  => 'Select Logo',
                    'library_type' => 'image', // Restrict to images only
                ],
            ]
        );

        // Media (Multiple).
        $page->add_field(
            'media_multiple_field',
            $section_id,
            'media_multiple',
            'Media (Multiple, Gallery)',
            [
                'description'    => 'Select multiple items from the Media Library. Stores an array of attachment IDs.',
                'media_settings' => [
                    'title'       => 'Build Your Gallery',
                    'button_text' => 'Add to Gallery',
                ],
            ]
        );

        // Post (Single).
        $page->add_field(
            'post_field',
            $section_id,
            'post',
            'Post (Single, AJAX)',
            [
                'description' => 'An AJAX-powered, searchable dropdown to select a single post. Use <code>query_args</code> to filter the results (e.g., by post type). Stores the post ID.',
                'query_args'  => [
                    'post_type' => 'page',
                ],
            ]
        );

        // Posts (Multiple).
        $page->add_field(
            'posts_field',
            $section_id,
            'posts',
            'Posts (Multiple, AJAX)',
            [
                'description' => 'Select multiple posts with an AJAX-powered search. Stores an array of post IDs.',
                'query_args'  => [
                    'post_type' => 'post',
                ],
            ]
        );

        // User (Single).
        $page->add_field(
            'user_field',
            $section_id,
            'user',
            'User (Single, AJAX)',
            [
                'description' => 'An AJAX-powered search to select a single user. Use <code>query_args</code> to filter by role. Stores the user ID.',
                'query_args'  => [
                    'role' => 'administrator',
                ],
            ]
        );

        // Users (Multiple).
        $page->add_field(
            'users_field',
            $section_id,
            'users',
            'Users (Multiple, AJAX)',
            [
                'description' => 'Select multiple users via an AJAX search. Stores an array of user IDs.',
            ]
        );

        // Term (Single).
        $page->add_field(
            'term_field',
            $section_id,
            'term',
            'Term (Single, AJAX)',
            [
                'description' => 'An AJAX-powered search to select a single term (e.g., a category or tag). Use <code>query_args</code> to specify the taxonomy. Stores the term ID.',
                'query_args'  => [
                    'taxonomy' => 'category',
                ],
            ]
        );

        // Terms (Multiple).
        $page->add_field(
            'terms_field',
            $section_id,
            'terms',
            'Terms (Multiple, AJAX)',
            [
                'description' => 'Select multiple terms via an AJAX search. Stores an array of term IDs.',
                'query_args'  => [
                    'taxonomy' => 'post_tag',
                ],
            ]
        );

        // Switch Field (Our primary trigger for other fields).
        $page->add_field(
            'enable_customizations',
            $section_id,
            'switch',
            'Enable Advanced Customizations',
            [
                'description' => 'Turn this on to reveal more specific settings below.',
                'default'     => false,
            ]
        );

        // Conditional Text Field.
        $page->add_field(
            'conditional_text_field',
            $section_id,
            'text',
            'Custom Welcome Message',
            [
                'description'     => 'This text will appear only when the switch above is enabled.',
                'html_attributes' => [ 'placeholder' => 'Hello, World!' ],
                'conditions'      => [
                    'relation' => 'AND',
                    'rules'    => [
                        [
                            'field'    => 'enable_customizations',
                            'operator' => '=',
                            'value'    => true,
                        ],
                    ],
                ],
            ]
        );

        // Buttons Group Field (Another trigger).
        $page->add_field(
            'layout_mode',
            $section_id,
            'buttons_group',
            'Content Layout Mode',
            [
                'description' => 'Choose how content should be displayed. This choice will reveal other options.',
                'default'     => 'standard',
                'options'     => [
                    'standard' => 'Standard',
                    'image'    => 'Image-focused',
                    'text'     => 'Text-only',
                ],
                // Also conditional on the main switch!
                'conditions'  => [
                    'relation' => 'AND',
                    'rules'    => [ [ 'field' => 'enable_customizations', 'operator' => '=', 'value' => true ] ],
                ],
            ]
        );

        // Conditional Media Field.
        $page->add_field(
            'conditional_media_field',
            $section_id,
            'media',
            'Featured Image',
            [
                'description' => 'This field appears only when the "Image-focused" layout is selected.',
                'conditions'  => [
                    'relation' => 'AND',
                    'rules'    => [
                        [ 'field' => 'enable_customizations', 'operator' => '=', 'value' => true ],
                        [ 'field' => 'layout_mode', 'operator' => '=', 'value' => 'image' ],
                    ],
                ],
            ]
        );

        // Conditional Textarea Field.
        $page->add_field(
            'conditional_textarea_field',
            $section_id,
            'textarea',
            'Custom Text Content',
            [
                'description' => 'This field appears for either "Image-focused" OR "Text-only" layouts, but not "Standard".',
                'conditions'  => [
                    'relation' => 'OR', // Note the 'OR' relation.
                    'rules'    => [
                        [ 'field' => 'layout_mode', 'operator' => '=', 'value' => 'image' ],
                        [ 'field' => 'layout_mode', 'operator' => '=', 'value' => 'text' ],
                    ],
                ],
            ]
        );
    }
}
