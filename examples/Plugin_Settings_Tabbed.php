<?php
/**
 * Creates a complex, tabbed settings page.
 *
 * @package My_Plugin\Admin
 */

declare(strict_types=1);

namespace My_Plugin\Admin;

use WPTechnix\WP_Settings_Builder\Settings_Builder;

/**
 * Class Plugin_Settings_Tabbed
 *
 * A concrete example of a tabbed settings page using all advanced field types.
 */
final class Plugin_Settings_Tabbed {

		/**
	 * The unique option name where all settings are stored.
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'wptx_settings_with_tabs';

	/**
	 * The unique page slug for the settings page.
	 *
	 * @var string
	 */
	private const PAGE_SLUG = 'wptx-settings-with-tabs';

	/**
	 * Initializes the settings page by building its structure and hooking into WordPress.
	 */
	public function init(): void {

		// 1. Create a new settings page instance.
		$page = ( new Settings_Builder() )->create( self::OPTION_NAME, self::PAGE_SLUG );

		// 2. Configure the page properties.
		$page->set_page_title( 'All Fields With Tabs' )
			->set_menu_title( 'Tabs Settings' )
			->set_parent_slug( 'options-general.php' )
			->set_capability( 'manage_options' );

		// 3. Define the page structure (tabs and sections).
		$this->add_structure( $page );

		// 4. Add fields to their respective sections.
		$this->add_basic_fields( $page );
		$this->add_choice_fields_with_conditions( $page );
		$this->add_content_fields( $page );
		$this->add_date_time_fields( $page );
		$this->add_relationship_fields( $page );


		// 5. Build and register the page with WordPress.
		$page->init();
	}

	/**
	 * Defines the tabs and sections for the settings page.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page The settings page instance.
	 */
	private function add_structure( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Tabs
		$page->add_tab( 'basic_tab', 'Basic Inputs', 'dashicons-edit' );
		$page->add_tab( 'choice_tab', 'Choice Fields', 'dashicons-forms' );
		$page->add_tab( 'content_tab', 'Content & Media', 'dashicons-admin-media' );
		$page->add_tab( 'datetime_tab', 'Date & Time', 'dashicons-calendar-alt' );
		$page->add_tab( 'relationship_tab', 'Relationships', 'dashicons-admin-links' );

		// Sections for 'Basic Inputs' Tab
		$page->add_section( 'text_section', 'Text & Number Inputs', 'Fields for various text-based and numeric data.', 'basic_tab' );
		$page->add_section( 'toggle_section', 'Toggles & Pickers', 'Boolean switches and color selection.', 'basic_tab' );

		// Sections for 'Choice Fields' Tab
		$page->add_section( 'single_choice_section', 'Single Choice Fields', 'Fields for selecting one option from a list.', 'choice_tab' );
		$page->add_section( 'multiple_choice_section', 'Multiple Choice Fields', 'Fields for selecting multiple options from a list.', 'choice_tab' );
		$page->add_section( 'conditional_choice_section', 'Conditional Fields', 'These fields appear based on the settings above.', 'choice_tab' );

		// Sections for 'Content & Media' Tab
		$page->add_section( 'editor_section', 'Editors', 'Fields for rich text and code.', 'content_tab' );
		$page->add_section( 'media_section', 'Media Library', 'Fields for selecting items from the WordPress Media Library.', 'content_tab' );

		// Sections for 'Date & Time' Tab
		$page->add_section( 'single_date_section', 'Single Date/Time Pickers', null, 'datetime_tab' );
		$page->add_section( 'range_date_section', 'Date/Time Range Pickers', null, 'datetime_tab' );

		// Sections for 'Relationships' Tab
		$page->add_section( 'post_relationship_section', 'Post Relationships', 'Link to posts, pages, or custom post types.', 'relationship_tab' );
		$page->add_section( 'user_term_relationship_section', 'User & Term Relationships', 'Link to users and taxonomy terms.', 'relationship_tab' );
	}

