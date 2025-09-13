<?php
/**
 * Concrete implementation of Renderer_Interface for generating HTML.
 *
 * @package WPTechnix\WP_Settings_Builder
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder;

use WPTechnix\WP_Settings_Builder\Interfaces\Renderer_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Page_Definition_Interface;
use WPTechnix\WP_Settings_Builder\Interfaces\Field_Factory_Interface;
use InvalidArgumentException;

/**
 * Class Html_Renderer
 *
 * @phpstan-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @phpstan-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Field_Config from \WPTechnix\WP_Settings_Builder\Internal\Types
 * @psalm-import-type Tabs_Map from \WPTechnix\WP_Settings_Builder\Internal\Types
 */
final class Html_Renderer implements Renderer_Interface {

	/**
	 * Class Constructor.
	 *
	 * @param Field_Factory_Interface $field_factory The field factory.
	 */
	public function __construct(
		private Field_Factory_Interface $field_factory
	) {
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render_page( Page_Definition_Interface $definition ): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $definition->get_page_title() ); ?></h1>

			<?php
			$this->render_tabs( $definition );
			?>

			<form method="post" action="options.php" class="wptx-settings-form">
				<?php
				settings_fields( $definition->get_option_group() );

				if ( 0 < count( $definition->get_tabs() ) ) {
					$this->render_tabbed_sections( $definition );
				} else {
					do_settings_sections( $definition->get_page_slug() );
				}

				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Renders the navigation tab bar if tabs are configured.
	 *
	 * @param Page_Definition_Interface $definition The page definition.
	 */
	private function render_tabs( Page_Definition_Interface $definition ): void {
		$tabs = $definition->get_tabs();
		if ( 0 === count( $tabs ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification
		$active_tab = $this->get_active_tab( $tabs );

		?>
		<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary navigation">
			<?php
			foreach ( $tabs as $tab_id => $tab_config ) {
				$url   = add_query_arg(
					[
						'page' => $definition->get_page_slug(),
						'tab'  => $tab_id,
					],
					admin_url( 'admin.php' )
				);
				$class = 'nav-tab' . ( $tab_id === $active_tab ? ' nav-tab-active' : '' );
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $class ); ?>">
					<?php if ( is_string( $tab_config['icon'] ) ) : ?>
						<span class="dashicons <?php echo esc_attr( $tab_config['icon'] ); ?>"></span>
					<?php endif; ?>
					<?php echo esc_html( $tab_config['title'] ); ?>
				</a>
				<?php
			}
			?>
		</nav>
		<?php
	}

	/**
	 * Renders the settings sections that belong to the current active tab.
	 *
	 * @param Page_Definition_Interface $definition The page definition.
	 */
	private function render_tabbed_sections( Page_Definition_Interface $definition ): void {
		global $wp_settings_sections, $wp_settings_fields;

		$page_slug = $definition->get_page_slug();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = $this->get_active_tab( $definition->get_tabs() );

		printf( '<input type="hidden" name="tab" value="%s">', esc_attr( (string) $active_tab ) );

		if ( ! is_array( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page_slug ] ) || ! is_array( $wp_settings_sections[ $page_slug ] ) ) {
			return;
		}

		$sections_config = $definition->get_sections();

		foreach ( $wp_settings_sections[ $page_slug ] as $section_id => $section ) {

			/** @var string $section_id */

			$section_config = $sections_config[ $section_id ] ?? null;

			if (
				! is_array( $section_config ) ||
				null === $section_config['tab'] ||
				$active_tab !== $section_config['tab']
			) {
				continue;
			}

			echo '<h2>' . esc_html( $section_config['title'] ) . '</h2>';

			if ( is_array( $section ) && isset( $section['callback'] ) && is_callable( $section['callback'] ) ) {
				call_user_func( $section['callback'], $section );
			}

			if ( isset( $wp_settings_fields[ $page_slug ][ $section_id ] ) ) {
				echo '<table class="form-table" role="presentation">';
				do_settings_fields( $page_slug, $section_id );
				echo '</table>';
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function render_field( array $args ): void {

		/** @var Field_Config|null $field_config */
		$field_config = $args['config'] ?? null;
		if ( ! is_array( $field_config ) ) {
			return;
		}

		$conditions   = isset( $field_config['extras']['conditions'] ) && is_array( $field_config['extras']['conditions'] ) ? $field_config['extras']['conditions'] : [];
		$wrapper_open = 0 < count( $conditions );

		if ( $wrapper_open ) {
			printf(
				'<div class="wptx-conditional-wrapper" data-conditions="%s">',
				esc_attr( (string) wp_json_encode( $conditions ) )
			);
		}

		try {
			$field_object = $this->field_factory->create( $field_config );

			$field_object->render();
			if ( $field_object->should_render_description_below() ) {
				$description = $field_object->get_description();
				if ( '' !== $description ) {
					echo '<p class="description">' . wp_kses_post( $description ) . '</p>';
				}
			}
		} catch ( InvalidArgumentException $e ) {
			printf( '<p class="error-message"><strong>Error:</strong> %s</p>', esc_html( $e->getMessage() ) );
		}

		if ( $wrapper_open ) {
			echo '</div>';
		}
	}

	/**
	 * Determines the current active tab.
	 *
	 * @param array[] $tabs The map of configured tabs.
	 * @phpstan-param Tabs_Map $tabs
	 * @psalm-param Tabs_Map $tabs
	 *
	 * @return non-empty-string|null The active tab ID, or null if no tabs exist.
	 */
	private function get_active_tab( array $tabs ): ?string {
		if ( 0 === count( $tabs ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.NonceVerification
		$active_tab = isset( $_REQUEST['tab'] ) && is_string( $_REQUEST['tab'] ) ? sanitize_key( $_REQUEST['tab'] ) : '';
		if ( '' !== $active_tab && array_key_exists( $active_tab, $tabs ) ) {
			return $active_tab;
		}
		return array_key_first( $tabs );
	}
}
