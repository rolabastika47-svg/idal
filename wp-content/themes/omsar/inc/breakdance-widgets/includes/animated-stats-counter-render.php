<?php
/**
 * Animated Stats Counter rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_animated_stats_counter_default_settings() {
	return array(
		'counter_items'      => array(
			array(
				'icon_type'     => 'image',
				'icon_image'    => '',
				'icon_class'    => 'bi bi-star',
				'counter_value' => 10000,
				'unit'          => '+',
				'label_text'    => __( 'Happy Customers', 'omsar' ),
			),
		),
		'animation_duration' => 2,
		'columns'            => 'auto',
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_animated_stats_counter_default_content_properties() {
	$defaults = omsar_bd_animated_stats_counter_default_settings();

	return array(
		'items' => array(
			'counter_items'      => $defaults['counter_items'],
			'animation_duration' => $defaults['animation_duration'],
		),
	);
}

/**
 * @param mixed $image
 * @return string
 */
function omsar_bd_animated_stats_counter_image_url( $image ) {
	if ( is_string( $image ) ) {
		return $image;
	}

	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return (string) $image['url'];
	}

	return '';
}

/**
 * @param array<string, mixed> $item
 * @return array<string, mixed>
 */
function omsar_bd_normalize_animated_stats_counter_item( $item ) {
	if ( ! is_array( $item ) ) {
		return array();
	}

	$icon_type = ! empty( $item['icon_type'] ) ? (string) $item['icon_type'] : 'image';

	return array(
		'icon_type'     => in_array( $icon_type, array( 'image', 'icon' ), true ) ? $icon_type : 'image',
		'icon_image'    => omsar_bd_animated_stats_counter_image_url( $item['icon_image'] ?? '' ),
		'icon_class'    => ! empty( $item['icon_class'] ) ? (string) $item['icon_class'] : 'bi bi-star',
		'counter_value' => isset( $item['counter_value'] ) ? floatval( $item['counter_value'] ) : 0,
		'unit'          => isset( $item['unit'] ) ? (string) $item['unit'] : '',
		'label_text'    => isset( $item['label_text'] ) ? (string) $item['label_text'] : '',
	);
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_animated_stats_counter_settings( $settings ) {
	$defaults = omsar_bd_animated_stats_counter_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$items = array();
	if ( ! empty( $settings['counter_items'] ) && is_array( $settings['counter_items'] ) ) {
		foreach ( $settings['counter_items'] as $item ) {
			$normalized = omsar_bd_normalize_animated_stats_counter_item( $item );
			if ( ! empty( $normalized ) ) {
				$items[] = $normalized;
			}
		}
	}

	$settings['counter_items']      = $items;
	$settings['animation_duration'] = max( 0.5, min( 5, floatval( $settings['animation_duration'] ) ) );
	$settings['columns']            = in_array( (string) $settings['columns'], array( 'auto', '1', '2', '3', '4' ), true )
		? (string) $settings['columns']
		: 'auto';

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_animated_stats_counter_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['items'] ?? array();
	$spacing = $properties_data['design']['spacing'] ?? array();

	$settings = array(
		'counter_items'      => $content['counter_items'] ?? array(),
		'animation_duration' => $content['animation_duration'] ?? 2,
		'columns'            => $spacing['columns'] ?? 'auto',
	);

	return omsar_bd_normalize_animated_stats_counter_settings( $settings );
}

/**
 * @param string $columns
 * @return string
 */
function omsar_bd_animated_stats_counter_grid_columns( $columns ) {
	if ( 'auto' !== $columns && is_numeric( $columns ) ) {
		return 'repeat(' . intval( $columns ) . ', 1fr)';
	}

	return 'repeat(auto-fit, minmax(250px, 1fr))';
}

/**
 * @param array<string, mixed> $settings
 * @param string               $element_id
 */
function omsar_bd_render_animated_stats_counter_widget( $settings, $element_id ) {
	$settings           = omsar_bd_normalize_animated_stats_counter_settings( $settings );
	$items              = $settings['counter_items'];
	$animation_duration = $settings['animation_duration'];
	$columns            = $settings['columns'];

	if ( empty( $items ) ) {
		return;
	}

	$widget_id    = 'omsar-stats-counter-' . sanitize_html_class( $element_id );
	$grid_columns = omsar_bd_animated_stats_counter_grid_columns( $columns );
	?>
	<div class="omsar-stats-counter-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
		<div class="omsar-stats-counter-grid" style="grid-template-columns: <?php echo esc_attr( $grid_columns ); ?>;">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$counter_value = $item['counter_value'];
				$unit          = $item['unit'];
				$label_text    = $item['label_text'];
				$icon_type     = $item['icon_type'];
				$item_id       = $widget_id . '-item-' . $index;
				?>
				<div class="omsar-stats-counter-item" data-counter-value="<?php echo esc_attr( $counter_value ); ?>" data-counter-duration="<?php echo esc_attr( $animation_duration ); ?>" id="<?php echo esc_attr( $item_id ); ?>">
					<?php if ( 'image' === $icon_type && $item['icon_image'] ) : ?>
						<div class="omsar-stats-icon">
							<img src="<?php echo esc_url( $item['icon_image'] ); ?>" alt="<?php echo esc_attr( $label_text ); ?>" />
						</div>
					<?php elseif ( 'icon' === $icon_type && $item['icon_class'] ) : ?>
						<div class="omsar-stats-icon">
							<i class="<?php echo esc_attr( $item['icon_class'] ); ?>" aria-hidden="true"></i>
						</div>
					<?php endif; ?>

					<div class="omsar-stats-number" data-unit="<?php echo esc_attr( $unit ); ?>">
						<span class="omsar-counter-value">0</span><?php echo esc_html( $unit ); ?>
					</div>

					<?php if ( $label_text ) : ?>
						<div class="omsar-stats-label"><?php echo esc_html( $label_text ); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