	/**
	 * Adds basic text, number, and toggle fields.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page The settings page instance.
	 */
	private function add_basic_fields( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Text & Number Section
		$page->add_field( 'text_field', 'text_section', 'text', 'Site Name', [ 'description' => 'A standard input for single-line text.' ] );
		$page->add_field( 'url_field', 'text_section', 'url', 'Site URL', [ 'description' => 'Input for URLs, sanitized with <code>esc_url_raw()</code>.' ] );
		$page->add_field( 'email_field', 'text_section', 'email', 'Admin Email', [ 'description' => 'Input for a valid email address.' ] );
		$page->add_field( 'number_field', 'text_section', 'number', 'Items per Page', [ 'description' => 'A field for numeric values.', 'default' => 12, 'html_attributes' => [ 'min' => 1, 'max' => 24 ] ] );
		$page->add_field( 'password_field', 'text_section', 'password', 'API Key', [ 'description' => 'A secure field for sensitive data. Value is not shown in HTML.' ] );

		// Toggles & Pickers Section
		$page->add_field( 'checkbox_field', 'toggle_section', 'checkbox', 'Enable Feature A', [ 'description' => 'A standard checkbox.', 'default' => true ] );
		$page->add_field( 'switch_field', 'toggle_section', 'switch', 'Enable Feature B', [ 'description' => 'A modern toggle switch.', 'default' => false ] );
		$page->add_field( 'color_field', 'toggle_section', 'color', 'Primary Color', [ 'description' => 'Uses the native WordPress color picker.', 'default' => '#0073aa' ] );
	}

	/**
	 * Adds single and multiple choice fields and demonstrates conditional logic.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page
	 */
	private function add_choice_fields_with_conditions( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Single Choice Section (Triggers)
		$page->add_field(
			'delivery_method',
			'single_choice_section',
			'choice',
			'Delivery Method',
			[
				'description' => 'Select a delivery method to see relevant options below.',
				'default' => 'standard',
				'options' => [
					'standard' => 'Standard Shipping',
					'express' => 'Express Shipping',
					'pickup' => 'Local Pickup',
				],
			]
		);

		$page->add_field(
			'notification_channels',
			'single_choice_section',
			'multi_select',
			'Notification Channels',
			[
				'description' => 'Select how you want to be notified.',
				'default' => ['email'],
				'options' => [
					'email' => 'Email',
					'sms' => 'SMS',
					'push' => 'Push Notification',
				],
			]
		);

		// Conditional Choice Section (Targets)
		$page->add_field(
			'pickup_location',
			'conditional_choice_section',
			'textarea',
			'Pickup Location Address',
			[
				'description' => 'This field only appears if "Local Pickup" is selected.',
				'conditions' => [
					'relation' => 'AND',
					'rules'    => [ [ 'field' => 'delivery_method', 'operator' => '=', 'value' => 'pickup' ] ],
				],
			]
		);

		$page->add_field(
			'shipping_notes',
			'conditional_choice_section',
			'text',
			'Shipping Notes',
			[
				'description' => 'Appears for Standard or Express shipping (using the "IN" operator).',
				'conditions' => [
					'relation' => 'AND',
					'rules'    => [
						[
							'field'    => 'delivery_method',
							'operator' => 'IN',
							'value'    => [ 'standard', 'express' ],
						],
					],
				],
			]
		);

		$page->add_field(
			'sms_number',
			'conditional_choice_section',
			'text',
			'SMS Phone Number',
			[
				'description' => 'Enter your phone number if SMS notifications are enabled (using the "IN" operator on a multi-select field).',
				'conditions' => [
					'relation' => 'AND',
					'rules'    => [
						[
							'field'    => 'notification_channels',
							'operator' => 'IN',
							'value'    => ['sms'],
						],
					],
				],
			]
		);
	}

