<?php
/**
 * Abstract base class for fields using the WordPress Media Modal.
 *
 * @package WPTechnix\WP_Settings_Builder\Fields\Common
 */

declare(strict_types=1);

namespace WPTechnix\WP_Settings_Builder\Fields\Common;

/**
 * Abstract_Media_Field
 *
 * Provides the complete rendering logic, asset requirements, and AJAX handling
 * for all media-based fields. It is now fully configurable via the 'extras' array.
 */
abstract class Abstract_Media_Field extends Abstract_Field {

	/**
	 * Whether the field allows multiple selections.
	 *
	 * @var bool
	 */
	protected bool $is_multiple = false;

	/**
	 * Renders the media field wrapper, hidden input, preview area, and selection button.
	 *
	 * All user-facing text and media library options are now configurable via the
	 * 'media_settings' key in the field's 'extras' array.
	 */
	#[\Override]
	public function render(): void {
		$nonce       = wp_create_nonce( 'wptx_get_attachment_previews' );
		$value       = $this->get_value();
		$input_value = is_array( $value ) ? implode( ',', $value ) : (string) $value;

		// Define default settings and merge with user-provided extras.
		$default_settings = [
			'title'        => $this->is_multiple ? __( 'Select or Upload Media' ) : __( 'Select or Upload Media' ),
			'button_text'  => $this->is_multiple ? __( 'Use media' ) : __( 'Use this media' ),
			'library_type' => '', // Default to all media types.
		];
		$user_settings    = $this->get_extra( 'media_settings', [] );
		$user_settings    = is_array( $user_settings ) ? $user_settings : [];
		$settings         = wp_parse_args( $user_settings, $default_settings );

		$wrapper_class = 'wptx-media-field-wrapper';
		if ( $this->is_multiple ) {
			$wrapper_class .= ' is-multiple';
		}

		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		printf(
			'<input type="hidden" class="wptx-media-input" id="%s" name="%s" value="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_name() ),
			esc_attr( $input_value )
		);

		printf(
			'<div class="wptx-media-preview-list" data-nonce="%s"></div>',
			esc_attr( $nonce )
		);

		printf(
			'<button type="button" class="button wptx-select-media-button" data-multiple="%s" data-library-type="%s" data-title="%s" data-button-text="%s">%s</button>',
			$this->is_multiple ? 'true' : 'false',
			esc_attr( $settings['library_type'] ),
			esc_attr( $settings['title'] ),
			esc_attr( $settings['button_text'] ),
			esc_html( $this->is_multiple ? __( 'Add Media' ) : __( 'Select Media' ) )
		);

		echo '</div>';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function get_default_value(): int|array|null {
		$default_value = parent::get_default_value();
		if ( $this->is_multiple ) {
			return is_array( $default_value ) ? $this->keep_valid_ids( $default_value ) : null;
		}
		if ( is_numeric( $default_value ) ) {
			$default_value = (int) $default_value;
			return 0 < $default_value ? $default_value : null;
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function get_value(): int|array|null {
		$value = parent::get_value();
		if ( $this->is_multiple ) {
			return is_array( $value ) ? $this->keep_valid_ids( $value ) : $this->get_default_value();
		}
		if ( is_numeric( $value ) ) {
			$value = (int) $value;
			return 0 < $value ? $value : $this->get_default_value();
		}
		return $this->get_default_value();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return positive-int|list<positive-int>|null
	 */
	#[\Override]
	public function sanitize( mixed $value ): int|array|null {
		$raw_ids = is_array( $value ) ? $value : ( is_scalar( $value ) ? explode( ',', (string) $value ) : null );
		if ( is_array( $raw_ids ) ) {
			$sanitized_ids = $this->keep_valid_ids( $raw_ids );
			if ( $this->is_multiple ) {
				return $sanitized_ids;
			}
			return $sanitized_ids[0];
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public function should_use_inline_title_as_label(): bool {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_css_contents(): string {
		return <<<'CSS'
.wptx-media-field-wrapper {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 10px;
}
.wptx-media-preview-list {
	display: flex;
	flex-wrap: wrap;
	gap: 10px;
	padding-top: 5px;
}
.wptx-media-preview-item {
	position: relative;
	width: 125px;
	height: 125px;
	border: 1px solid #ddd;
	box-shadow: 0 1px 2px rgba(0,0,0,0.07);
	background-color: #f0f0f1;
	background-size: cover;
	background-position: center;
	border-radius: 2px;
}
.wptx-media-preview-item img {
	width: 100%;
	height: 100%;
	object-fit: contain;
	display: block;
}
.wptx-media-preview-item img.is-icon {
	object-fit: contain;
	padding: 10px;
	box-sizing: border-box;
}
.wptx-media-remove {
	position: absolute;
	top: -5px;
	right: -5px;
	width: 20px;
	height: 20px;
	background: #d63638;
	color: #fff;
	border-radius: 50%;
	text-align: center;
	line-height: 18px;
	cursor: pointer;
	font-weight: bold;
	text-decoration: none;
	border: 1px solid #fff;
	box-shadow: 0 0 2px rgba(0,0,0,0.5);
	transition: background-color 0.15s ease-in-out;
}
.wptx-media-remove:hover {
	background-color: #f00;
}
CSS;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_js_contents(): string {
		return <<<'JS'
jQuery(function($) {
	// Ensure the wp.media object exists.
	if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
		return;
	}

	$('body').on('click', '.wptx-select-media-button', function(e) {
		e.preventDefault();

		const $button = $(this);
		const $wrapper = $button.closest('.wptx-media-field-wrapper');
		const $input = $wrapper.find('.wptx-media-input');
		const $previewContainer = $wrapper.find('.wptx-media-preview-list');
		const isMultiple = $button.data('multiple');
		const libraryType = $button.data('library-type') || '';

		// Create a new media frame instance EVERY time the button is clicked.
		const frame = wp.media({
			title: $button.data('title'),
			button: { text: $button.data('button-text') },
			multiple: isMultiple,
			library: { type: libraryType }
		});

		// When an item is selected, run the callback.
		frame.on('select', function() {
			const selection = frame.state().get('selection');
			let currentIds = $input.val() ? $input.val().split(',') : [];

			if (!isMultiple) {
				currentIds = []; // For single selection, always replace the value.
			}

			selection.each(function(attachment) {
				currentIds.push(String(attachment.id));
			});

			// Remove duplicates and any empty values that might have crept in.
			const newIds = [...new Set(currentIds)].filter(id => id && id.length > 0);

			$input.val(newIds.join(',')).trigger('change');
			renderPreviews(newIds, $previewContainer);
		});

		// Finally, open the modal.
		frame.open();
	});

	// Handle removal of a preview item.
	$('body').on('click', '.wptx-media-remove', function(e) {
		e.preventDefault();
		const $removeButton = $(this);
		const $item = $removeButton.closest('.wptx-media-preview-item');
		const $wrapper = $item.closest('.wptx-media-field-wrapper');
		const $input = $wrapper.find('.wptx-media-input');
		const idToRemove = String($item.data('id'));

		const currentIds = $input.val().split(',');
		const newIds = currentIds.filter(id => id !== idToRemove);
		$input.val(newIds.join(',')).trigger('change');
		$item.remove();
	});

	// AJAX function to render previews from a list of IDs.
	function renderPreviews(ids, container) {
		if (!ids || ids.length === 0 || (ids.length === 1 && ids[0] === '')) {
			container.html('');
			return;
		}

		$.post(ajaxurl, {
			action: 'wptx_get_attachment_previews',
			_ajax_nonce: container.data('nonce'),
			attachment_ids: ids
		}, function(response) {
			if (response.success) {
				container.html(response.data);
			}
		});
	}

	// Initial load of previews for all media fields on the page.
	$('.wptx-media-preview-list').each(function() {
		const $container = $(this);
		const $input = $container.closest('.wptx-media-field-wrapper').find('.wptx-media-input');
		const ids = $input.val() ? $input.val().split(',') : [];
		renderPreviews(ids, $container);
	});
});
JS;
	}

	/**
	 * {@inheritDoc}
	 */
	#[\Override]
	public static function get_ajax_actions(): array {
		return [ 'get_attachment_previews' => 'ajax_get_attachment_previews' ];
	}

	/**
	 * AJAX handler to fetch HTML previews for given attachment IDs.
	 */
	public static function ajax_get_attachment_previews(): void {
		check_ajax_referer( 'wptx_get_attachment_previews', '_ajax_nonce' );

		$attachment_ids = $_POST['attachment_ids'] ?? [];
		if ( ! is_array( $attachment_ids ) || 0 === count( $attachment_ids ) ) {
			wp_send_json_error( 'No attachment IDs provided.' );
		}

		$html = '';

		foreach ( $attachment_ids as $id ) {
			if ( ! is_numeric( $id ) ) {
				continue;
			}
			$id              = abs( intval( $id ) );
			$attachment_type = get_post_mime_type( $id );
			$is_image        = is_string( $attachment_type ) && str_starts_with( $attachment_type, 'image' );
			$preview_url     = (string) wp_get_attachment_image_url( $id, 'full', false );
			$icon_url        = (string) wp_get_attachment_image_url( $id, 'full', true );

			$html .= sprintf( '<div class="wptx-media-preview-item" data-id="%d">', esc_attr( (string) $id ) );
			$html .= '<a href="#" class="wptx-media-remove">&times;</a>';

			if ( $is_image && '' !== $preview_url ) {
				$html .= sprintf( '<img src="%s" alt="">', esc_url( $preview_url ) );
			} else {
				$html .= sprintf( '<img src="%s" alt="" class="is-icon">', esc_url( $icon_url ) );
			}
			$html .= '</div>';
		}
		wp_send_json_success( $html );
	}

	/**
	 * Validates a list of media IDs and returns only those that are valid.
	 *
	 * @param array<array-key,mixed> $ids The list of media IDs to validate.
	 * @return list<positive-int> The list of valid media IDs.
	 */
	protected function keep_valid_ids( array $ids ): array {
		return array_values(
			array_unique(
				array_filter(
					array_map( fn( $v ) => is_scalar( $v ) ? (int) $v : 0, $ids ),
					fn( $id ) =>  0 < $id,
				)
			)
		);
	}
}
