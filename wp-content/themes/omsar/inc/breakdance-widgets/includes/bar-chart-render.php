<?php
/**
 * Bar Chart rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default content settings (Elementor-compatible shape).
 *
 * @return array<string, mixed>
 */
function omsar_bd_bar_chart_default_settings() {
	return array(
		'chart_title'              => __( 'Figure 4.1 - Responsibilities of the Coordinating Office', 'omsar' ),
		'show_export_icon'         => 'no',
		'show_zoom_icon'           => 'no',
		'y_axis_label'             => '%',
		'show_y_axis_label_title'  => 'yes',
		'reverse_chart'            => 'no',
		'y_axis_values'            => array(
			array( 'y_axis_value' => '0' ),
			array( 'y_axis_value' => '20' ),
			array( 'y_axis_value' => '40' ),
			array( 'y_axis_value' => '60' ),
			array( 'y_axis_value' => '80' ),
			array( 'y_axis_value' => '100' ),
		),
		'bars_config'              => array(
			array( 'bar_name' => __( 'Bar 1', 'omsar' ), 'bar_color' => '#2563eb' ),
			array( 'bar_name' => __( 'Bar 2', 'omsar' ), 'bar_color' => '#60a5fa' ),
		),
		'chart_data'               => array(
			array( 'category_label' => __( 'Category A', 'omsar' ), 'value_1' => 84, 'value_2' => 64 ),
			array( 'category_label' => __( 'Category B', 'omsar' ), 'value_1' => 72, 'value_2' => 58 ),
			array( 'category_label' => __( 'Category C', 'omsar' ), 'value_1' => 90, 'value_2' => 74 ),
			array( 'category_label' => __( 'Category D', 'omsar' ), 'value_1' => 68, 'value_2' => 52 ),
			array( 'category_label' => __( 'Category E', 'omsar' ), 'value_1' => 78, 'value_2' => 62 ),
			array( 'category_label' => __( 'Category F', 'omsar' ), 'value_1' => 82, 'value_2' => 70 ),
		),
		'bar_border_radius'        => array( 'unit' => 'px', 'size' => 0 ),
		'show_legend'              => 'yes',
		'legend_position'          => 'bottom',
		'legend_alignment'         => 'center',
		'legend_text_color'        => '#333333',
		'container_background'     => '#ffffff',
	);
}

/**
 * @return array<string, mixed>
 */
function omsar_bd_bar_chart_default_design_properties() {
	return array(
		'title'     => array(
			'color'     => '#000000',
			'alignment' => 'left',
			'margin'    => array(
				'margin' => array(
					'bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
				),
			),
		),
		'legend'    => array( 'text_color' => '#333333' ),
		'container' => array(
			'chart_height'     => array( 'number' => 400, 'unit' => 'px', 'style' => '400px' ),
			'background_color' => '#ffffff',
			'border_radius'    => array( 'number' => 0, 'unit' => 'px', 'style' => '0px' ),
			'padding'          => array(
				'padding' => array(
					'top'    => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'right'  => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
					'left'   => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
				),
			),
		),
		'advanced'  => array(
			'chart_alignment' => 'center',
			'chart_width'     => array( 'number' => 100, 'unit' => '%', 'style' => '100%' ),
		),
	);
}

/**
 * @param mixed $value
 * @return string
 */