	/**
	 * Adds content and media-related fields.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page The settings page instance.
	 */
	private function add_content_fields( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Editors Section
		$page->add_field( 'textarea_field', 'editor_section', 'textarea', 'Footer Copyright Text', [ 'description' => 'A field for multi-line text.', 'html_attributes' => [ 'rows' => 4 ] ] );
		$page->add_field( 'wysiwyg_field', 'editor_section', 'wysiwyg', 'Welcome Message', [ 'description' => 'The classic WordPress rich text editor.', 'editor_settings' => [ 'textarea_rows' => 8, 'media_buttons' => true ] ] );
		$page->add_field( 'code_editor_css', 'editor_section', 'code_editor', 'Custom CSS', [ 'description' => 'A syntax-highlighting editor for CSS.', 'mode' => 'text/css' ] );

		// Media Section
		$page->add_field( 'media_field', 'media_section', 'media', 'Site Logo', [ 'description' => 'Select a single image from the Media Library.', 'media_settings' => [ 'library_type' => 'image' ] ] );
		$page->add_field( 'media_multiple_field', 'media_section', 'media_multiple', 'Image Gallery', [ 'description' => 'Select multiple items from the Media Library.' ] );
	}

	/**
	 * Adds date and time picker fields.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page The settings page instance.
	 */
	private function add_date_time_fields( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Single Date/Time Section
		$page->add_field( 'date_field', 'single_date_section', 'date', 'Event Date', [ 'description' => 'Stores date as <code>Y-m-d</code>. <code>display_format</code> changes the view.', 'display_format' => 'F j, Y' ] );
		$page->add_field( 'time_field', 'single_date_section', 'time', 'Opening Time', [ 'description' => 'Stores time as <code>H:i:s</code>.', 'display_format' => 'h:i A' ] );
		$page->add_field( 'datetime_field', 'single_date_section', 'date_time', 'Campaign Start', [ 'description' => 'Stores date and time as <code>Y-m-d H:i:s</code>.', 'display_format' => 'M j, Y @ h:i K' ] );

		// Range Date/Time Section
		$page->add_field( 'date_range_field', 'range_date_section', 'date_range', 'Promotion Duration', [ 'description' => 'Stores a start and end date as a JSON array.', 'display_format' => 'M d' ] );
		$page->add_field( 'datetime_range_field', 'range_date_section', 'date_time_range', 'Maintenance Window', [ 'description' => 'Stores a start and end date/time as a JSON array.', 'display_format' => 'M d, h:i K' ] );
	}

	/**
	 * Adds fields for selecting posts, users, and terms.
	 *
	 * @param \WPTechnix\WP_Settings_Builder\Settings_Page $page The settings page instance.
	 */
	private function add_relationship_fields( \WPTechnix\WP_Settings_Builder\Settings_Page $page ): void {
		// Post Relationships Section
		$page->add_field( 'description_field', 'post_relationship_section', 'description', '', [ 'description' => 'These fields use AJAX to search for WordPress objects. Use <code>query_args</code> to filter the results.' ] );
		$page->add_field( 'post_field', 'post_relationship_section', 'post', 'Featured Page', [ 'description' => 'Select a single Page.', 'query_args' => [ 'post_type' => 'page' ] ] );
		$page->add_field( 'posts_field', 'post_relationship_section', 'posts', 'Related Articles', [ 'description' => 'Select multiple Posts.', 'query_args' => [ 'post_type' => 'post' ] ] );

		// User & Term Relationships Section
		$page->add_field( 'user_field', 'user_term_relationship_section', 'user', 'Site Moderator', [ 'description' => 'Select a single user.', 'query_args' => [ 'role' => 'editor' ] ] );
		$page->add_field( 'users_field', 'user_term_relationship_section', 'users', 'Team Members', [ 'description' => 'Select multiple users.' ] );
		$page->add_field( 'term_field', 'user_term_relationship_section', 'term', 'Default Category', [ 'description' => 'Select a single category.', 'query_args' => [ 'taxonomy' => 'category' ] ] );
		$page->add_field( 'terms_field', 'user_term_relationship_section', 'terms', 'Relevant Tags', [ 'description' => 'Select multiple post tags.', 'query_args' => [ 'taxonomy' => 'post_tag' ] ] );
	}
}