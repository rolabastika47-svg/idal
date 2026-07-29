<?php
/**
 * Statistics Card rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_stat_card_default_settings() {
	return array(
		'number'           => '120',
		'unit'             => '+',
		'title'            => __( 'Digital Services Launched', 'omsar' ),
		'background_color' => '#ffffff',
		'icon'             => '',
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_stat_card_default_content_properties() {
	$defaults = omsar_bd_stat_card_default_settings();

	return array(
		'content' => array(
			'number'           => $defaults['number'],
			'unit'             => $defaults['unit'],
			'title'            => $defaults['title'],
			'background_color' => $defaults['background_color'],
			'icon'             => $defaults['icon'],
		),
	);
}

/**
 * @param mixed $image
 * @return string
 */
function omsar_bd_stat_card_image_url( $image ) {
	if ( is_string( $image ) ) {
		return $image;
	}

	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return (string) $image['url'];
	}

	return '';
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_stat_card_settings( $settings ) {
	$defaults = omsar_bd_stat_card_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['number']           = isset( $settings['number'] ) ? (string) $settings['number'] : '';
	$settings['unit']             = isset( $settings['unit'] ) ? (string) $settings['unit'] : '';
	$settings['title']            = isset( $settings['title'] ) ? (string) $settings['title'] : '';
	$settings['background_color'] = ! empty( $settings['background_color'] ) ? (string) $settings['background_color'] : '#ffffff';
	$settings['icon']             = omsar_bd_stat_card_image_url( $settings['icon'] ?? '' );

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_stat_card_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content']['content'] ?? array();

	return omsar_bd_normalize_stat_card_settings(
		array(
			'number'           => $content['number'] ?? '',
			'unit'             => $content['unit'] ?? '',
			'title'            => $content['title'] ?? '',
			'background_color' => $content['background_color'] ?? '#ffffff',
			'icon'             => $content['icon'] ?? '',
		)
	);
}

/**
 * Read a Breakdance number control stored as a scalar or { number: n } object.
 *
 * @param mixed $value
 * @param float $default
 * @return float
 */
function omsar_bd_stat_card_control_number( $value, $default = 0 ) {
	if ( is_numeric( $value ) ) {
		return floatval( $value );
	}

	if ( is_array( $value ) && isset( $value['number'] ) && is_numeric( $value['number'] ) ) {
		return floatval( $value['number'] );
	}

	return floatval( $default );
}

/**
 * Read a Breakdance unit control stored as a style string or unit object.
 *
 * @param mixed  $value
 * @param string $default
 * @return string
 */
function omsar_bd_stat_card_control_unit( $value, $default = '0px' ) {
	if ( is_array( $value ) && ! empty( $value['style'] ) ) {
		return (string) $value['style'];
	}

	if ( is_array( $value ) && isset( $value['number'] ) && isset( $value['unit'] ) ) {
		return $value['number'] . $value['unit'];
	}

	if ( is_numeric( $value ) ) {
		return $value . 'px';
	}

	return $default;
}

/**
 * @param array<string, mixed> $design
 * @return bool
 */
function omsar_bd_stat_card_hover_enabled( $design ) {
	$hover = $design['hover'] ?? array();

	if ( ! isset( $hover['enable_hover_effect'] ) ) {
		return true;
	}

	return ! empty( $hover['enable_hover_effect'] );
}

/**
 * @param array<string, mixed> $settings
 * @param array<string, mixed> $design
 * @param string               $element_id
 */
function omsar_bd_render_stat_card_widget( $settings, $design, $element_id ) {
	$settings      = omsar_bd_normalize_stat_card_settings( $settings );
	$number        = $settings['number'];
	$unit          = $settings['unit'];
	$title         = $settings['title'];
	$icon_url      = $settings['icon'];
	$enable_hover  = omsar_bd_stat_card_hover_enabled( $design );
	$hover         = $design['hover'] ?? array();
	$gradient_from      = ! empty( $hover['gradient_from'] ) ? $hover['gradient_from'] : '#E0F2FF';
	$gradient_to        = ! empty( $hover['gradient_to'] ) ? $hover['gradient_to'] : '#6EA8FE';
	$icon_design        = $design['icon'] ?? array();
	$icon_opacity       = omsar_bd_stat_card_control_number( $icon_design['opacity'] ?? null, 0.15 );
	$icon_size          = omsar_bd_stat_card_control_unit( $icon_design['size'] ?? null, '150px' );
	$hover_icon_opacity = omsar_bd_stat_card_control_number( $hover['hover_icon_opacity'] ?? null, 0.8 );
	$widget_id          = 'omsar-stat-card-' . sanitize_html_class( $element_id );
	?>
	<style>
		#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card::before {
			opacity: <?php echo esc_attr( $icon_opacity ); ?>;
			width: <?php echo esc_attr( $icon_size ); ?>;
			height: <?php echo esc_attr( $icon_size ); ?>;
			background-size: <?php echo esc_attr( $icon_size ); ?> <?php echo esc_attr( $icon_size ); ?>;
			<?php if ( $icon_url ) : ?>
			background-image: url(<?php echo esc_url( $icon_url ); ?>);
			<?php endif; ?>
		}

		<?php if ( $enable_hover ) : ?>
		#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card:hover::before {
			opacity: <?php echo esc_attr( $hover_icon_opacity ); ?>;
		}
		#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card:hover {
			background: linear-gradient(180deg, <?php echo esc_attr( $gradient_from ); ?> 0%, <?php echo esc_attr( $gradient_to ); ?> 100%);
		}
		<?php else : ?>
		#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card:hover {
			transform: none !important;
			box-shadow: inherit !important;
			background: <?php echo esc_attr( $settings['background_color'] ); ?> !important;
		}
		#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card::after {
			display: none;
		}
		<?php endif; ?>
	</style>
	<div id="<?php echo esc_attr( $widget_id ); ?>" class="dt-stat-card elementor-stat-card">
		<div class="card-content">
			<?php if ( $number ) : ?>
				<h3 class="card-number">
					<?php echo esc_html( $number ); ?><?php echo esc_html( $unit ); ?>
				</h3>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<p class="card-text"><?php echo esc_html( $title ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
