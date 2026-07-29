<?php
/**
 * Custom Timeline rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_timeline_default_settings() {
	return array(
		'timeline_items'      => array(
			array(
				'item_title'          => __( 'Project Kickoff', 'omsar' ),
				'item_date'           => __( 'January 2024', 'omsar' ),
				'item_description'    => __( 'Initial planning and requirement gathering phase completed successfully.', 'omsar' ),
				'item_marker_filled'  => false,
			),
			array(
				'item_title'          => __( 'Development Phase', 'omsar' ),
				'item_date'           => __( 'March 2024', 'omsar' ),
				'item_description'    => __( 'Core features implemented and tested across multiple platforms.', 'omsar' ),
				'item_marker_filled'  => false,
			),
			array(
				'item_title'          => __( 'Beta Launch', 'omsar' ),
				'item_date'           => __( 'June 2024', 'omsar' ),
				'item_description'    => __( 'Limited release to selected users for feedback and improvements.', 'omsar' ),
				'item_marker_filled'  => true,
			),
			array(
				'item_title'          => __( 'Public Release', 'omsar' ),
				'item_date'           => __( 'Q4 2024', 'omsar' ),
				'item_description'    => __( 'Full public launch with all features and enhancements.', 'omsar' ),
				'item_marker_filled'  => false,
			),
		),
		'timeline_layout'     => 'alternating',
		'same_side_alignment' => 'right',
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_timeline_default_content_properties() {
	$defaults = omsar_bd_timeline_default_settings();

	return array(
		'items'  => array(
			'timeline_items' => $defaults['timeline_items'],
		),
		'layout' => array(
			'timeline_layout'     => $defaults['timeline_layout'],
			'same_side_alignment' => $defaults['same_side_alignment'],
		),
	);
}

/**
 * @param mixed $value
 * @return bool
 */
function omsar_bd_timeline_is_marker_filled( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}

	return in_array( $value, array( 'yes', '1', 1, 'true' ), true );
}

/**
 * @param array<string, mixed> $item
 * @return array<string, mixed>
 */
function omsar_bd_normalize_timeline_item( $item ) {
	if ( ! is_array( $item ) ) {
		return array();
	}

	return array(
		'item_title'         => isset( $item['item_title'] ) ? (string) $item['item_title'] : '',
		'item_date'          => isset( $item['item_date'] ) ? (string) $item['item_date'] : '',
		'item_description'   => isset( $item['item_description'] ) ? (string) $item['item_description'] : '',
		'item_marker_filled' => omsar_bd_timeline_is_marker_filled( $item['item_marker_filled'] ?? false ),
	);
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_timeline_settings( $settings ) {
	$defaults = omsar_bd_timeline_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$items = array();
	if ( ! empty( $settings['timeline_items'] ) && is_array( $settings['timeline_items'] ) ) {
		foreach ( $settings['timeline_items'] as $item ) {
			$normalized = omsar_bd_normalize_timeline_item( $item );
			if ( ! empty( $normalized ) ) {
				$items[] = $normalized;
			}
		}
	}

	$settings['timeline_items'] = $items;

	$valid_layouts = array( 'alternating', 'alternating_right_left', 'same_side' );
	$settings['timeline_layout'] = in_array( $settings['timeline_layout'], $valid_layouts, true )
		? $settings['timeline_layout']
		: 'alternating';

	$settings['same_side_alignment'] = in_array( $settings['same_side_alignment'], array( 'left', 'right' ), true )
		? $settings['same_side_alignment']
		: 'right';

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_timeline_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content'] ?? array();
	$items   = $content['items'] ?? array();
	$layout  = $content['layout'] ?? array();

	$settings = array(
		'timeline_items'      => $items['timeline_items'] ?? array(),
		'timeline_layout'     => $layout['timeline_layout'] ?? 'alternating',
		'same_side_alignment' => $layout['same_side_alignment'] ?? 'right',
	);

	return omsar_bd_normalize_timeline_settings( $settings );
}

/**
 * @param array<string, mixed> $settings
 * @param string               $element_id
 */
function omsar_bd_render_timeline_widget( $settings, $element_id ) {
	$settings = omsar_bd_normalize_timeline_settings( $settings );
	$items    = $settings['timeline_items'];

	if ( empty( $items ) ) {
		return;
	}

	$layout              = $settings['timeline_layout'];
	$same_side_alignment = $settings['same_side_alignment'];

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : ( is_rtl() ? 'ar' : 'en' );
	$is_arabic    = ( 'ar' === $current_lang || is_rtl() );

	if ( 'same_side' === $layout && $is_arabic ) {
		$same_side_alignment = 'right';
	}

	$widget_id    = 'omsar-timeline-' . sanitize_html_class( $element_id );
	$layout_class = 'omsar-timeline-' . $layout;

	if ( 'same_side' === $layout ) {
		$layout_class .= ' omsar-timeline-' . $same_side_alignment;
	}

	if ( $is_arabic ) {
		$layout_class .= ' omsar-timeline-rtl';
	}

	$item_count = count( $items );
	?>
	<div class="omsar-timeline-wrapper <?php echo esc_attr( $layout_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>">
		<div class="omsar-timeline-container">
			<div class="omsar-timeline-line"></div>
			<div class="omsar-timeline-items">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$item_title       = $item['item_title'];
					$item_date        = $item['item_date'];
					$item_description = $item['item_description'];
					$marker_class     = ! empty( $item['item_marker_filled'] ) ? 'filled' : '';
					$is_last          = ( $index === $item_count - 1 );
					?>
					<div class="omsar-timeline-item <?php echo $is_last ? 'omsar-timeline-item-last' : ''; ?>">
						<div class="omsar-timeline-marker <?php echo esc_attr( $marker_class ); ?>"></div>
						<div class="omsar-timeline-item-content">
							<?php if ( $item_title ) : ?>
								<div class="omsar-timeline-item-title"><?php echo esc_html( $item_title ); ?></div>
							<?php endif; ?>
							<?php if ( $item_date ) : ?>
								<div class="omsar-timeline-item-date"><?php echo esc_html( $item_date ); ?></div>
							<?php endif; ?>
							<?php if ( $item_description ) : ?>
								<div class="omsar-timeline-item-description"><?php echo wp_kses_post( $item_description ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<script>
	(function() {
		var timelineWrapper = document.getElementById('<?php echo esc_js( $widget_id ); ?>');
		if (timelineWrapper) {
			function updateTimelineLine() {
				var lastItem = timelineWrapper.querySelector('.omsar-timeline-item-last');
				var line = timelineWrapper.querySelector('.omsar-timeline-line');
				if (lastItem && line) {
					var lastMarker = lastItem.querySelector('.omsar-timeline-marker');
					if (lastMarker) {
						var container = timelineWrapper.querySelector('.omsar-timeline-container');
						var markerRect = lastMarker.getBoundingClientRect();
						var containerRect = container.getBoundingClientRect();
						var lineRect = line.getBoundingClientRect();
						var lineTop = lineRect.top - containerRect.top;
						var markerTop = markerRect.top - containerRect.top;
						var markerCenter = markerTop + (markerRect.height / 2);
						var lineHeight = markerCenter - lineTop;
						if (lineHeight > 0) {
							line.style.height = lineHeight + 'px';
						}
					}
				}
			}

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', updateTimelineLine);
			} else {
				updateTimelineLine();
			}

			var resizeTimer;
			window.addEventListener('resize', function() {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(updateTimelineLine, 100);
			});

			setTimeout(updateTimelineLine, 100);
			setTimeout(updateTimelineLine, 500);
		}
	})();
	</script>
	<?php
}