function omsar_bd_bar_chart_yes_no( $value, $default = 'no' ) {
	if ( $value === 'yes' || $value === true || $value === 1 || $value === '1' ) {
		return 'yes';
	}
	if ( $value === 'no' || $value === false || $value === 0 || $value === '0' ) {
		return 'no';
	}
	if ( $value === null || $value === '' ) {
		return $default;
	}
	return (string) $value;
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_normalize_bar_chart_settings( $settings ) {
	$defaults = omsar_bd_bar_chart_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['show_export_icon']        = omsar_bd_bar_chart_yes_no( $settings['show_export_icon'] ?? 'no', 'no' );
	$settings['show_zoom_icon']          = omsar_bd_bar_chart_yes_no( $settings['show_zoom_icon'] ?? 'no', 'no' );
	$settings['show_y_axis_label_title'] = omsar_bd_bar_chart_yes_no( $settings['show_y_axis_label_title'] ?? 'yes', 'yes' );
	$settings['reverse_chart']           = omsar_bd_bar_chart_yes_no( $settings['reverse_chart'] ?? 'no', 'no' );
	$settings['show_legend']             = omsar_bd_bar_chart_yes_no( $settings['show_legend'] ?? 'yes', 'yes' );

	if ( ! is_array( $settings['y_axis_values'] ) ) {
		$settings['y_axis_values'] = $defaults['y_axis_values'];
	}
	if ( ! is_array( $settings['bars_config'] ) ) {
		$settings['bars_config'] = $defaults['bars_config'];
	}
	if ( ! is_array( $settings['chart_data'] ) ) {
		$settings['chart_data'] = $defaults['chart_data'];
	}

	if ( empty( $settings['bar_border_radius'] ) || ! is_array( $settings['bar_border_radius'] ) ) {
		$settings['bar_border_radius'] = array( 'unit' => 'px', 'size' => 0 );
	} elseif ( isset( $settings['bar_border_radius']['number'] ) && ! isset( $settings['bar_border_radius']['size'] ) ) {
		$settings['bar_border_radius']['size'] = (int) $settings['bar_border_radius']['number'];
		$settings['bar_border_radius']['unit'] = $settings['bar_border_radius']['unit'] ?? 'px';
	}

	return $settings;
}

/**
 * Merge Breakdance design properties into Elementor-shaped settings for render/JS.
 *
 * @param array<string, mixed> $settings
 * @param array<string, mixed> $design
 * @return array<string, mixed>
 */
function omsar_bd_bar_chart_merge_design_into_settings( $settings, $design ) {
	$design = wp_parse_args( is_array( $design ) ? $design : array(), omsar_bd_bar_chart_default_design_properties() );

	if ( ! empty( $design['legend']['text_color'] ) ) {
		$settings['legend_text_color'] = $design['legend']['text_color'];
	}
	if ( ! empty( $design['container']['background_color'] ) ) {
		$settings['container_background'] = $design['container']['background_color'];
	}

	return $settings;
}

/**
 * @param array<string, mixed> $properties_data
 * @return array<string, mixed>
 */
function omsar_bd_bar_chart_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content'] ?? array();
	$chart   = $content['chart'] ?? array();
	$y_axis  = $content['y_axis'] ?? array();
	$bars    = $content['bars'] ?? array();
	$data    = $content['data'] ?? array();
	$style   = $content['bar_style'] ?? array();
	$legend  = $content['legend'] ?? array();
	$design  = $properties_data['design'] ?? array();

	$settings = array(
		'chart_title'             => isset( $chart['chart_title'] ) ? trim( (string) $chart['chart_title'] ) : '',
		'show_export_icon'        => ! empty( $chart['show_export_icon'] ),
		'show_zoom_icon'          => ! empty( $chart['show_zoom_icon'] ),
		'y_axis_label'            => isset( $chart['y_axis_label'] ) ? trim( (string) $chart['y_axis_label'] ) : '%',
		'show_y_axis_label_title' => ! isset( $chart['show_y_axis_label_title'] ) || ! empty( $chart['show_y_axis_label_title'] ),
		'reverse_chart'           => ! empty( $chart['reverse_chart'] ),
		'y_axis_values'           => $y_axis['y_axis_values'] ?? array(),
		'bars_config'             => $bars['bars_config'] ?? array(),
		'chart_data'              => $data['chart_data'] ?? array(),
		'bar_border_radius'       => isset( $style['bar_border_radius'] ) ? array(
			'size' => (int) ( $style['bar_border_radius']['number'] ?? 0 ),
			'unit' => $style['bar_border_radius']['unit'] ?? 'px',
		) : array( 'size' => 0, 'unit' => 'px' ),
		'show_legend'             => ! isset( $legend['show_legend'] ) || ! empty( $legend['show_legend'] ),
		'legend_position'         => $legend['legend_position'] ?? 'bottom',
		'legend_alignment'        => $legend['legend_alignment'] ?? 'center',
	);

	$settings = omsar_bd_normalize_bar_chart_settings( $settings );
	return omsar_bd_bar_chart_merge_design_into_settings( $settings, $design );
}

/**
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function omsar_bd_bar_chart_settings_from_elementor( $settings ) {
	return omsar_bd_normalize_bar_chart_settings( $settings );
}

/**
 * Render the bar chart widget markup and scripts.
 *
 * @param array<string, mixed> $settings  Elementor-compatible settings.
 * @param string               $element_id Unique instance id (without prefix).
 * @param array<string, mixed> $args       Optional render args.
 */
