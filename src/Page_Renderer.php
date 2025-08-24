<?php
/**
 * Handles all HTML output for the settings page.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

/**
 * Class responsible for handling all HTML output for the settings page.
 */
final class Page_Renderer {


	/**
	 * The HTML prefix that will be used in fields markup and generated CSS/JS.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private string $html_prefix = 'wptechnix-settings';

	/**
	 * Class Constructor
	 *
	 * @param Settings_Store $settings_store Settings store.
	 */
	public function __construct(
		private Settings_Store $settings_store
	) {}

	/**
	 * Set HTML prefix that will be used in fields markup.
	 *
	 * @param string $html_prefix HTML prefix.
	 *
	 * @phpstan-param non-empty-string $html_prefix
	 */
	public function set_html_prefix( string $html_prefix ): void {
		$this->html_prefix = $html_prefix;
	}

	/**
	 * Renders the entire settings page.
	 *
	 * This is the main callback function for `add_submenu_page`.
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php
			settings_errors();
			?>

			<?php
			if ( $this->settings_store->has_tabs() ) :
				$this->render_tabs();
			endif;
			?>

			<!--suppress HtmlUnknownTarget -->
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->settings_store->get_option_group_name() );

				if ( $this->settings_store->has_tabs() ) {
					$active_tab = $this->settings_store->get_active_tab();
					printf( '<input type="hidden" name="tab" value="%s" />', esc_attr( (string) $active_tab ) );
					$this->render_sections();
				} else {
					do_settings_sections( $this->settings_store->get_page_slug() );
				}

				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * The WordPress callback for rendering a settings field.
	 *
	 * @param array $args Arguments passed from `add_settings_field`.
	 * @phpstan-param array<non-empty-string, mixed> $args
	 */
	public function render_field( array $args ): void {
		// TODO: Implement.
	}


	/**
	 * Renders the navigation tabs for the settings page.
	 */
	private function render_tabs(): void {
		$classes = implode(
			' ',
			[
				'nav-tab-wrapper',
				$this->html_prefix . '-nav-tab-wrapper',
			]
		);

		$active_tab = $this->settings_store->get_active_tab();

		echo '<nav class="' . esc_attr( $classes ) . '">';
		foreach ( $this->settings_store->get_tabs() as $tab_id => $tab ) {
			$url   = add_query_arg(
				[
					'page' => $this->settings_store->get_page_slug(),
					'tab'  => $tab_id,
				]
			);
			$class = 'nav-tab' . ( $tab_id === $active_tab ? ' nav-tab-active' : '' );
			$icon  = ! empty( $tab['icon'] ) ? '<span class="dashicons ' . esc_attr( $tab['icon'] ) . '"></span>' : '';
			printf(
				'<a href="%s" class="%s">%s%s</a>',
				esc_url( $url ),
				esc_attr( $class ),
				$icon, // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $tab['title'] )
			);
		}
		echo '</nav>';
	}


	/**
	 * Renders all settings sections associated with the active tab.
	 */
	private function render_sections(): void {
		global $wp_settings_sections, $wp_settings_fields;

		$page_slug  = $this->settings_store->get_page_slug();
		$active_tab = $this->settings_store->get_active_tab();
		$sections   = $this->settings_store->get_sections();

		if ( empty( $wp_settings_sections[ $page_slug ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page_slug ] as $section ) {
			// Ensure the section ID exists in our store before proceeding.
			if ( ! isset( $sections[ $section['id'] ] ) ) {
				continue;
			}

			// Check if the section belongs to the currently active tab.
			if ( $sections[ $section['id'] ]['tab_id'] !== $active_tab ) {
				continue;
			}

			if ( $section['title'] ) {
				echo '<h2>' . esc_html( $section['title'] ) . "</h2>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! empty( $wp_settings_fields[ $page_slug ][ $section['id'] ] ) ) {
				echo '<table class="form-table" role="presentation">';
				do_settings_fields( $page_slug, $section['id'] );
				echo '</table>';
			}
		}
	}
}
