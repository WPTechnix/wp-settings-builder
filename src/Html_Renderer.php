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
	public function render_page( Page_Definition_Interface $definition ): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $definition->get_page_title() ); ?></h1>

			<?php

			settings_errors();

			$this->render_tabs( $definition );
			?>

			<form method="post" action="options.php" class="wptx-settings-form">
				<?php

				settings_fields( $definition->get_option_group() );

				if ( ! empty( $definition->get_tabs() ) ) {
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
		if ( empty( $tabs ) ) {
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
					<?php if ( ! empty( $tab_config['icon'] ) ) : ?>
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

		if ( ! isset( $wp_settings_sections[ $page_slug ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page_slug ] as $section_id => $section ) {
			$section_config = $definition->get_sections()[ $section_id ] ?? null;

			if ( empty( $section_config ) || empty( $section_config['title'] ) || $active_tab !== $section_config['tab'] ) {
				continue;
			}

			if ( ! empty( $section['title'] ) ) {
				echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
			}

			if ( ! empty( $section['callback'] ) && is_callable( $section['callback'] ) ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! empty( $wp_settings_fields[ $page_slug ][ $section_id ] ) ) {
				echo '<table class="form-table" role="presentation">';
				do_settings_fields( $page_slug, $section_id );
				echo '</table>';
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function render_field( array $args ): void {
		/** @var Field_Config|null $field_config */
		$field_config = $args['config'] ?? null;
		if ( empty( $field_config ) ) {
			return;
		}

		$conditions   = $field_config['extras']['conditions'] ?? null;
		$wrapper_open = ! empty( $conditions ) && is_array( $conditions );

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
				if ( ! empty( $description ) ) {
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
	 * @param array $tabs The map of configured tabs.
	 *
	 * @phpstan-param Tabs_Map $tabs
	 *
	 * @return string|null The active tab ID, or null if no tabs exist.
	 *
	 * @phpstan-return non-empty-string|null
	 */
	private function get_active_tab( array $tabs ): ?string {
		if ( empty( $tabs ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.NonceVerification
		$tab_from_request = $_GET['tab'] ?? null;

		$active_tab = is_string( $tab_from_request ) ? sanitize_key( $tab_from_request ) : '';
		if ( ! empty( $active_tab ) && array_key_exists( $active_tab, $tabs ) ) {
			return $active_tab;
		}
		$tab = array_key_first( $tabs );

		return empty( $tab ) ? null : (string) $tab;
	}
}