function omsar_bd_render_bar_chart_widget( $settings, $element_id, $args = array() ) {
	$settings = omsar_bd_normalize_bar_chart_settings( $settings );
	$breakdance = ! empty( $args['breakdance'] );

	$chart_title = ! empty( $settings['chart_title'] ) ? esc_html( $settings['chart_title'] ) : '';
		$y_axis_label = ! empty( $settings['y_axis_label'] ) ? esc_html( $settings['y_axis_label'] ) : '';
		$show_y_axis_label_title = ! empty( $settings['show_y_axis_label_title'] ) && $settings['show_y_axis_label_title'] === 'yes';
		$bar_border_radius = ! empty( $settings['bar_border_radius']['size'] ) ? intval( $settings['bar_border_radius']['size'] ) : 0;
		$chart_data = ! empty( $settings['chart_data'] ) ? $settings['chart_data'] : [];
		$y_axis_values = ! empty( $settings['y_axis_values'] ) ? $settings['y_axis_values'] : [];
		$bars_config = ! empty( $settings['bars_config'] ) ? $settings['bars_config'] : [];
		$show_legend = ! empty( $settings['show_legend'] ) && $settings['show_legend'] === 'yes';
		$legend_position = ! empty( $settings['legend_position'] ) ? esc_attr( $settings['legend_position'] ) : 'bottom';
		$legend_alignment = ! empty( $settings['legend_alignment'] ) ? esc_attr( $settings['legend_alignment'] ) : 'center';
		$reverse_chart = ! empty( $settings['reverse_chart'] ) && $settings['reverse_chart'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-chart-' . $element_id;
		$canvas_id = $widget_id . '-canvas';

		// Prepare bars configuration
		$bars = [];
		foreach ( $bars_config as $bar_config ) {
			$bars[] = [
				'name' => ! empty( $bar_config['bar_name'] ) ? esc_html( $bar_config['bar_name'] ) : esc_html__( 'Bar', 'omsar' ),
				'color' => ! empty( $bar_config['bar_color'] ) ? esc_attr( $bar_config['bar_color'] ) : '#2563eb',
			];
		}

		// If no bars configured, use default 2 bars for backward compatibility
		if ( empty( $bars ) ) {
			$bars = [
				[
					'name' => esc_html__( 'Bar 1', 'omsar' ),
					'color' => ! empty( $settings['bar_color_1'] ) ? esc_attr( $settings['bar_color_1'] ) : '#2563eb',
				],
				[
					'name' => esc_html__( 'Bar 2', 'omsar' ),
					'color' => ! empty( $settings['bar_color_2'] ) ? esc_attr( $settings['bar_color_2'] ) : '#60a5fa',
				],
			];
		}

		// Prepare data arrays
		$labels = [];
		$datasets_data = [];

		// Initialize datasets arrays
		foreach ( $bars as $index => $bar ) {
			$datasets_data[ $index ] = [];
		}

		foreach ( $chart_data as $item ) {
			$labels[] = ! empty( $item['category_label'] ) ? esc_html( $item['category_label'] ) : '';
			
			// Check if using new bar_values repeater
			if ( ! empty( $item['bar_values'] ) && is_array( $item['bar_values'] ) ) {
				// Use new bar_values repeater
				foreach ( $bars as $bar_index => $bar ) {
					$bar_value = 0;
					if ( isset( $item['bar_values'][ $bar_index ] ) && isset( $item['bar_values'][ $bar_index ]['bar_value'] ) ) {
						$bar_value = floatval( $item['bar_values'][ $bar_index ]['bar_value'] );
					}
					$datasets_data[ $bar_index ][] = $bar_value;
				}
			} else {
				// Use legacy value_1 and value_2 for backward compatibility
				$datasets_data[0][] = ! empty( $item['value_1'] ) ? floatval( $item['value_1'] ) : 0;
				if ( isset( $datasets_data[1] ) ) {
					$datasets_data[1][] = ! empty( $item['value_2'] ) ? floatval( $item['value_2'] ) : 0;
				}
			}
		}

		// Prepare Y-axis values
		$y_axis_labels = [];
		$y_axis_numeric_values = [];
		
		foreach ( $y_axis_values as $y_item ) {
			$y_value = ! empty( $y_item['y_axis_value'] ) ? trim( $y_item['y_axis_value'] ) : '';
			if ( $y_value !== '' ) {
				// Store the raw value (without label) for display
				$y_axis_labels[] = esc_html( $y_value );
				// Try to convert to number for positioning, if it's numeric
				$numeric_value = is_numeric( $y_value ) ? floatval( $y_value ) : null;
				$y_axis_numeric_values[] = $numeric_value;
			}
		}

		// If no Y-axis values provided, use default numeric scale
		if ( empty( $y_axis_labels ) ) {
			$y_axis_labels = [ '0', '20', '40', '60', '80', '100' ];
			$y_axis_numeric_values = [ 0, 20, 40, 60, 80, 100 ];
		}

		// Build datasets from bars configuration
		$datasets = [];
		foreach ( $bars as $index => $bar ) {
			$datasets[] = [
				'label' => $bar['name'],
				'data' => isset( $datasets_data[ $index ] ) ? $datasets_data[ $index ] : [],
				'backgroundColor' => $bar['color'],
				'borderColor' => $bar['color'],
				'borderWidth' => 0,
				'borderRadius' => $bar_border_radius,
			];
		}

		// Reverse chart data if reverse_chart is enabled
		if ( $reverse_chart ) {
			$labels = array_reverse( $labels );
			$datasets = array_reverse( $datasets );
			// Reverse data within each dataset
			foreach ( $datasets as $index => $dataset ) {
				$datasets[ $index ]['data'] = array_reverse( $dataset['data'] );
			}
		}

		// Convert to JSON for JavaScript
		$chart_config = [
			'labels' => $labels,
			'datasets' => $datasets,
			'yAxisLabels' => $y_axis_labels,
			'yAxisNumericValues' => $y_axis_numeric_values,
			'yAxisLabel' => $y_axis_label,
			'showYAxisLabelTitle' => $show_y_axis_label_title,
			'showLegend' => $show_legend,
			'legendPosition' => $legend_position,
			'legendAlignment' => $legend_alignment,
			'reverseChart' => $reverse_chart,
		];

		// Get legend style settings
		$legend_text_color = ! empty( $settings['legend_text_color'] ) ? esc_attr( $settings['legend_text_color'] ) : '#333333';
		
		// Add text color to chart config
		$chart_config['legendTextColor'] = $legend_text_color;
		
		// Get container background color for export
		$container_background = ! empty( $settings['container_background'] ) ? esc_attr( $settings['container_background'] ) : '#ffffff';
		$chart_config['containerBackground'] = $container_background;
		
		// Get header action icons settings
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';
		
		?>
		<style>
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-wrapper canvas {
				max-height: 100%;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-header .omsar-chart-title {
				margin: 0;
				flex: 1;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				width: 32px;
				height: 32px;
				border: none;
				border-radius: 4px;
				background-color: #ffffff;
				color: #374151;
				cursor: pointer;
				transition: all 0.2s ease;
				position: relative;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon[data-tooltip]:hover::before {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: 5px;
				padding: 6px 10px;
				background-color: #1f2937;
				color: #ffffff;
				font-size: 12px;
				white-space: nowrap;
				border-radius: 4px;
				pointer-events: none;
				z-index: 1000;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-chart-action-icon[data-tooltip]:hover::after {
				content: '';
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				margin-bottom: -1px;
				width: 0;
				height: 0;
				border-left: 5px solid transparent;
				border-right: 5px solid transparent;
				border-top: 5px solid #1f2937;
				pointer-events: none;
				z-index: 1000;
			}
			/* Modal styles for zoom view */
			.omsar-chart-modal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.75);
				z-index: 9999;
				overflow: auto;
				padding: 20px;
				box-sizing: border-box;
			}
			.omsar-chart-modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-chart-modal-content {
				position: relative;
				background-color: #ffffff;
				border-radius: 8px;
				padding: 30px;
				max-width: 90%;
				max-height: 100%;
				overflow: hidden;
				box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
				display: flex;
				flex-direction: column;
			}
			.omsar-chart-modal-close {
				position: absolute;
				top: 10px;
				right: 10px;
				width: 32px;
				height: 32px;
				border: none;
				background-color: #f3f4f6;
				border-radius: 4px;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #374151;
				transition: all 0.2s ease;
				z-index: 1;
			}
			html[dir="rtl"] .omsar-chart-modal-close {
				left: 10px;  
				right: auto;  
			}

			.omsar-chart-modal-close:hover {
				background-color: #e5e7eb;
				color: #111827;
			}
			.omsar-chart-modal-close svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			.omsar-chart-modal-title {
				margin: 0 0 20px 0;
				font-size: 20px;
				font-weight: 600;
				flex-shrink: 0;
			}
			.omsar-chart-modal-container {
				width: 100%;
				flex: 1;
				min-height: 0;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-chart-modal-container canvas {
				max-width: 100%;
				max-height: 100%;
				height: auto !important;
				width: 1611px;
			}

		</style>
		<div class="omsar-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>"<?php echo omsar_bd_chart_wrapper_data_attributes( 'bar', $chart_config, $breakdance ); ?>>
			<?php if ( ! empty( $chart_title ) || $show_export_icon || $show_zoom_icon ) : ?>
				<div class="omsar-chart-header">
			<?php if ( ! empty( $chart_title ) ) : ?>
				<h3 class="omsar-chart-title"><?php echo esc_html( $chart_title ); ?></h3>
					<?php else : ?>
						<div></div>
					<?php endif; ?>
					<?php if ( $show_export_icon || $show_zoom_icon ) : ?>
						<div class="omsar-chart-header-actions">
							<?php if ( $show_export_icon ) : ?>
								<button type="button" class="omsar-chart-action-icon omsar-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<?php endif; ?>
							<?php if ( $show_zoom_icon ) : ?>
								<button type="button" class="omsar-chart-action-icon omsar-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="omsar-chart-container">
				<canvas id="<?php echo esc_attr( $canvas_id ); ?>"></canvas>
			</div>
		</div>

		<?php if ( ! $breakdance ) : ?>
		<script type="text/javascript">
		(function() {
			'use strict';
			
			var initChart = function() {
				if (typeof Chart === 'undefined') {
					setTimeout(initChart, 100);
					return;
				}

				var canvas = document.getElementById('<?php echo esc_js( $canvas_id ); ?>');
				if (!canvas) {
					return;
				}

				var ctx = canvas.getContext('2d');
				var config = <?php echo wp_json_encode( $chart_config ); ?>;

				var chart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: config.labels,
						datasets: config.datasets
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: config.showLegend,
								position: config.legendPosition,
								align: config.legendAlignment,
								labels: {
									boxWidth: 10,
									boxHeight: 10,
									color: config.legendTextColor || '#333333',
								},
							},
							tooltip: {
								enabled: true,
								callbacks: {
									label: function(context) {
										return context.dataset.label + ': ' + context.parsed.y + (config.yAxisLabel ? ' ' + config.yAxisLabel.replace(/[()]/g, '') : '');
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								position: config.reverseChart ? 'right' : 'left',
								afterBuildTicks: function(scale) {
									// Clear default ticks
									scale.ticks = [];
									
									// Add custom ticks from configuration
									if (config.yAxisNumericValues && config.yAxisNumericValues.length > 0) {
										var allNumeric = config.yAxisNumericValues.every(function(val) {
											return val !== null && !isNaN(val);
										});
										
										if (allNumeric) {
											// All values are numeric, use them for positioning
											var min = Math.min.apply(null, config.yAxisNumericValues);
											var max = Math.max.apply(null, config.yAxisNumericValues);
											scale.min = min;
											scale.max = max;
											
											config.yAxisNumericValues.forEach(function(numVal, index) {
												var baseLabel = config.yAxisLabels[index] || String(numVal);
												var displayLabel = baseLabel;
												// Append Y-axis label to the value if label is provided
												if (config.yAxisLabel && config.yAxisLabel.trim() !== '') {
													displayLabel = baseLabel + config.yAxisLabel;
												}
												scale.ticks.push({
													value: numVal,
													label: displayLabel
												});
											});
										} else {
											// Mixed or non-numeric, use index-based positioning
											var maxDataValue = 0;
											config.datasets.forEach(function(dataset) {
												if (dataset.data && dataset.data.length > 0) {
													var datasetMax = Math.max.apply(null, dataset.data);
													if (datasetMax > maxDataValue) {
														maxDataValue = datasetMax;
													}
												}
											});
											scale.max = maxDataValue * 1.1;
											
											config.yAxisLabels.forEach(function(label, index) {
												var numVal = config.yAxisNumericValues[index];
												var baseLabel = label;
												var displayLabel = baseLabel;
												// Append Y-axis label to the value if label is provided
												if (config.yAxisLabel && config.yAxisLabel.trim() !== '') {
													displayLabel = baseLabel + config.yAxisLabel;
												}
												if (numVal !== null && !isNaN(numVal)) {
													scale.ticks.push({
														value: numVal,
														label: displayLabel
													});
												} else {
													// Calculate position based on index
													var position = (index / (config.yAxisLabels.length - 1)) * scale.max;
													scale.ticks.push({
														value: position,
														label: displayLabel
													});
												}
											});
										}
									}
								},
								ticks: {
									callback: function(value, index, ticks) {
										// Find the tick that matches this value
										var tick = ticks.find(function(t) {
											return t.value === value;
										});
										if (tick && tick.label) {
											return tick.label;
										}
										return value;
									}
								},
								title: {
									display: config.showYAxisLabelTitle && config.yAxisLabel ? true : false,
									text: config.yAxisLabel || ''
								},
								grid: {
									color: 'rgba(0, 0, 0, 0.1)'
								}
							},
							x: {
								grid: {
									display: false
								}
							}
						}
					}
				});

				// Store chart instance for export functionality
				var chartInstance = chart;

				// Export image functionality
				var exportIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-chart-export-icon');
				if (exportIcon) {
					exportIcon.addEventListener('click', function() {
						var canvasId = this.getAttribute('data-canvas-id');
						var chartTitle = this.getAttribute('data-chart-title') || 'chart';
						var canvas = document.getElementById(canvasId);
						
						if (!canvas) {
							return;
						}

						// Create a new canvas with background color
						var exportCanvas = document.createElement('canvas');
						exportCanvas.width = canvas.width;
						exportCanvas.height = canvas.height;
						var exportCtx = exportCanvas.getContext('2d');
						
						// Fill background with container background color
						var bgColor = config.containerBackground || '#ffffff';
						exportCtx.fillStyle = bgColor;
						exportCtx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
						
						// Draw the chart canvas on top
						exportCtx.drawImage(canvas, 0, 0);

						// Create a temporary link element to download the image
						var link = document.createElement('a');
						link.download = chartTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.png';
						link.href = exportCanvas.toDataURL('image/png');
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					});
				}

				// Zoom/Expand modal functionality
				var zoomIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-chart-zoom-icon');
				if (zoomIcon) {
					zoomIcon.addEventListener('click', function() {
						var widgetId = this.getAttribute('data-widget-id');
						var canvasId = this.getAttribute('data-canvas-id');
						var chartTitle = this.getAttribute('data-chart-title') || '';
						var canvas = document.getElementById(canvasId);
						
						if (!canvas) {
							return;
						}

						// Create modal if it doesn't exist
						var modalId = 'omsar-chart-modal-' + widgetId;
						var existingModal = document.getElementById(modalId);
						if (existingModal) {
							existingModal.remove();
						}

						var modal = document.createElement('div');
						modal.id = modalId;
						modal.className = 'omsar-chart-modal active';
						modal.setAttribute('role', 'dialog');
						modal.setAttribute('aria-modal', 'true');
						modal.setAttribute('aria-labelledby', modalId + '-title');

						var modalContent = document.createElement('div');
						modalContent.className = 'omsar-chart-modal-content container';

						var closeButton = document.createElement('button');
						closeButton.type = 'button';
						closeButton.className = 'omsar-chart-modal-close';
						closeButton.setAttribute('aria-label', 'Close modal');
						closeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
						closeButton.addEventListener('click', function() {
							modal.classList.remove('active');
							setTimeout(function() {
								modal.remove();
							}, 300);
						});

						if (chartTitle) {
							var modalTitle = document.createElement('h3');
							modalTitle.id = modalId + '-title';
							modalTitle.className = 'omsar-chart-modal-title';
							modalTitle.textContent = chartTitle;
							modalContent.appendChild(modalTitle);
						}

						var modalContainer = document.createElement('div');
						modalContainer.className = 'omsar-chart-modal-container';

						// Create new canvas for modal
						var clonedCanvas = document.createElement('canvas');
						modalContainer.appendChild(clonedCanvas);

						// Recreate chart in modal with same data and configuration
						setTimeout(function() {
							var clonedCtx = clonedCanvas.getContext('2d');
							var clonedChart = new Chart(clonedCtx, {
								type: 'bar',
								data: {
									labels: config.labels,
									datasets: JSON.parse(JSON.stringify(config.datasets))
								},
								options: {
									responsive: true,
									maintainAspectRatio: true,
									plugins: {
										legend: {
											display: config.showLegend,
											position: config.legendPosition,
											align: config.legendAlignment,
											labels: {
												boxWidth: 10,
												boxHeight: 10,
												color: config.legendTextColor || '#333333',
											},
										},
										tooltip: {
											enabled: true,
											callbacks: {
												label: function(context) {
													return context.dataset.label + ': ' + context.parsed.y + (config.yAxisLabel ? ' ' + config.yAxisLabel.replace(/[()]/g, '') : '');
												}
											}
										}
									},
						scales: {
							y: {
								beginAtZero: true,
								position: config.reverseChart ? 'right' : 'left',
								afterBuildTicks: function(scale) {
												scale.ticks = [];
												if (config.yAxisNumericValues && config.yAxisNumericValues.length > 0) {
													var allNumeric = config.yAxisNumericValues.every(function(val) {
														return val !== null && !isNaN(val);
													});
													if (allNumeric) {
														var min = Math.min.apply(null, config.yAxisNumericValues);
														var max = Math.max.apply(null, config.yAxisNumericValues);
														scale.min = min;
														scale.max = max;
														config.yAxisNumericValues.forEach(function(numVal, index) {
															var baseLabel = config.yAxisLabels[index] || String(numVal);
															var displayLabel = baseLabel;
															if (config.yAxisLabel && config.yAxisLabel.trim() !== '') {
																displayLabel = baseLabel + config.yAxisLabel;
															}
															scale.ticks.push({
																value: numVal,
																label: displayLabel
															});
														});
													} else {
														var maxDataValue = 0;
														config.datasets.forEach(function(dataset) {
															if (dataset.data && dataset.data.length > 0) {
																var datasetMax = Math.max.apply(null, dataset.data);
																if (datasetMax > maxDataValue) {
																	maxDataValue = datasetMax;
																}
															}
														});
														scale.max = maxDataValue * 1.1;
														config.yAxisLabels.forEach(function(label, index) {
															var numVal = config.yAxisNumericValues[index];
															var baseLabel = label;
															var displayLabel = baseLabel;
															if (config.yAxisLabel && config.yAxisLabel.trim() !== '') {
																displayLabel = baseLabel + config.yAxisLabel;
															}
															if (numVal !== null && !isNaN(numVal)) {
																scale.ticks.push({
																	value: numVal,
																	label: displayLabel
																});
															} else {
																var position = (index / (config.yAxisLabels.length - 1)) * scale.max;
																scale.ticks.push({
																	value: position,
																	label: displayLabel
																});
															}
														});
													}
												}
											},
											ticks: {
												callback: function(value, index, ticks) {
													var tick = ticks.find(function(t) {
														return t.value === value;
													});
													if (tick && tick.label) {
														return tick.label;
													}
													return value;
												}
											},
											title: {
												display: config.showYAxisLabelTitle && config.yAxisLabel ? true : false,
												text: config.yAxisLabel || ''
											},
											grid: {
												color: 'rgba(0, 0, 0, 0.1)'
											}
										},
										x: {
											grid: {
												display: false
											}
										}
									}
								}
							});
						}, 100);
						modalContent.appendChild(closeButton);
						modalContent.appendChild(modalContainer);
						modal.appendChild(modalContent);
						document.body.appendChild(modal);

						// Close modal on ESC key
						var handleEsc = function(e) {
							if (e.key === 'Escape') {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
								document.removeEventListener('keydown', handleEsc);
							}
						};
						document.addEventListener('keydown', handleEsc);

						// Close modal on backdrop click
						modal.addEventListener('click', function(e) {
							if (e.target === modal) {
								modal.classList.remove('active');
								setTimeout(function() {
									modal.remove();
								}, 300);
							}
						});
					});
				}
			};

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initChart);
			} else {
				initChart();
			}
		})();
		</script>
		<?php endif; ?>
		<?php
}

