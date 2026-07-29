<?php
/**
 * Line Chart rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function omsar_bd_line_chart_default_settings() {
	return array(
		'chart_title'          => __( 'Chart title goes here', 'omsar' ),
		'show_export_icon'     => 'no',
		'show_zoom_icon'       => 'no',
		'y_axis_label'         => '',
		'reverse_chart'        => 'no',
		'y_axis_values'        => array(
			array( 'y_axis_value' => '0' ),
			array( 'y_axis_value' => '25' ),
			array( 'y_axis_value' => '50' ),
			array( 'y_axis_value' => '75' ),
			array( 'y_axis_value' => '100' ),
		),
		'chart_data'           => array(
			array( 'x_axis_label' => __( 'Jan', 'omsar' ), 'data_value' => 28 ),
			array( 'x_axis_label' => __( 'Feb', 'omsar' ), 'data_value' => 45 ),
			array( 'x_axis_label' => __( 'Mar', 'omsar' ), 'data_value' => 38 ),
			array( 'x_axis_label' => __( 'Apr', 'omsar' ), 'data_value' => 55 ),
			array( 'x_axis_label' => __( 'May', 'omsar' ), 'data_value' => 68 ),
			array( 'x_axis_label' => __( 'Jun', 'omsar' ), 'data_value' => 75 ),
			array( 'x_axis_label' => __( 'Jul', 'omsar' ), 'data_value' => 80 ),
		),
		'line_label'           => __( 'Data', 'omsar' ),
		'line_color'           => '#2563eb',
		'line_width'           => array( 'unit' => 'px', 'size' => 2 ),
		'show_points'          => 'yes',
		'point_radius'         => array( 'unit' => 'px', 'size' => 4 ),
		'point_color'          => '#2563eb',
		'fill_area'            => 'no',
		'fill_color'           => 'rgba(37, 99, 235, 0.1)',
		'show_grid'            => 'yes',
		'grid_color'           => 'rgba(0, 0, 0, 0.1)',
		'grid_line_width'      => array( 'unit' => 'px', 'size' => 1 ),
		'show_legend'          => 'yes',
		'legend_position'      => 'bottom',
		'legend_alignment'     => 'center',
		'legend_text_color'    => '#333333',
		'container_background' => '#ffffff',
	);
}

function omsar_bd_line_chart_default_design_properties() {
	return array(
		'line'      => array(
			'line_color'   => '#2563eb',
			'line_width'   => array( 'number' => 2, 'unit' => 'px', 'style' => '2px' ),
			'show_points'  => true,
			'point_radius' => array( 'number' => 4, 'unit' => 'px', 'style' => '4px' ),
			'point_color'  => '#2563eb',
			'fill_area'    => false,
			'fill_color'   => 'rgba(37, 99, 235, 0.1)',
		),
		'grid'      => array(
			'show_grid'       => true,
			'grid_color'      => 'rgba(0, 0, 0, 0.1)',
			'grid_line_width' => array( 'number' => 1, 'unit' => 'px', 'style' => '1px' ),
		),
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
			'border_radius'    => array( 'number' => 8, 'unit' => 'px', 'style' => '8px' ),
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
			'chart_width' => array( 'number' => 100, 'unit' => '%', 'style' => '100%' ),
		),
	);
}

function omsar_bd_line_chart_yes_no( $value, $default = 'no' ) {
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

function omsar_bd_normalize_line_chart_slider( $value, $default ) {
	if ( empty( $value ) || ! is_array( $value ) ) {
		return $default;
	}
	if ( isset( $value['number'] ) && ! isset( $value['size'] ) ) {
		$value['size'] = $value['number'];
	}
	if ( empty( $value['unit'] ) ) {
		$value['unit'] = $default['unit'] ?? 'px';
	}
	return $value;
}

function omsar_bd_normalize_line_chart_settings( $settings ) {
	$defaults = omsar_bd_line_chart_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['show_export_icon'] = omsar_bd_line_chart_yes_no( $settings['show_export_icon'] ?? 'no', 'no' );
	$settings['show_zoom_icon']   = omsar_bd_line_chart_yes_no( $settings['show_zoom_icon'] ?? 'no', 'no' );
	$settings['reverse_chart']    = omsar_bd_line_chart_yes_no( $settings['reverse_chart'] ?? 'no', 'no' );
	$settings['show_points']      = omsar_bd_line_chart_yes_no( $settings['show_points'] ?? 'yes', 'yes' );
	$settings['fill_area']        = omsar_bd_line_chart_yes_no( $settings['fill_area'] ?? 'no', 'no' );
	$settings['show_grid']        = omsar_bd_line_chart_yes_no( $settings['show_grid'] ?? 'yes', 'yes' );
	$settings['show_legend']      = omsar_bd_line_chart_yes_no( $settings['show_legend'] ?? 'yes', 'yes' );

	if ( ! is_array( $settings['y_axis_values'] ) ) {
		$settings['y_axis_values'] = $defaults['y_axis_values'];
	}
	if ( ! is_array( $settings['chart_data'] ) ) {
		$settings['chart_data'] = $defaults['chart_data'];
	}

	$settings['line_width']      = omsar_bd_normalize_line_chart_slider( $settings['line_width'] ?? null, $defaults['line_width'] );
	$settings['point_radius']    = omsar_bd_normalize_line_chart_slider( $settings['point_radius'] ?? null, $defaults['point_radius'] );
	$settings['grid_line_width'] = omsar_bd_normalize_line_chart_slider( $settings['grid_line_width'] ?? null, $defaults['grid_line_width'] );

	return $settings;
}

function omsar_bd_line_chart_merge_design_into_settings( $settings, $design ) {
	$design = wp_parse_args( is_array( $design ) ? $design : array(), omsar_bd_line_chart_default_design_properties() );
	$line   = $design['line'] ?? array();
	$grid   = $design['grid'] ?? array();

	if ( ! empty( $line['line_color'] ) ) {
		$settings['line_color'] = $line['line_color'];
	}
	if ( isset( $line['line_width'] ) ) {
		$settings['line_width'] = array(
			'size' => (int) ( $line['line_width']['number'] ?? 2 ),
			'unit' => $line['line_width']['unit'] ?? 'px',
		);
	}
	$settings['show_points'] = omsar_bd_line_chart_yes_no( $line['show_points'] ?? true, 'yes' );
	if ( isset( $line['point_radius'] ) ) {
		$settings['point_radius'] = array(
			'size' => (int) ( $line['point_radius']['number'] ?? 4 ),
			'unit' => $line['point_radius']['unit'] ?? 'px',
		);
	}
	if ( ! empty( $line['point_color'] ) ) {
		$settings['point_color'] = $line['point_color'];
	}
	$settings['fill_area'] = omsar_bd_line_chart_yes_no( $line['fill_area'] ?? false, 'no' );
	if ( ! empty( $line['fill_color'] ) ) {
		$settings['fill_color'] = $line['fill_color'];
	}

	$settings['show_grid'] = omsar_bd_line_chart_yes_no( $grid['show_grid'] ?? true, 'yes' );
	if ( ! empty( $grid['grid_color'] ) ) {
		$settings['grid_color'] = $grid['grid_color'];
	}
	if ( isset( $grid['grid_line_width'] ) ) {
		$settings['grid_line_width'] = array(
			'size' => (float) ( $grid['grid_line_width']['number'] ?? 1 ),
			'unit' => $grid['grid_line_width']['unit'] ?? 'px',
		);
	}

	if ( ! empty( $design['legend']['text_color'] ) ) {
		$settings['legend_text_color'] = $design['legend']['text_color'];
	}
	if ( ! empty( $design['container']['background_color'] ) ) {
		$settings['container_background'] = $design['container']['background_color'];
	}

	return $settings;
}

function omsar_bd_line_chart_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content'] ?? array();
	$chart   = $content['chart'] ?? array();
	$y_axis  = $content['y_axis'] ?? array();
	$data    = $content['data'] ?? array();
	$legend  = $content['legend'] ?? array();
	$design  = $properties_data['design'] ?? array();

	$settings = array(
		'chart_title'      => isset( $chart['chart_title'] ) ? trim( (string) $chart['chart_title'] ) : '',
		'show_export_icon' => ! empty( $chart['show_export_icon'] ),
		'show_zoom_icon'   => ! empty( $chart['show_zoom_icon'] ),
		'y_axis_label'     => isset( $chart['y_axis_label'] ) ? trim( (string) $chart['y_axis_label'] ) : '',
		'reverse_chart'    => ! empty( $chart['reverse_chart'] ),
		'line_label'       => isset( $chart['line_label'] ) ? trim( (string) $chart['line_label'] ) : __( 'Data', 'omsar' ),
		'y_axis_values'    => $y_axis['y_axis_values'] ?? array(),
		'chart_data'       => $data['chart_data'] ?? array(),
		'show_legend'      => ! isset( $legend['show_legend'] ) || ! empty( $legend['show_legend'] ),
		'legend_position'  => $legend['legend_position'] ?? 'bottom',
		'legend_alignment' => $legend['legend_alignment'] ?? 'center',
	);

	$settings = omsar_bd_normalize_line_chart_settings( $settings );
	return omsar_bd_line_chart_merge_design_into_settings( $settings, $design );
}

function omsar_bd_line_chart_settings_from_elementor( $settings ) {
	return omsar_bd_normalize_line_chart_settings( $settings );
}

function omsar_bd_render_line_chart_widget( $settings, $element_id, $args = array() ) {
	$settings = omsar_bd_normalize_line_chart_settings( $settings );
	$breakdance = ! empty( $args['breakdance'] );
	$chart_title = ! empty( $settings['chart_title'] ) ? esc_html( $settings['chart_title'] ) : '';
		$y_axis_label = ! empty( $settings['y_axis_label'] ) ? esc_html( $settings['y_axis_label'] ) : '';
		$line_label = ! empty( $settings['line_label'] ) ? esc_html( $settings['line_label'] ) : esc_html__( 'Data', 'omsar' );
		$chart_data = ! empty( $settings['chart_data'] ) ? $settings['chart_data'] : [];
		$y_axis_values = ! empty( $settings['y_axis_values'] ) ? $settings['y_axis_values'] : [];
		$line_color = ! empty( $settings['line_color'] ) ? esc_attr( $settings['line_color'] ) : '#2563eb';
		$line_width = ! empty( $settings['line_width']['size'] ) ? intval( $settings['line_width']['size'] ) : 2;
		$show_points = ! empty( $settings['show_points'] ) && $settings['show_points'] === 'yes';
		$point_radius = $show_points && ! empty( $settings['point_radius']['size'] ) ? intval( $settings['point_radius']['size'] ) : 0;
		$point_color = ! empty( $settings['point_color'] ) ? esc_attr( $settings['point_color'] ) : '#2563eb';
		$fill_area = ! empty( $settings['fill_area'] ) && $settings['fill_area'] === 'yes';
		$fill_color = ! empty( $settings['fill_color'] ) ? esc_attr( $settings['fill_color'] ) : 'rgba(37, 99, 235, 0.1)';
		$show_grid = ! empty( $settings['show_grid'] ) && $settings['show_grid'] === 'yes';
		$grid_color = ! empty( $settings['grid_color'] ) ? esc_attr( $settings['grid_color'] ) : 'rgba(0, 0, 0, 0.1)';
		$grid_line_width = ! empty( $settings['grid_line_width']['size'] ) ? floatval( $settings['grid_line_width']['size'] ) : 1;
		$show_legend = ! empty( $settings['show_legend'] ) && $settings['show_legend'] === 'yes';
		$legend_position = ! empty( $settings['legend_position'] ) ? esc_attr( $settings['legend_position'] ) : 'bottom';
		$legend_alignment = ! empty( $settings['legend_alignment'] ) ? esc_attr( $settings['legend_alignment'] ) : 'center';
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';
		$reverse_chart = ! empty( $settings['reverse_chart'] ) && $settings['reverse_chart'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-line-chart-' . $element_id;
		$canvas_id = $widget_id . '-canvas';

		// Prepare data arrays
		$labels = [];
		$values = [];

		foreach ( $chart_data as $item ) {
			$labels[] = ! empty( $item['x_axis_label'] ) ? esc_html( $item['x_axis_label'] ) : '';
			$values[] = ! empty( $item['data_value'] ) ? floatval( $item['data_value'] ) : 0;
		}

		// Reverse chart data if reverse_chart is enabled
		if ( $reverse_chart ) {
			$labels = array_reverse( $labels );
			$values = array_reverse( $values );
		}

		// Prepare Y-axis values
		$y_axis_labels = [];
		$y_axis_numeric_values = [];
		
		foreach ( $y_axis_values as $y_item ) {
			$y_value = ! empty( $y_item['y_axis_value'] ) ? trim( $y_item['y_axis_value'] ) : '';
			if ( $y_value !== '' ) {
				$y_axis_labels[] = esc_html( $y_value );
				// Try to convert to number for positioning, if it's numeric
				$numeric_value = is_numeric( $y_value ) ? floatval( $y_value ) : null;
				$y_axis_numeric_values[] = $numeric_value;
			}
		}

		// If no Y-axis values provided, use default numeric scale
		if ( empty( $y_axis_labels ) ) {
			$y_axis_labels = [ '0', '25', '50', '75', '100' ];
			$y_axis_numeric_values = [ 0, 25, 50, 75, 100 ];
		}

		// Convert to JSON for JavaScript
		$chart_config = [
			'labels' => $labels,
			'data' => $values,
			'lineLabel' => $line_label,
			'lineColor' => $line_color,
			'lineWidth' => $line_width,
			'pointRadius' => $point_radius,
			'pointColor' => $point_color,
			'fillArea' => $fill_area,
			'fillColor' => $fill_color,
			'yAxisLabels' => $y_axis_labels,
			'yAxisNumericValues' => $y_axis_numeric_values,
			'yAxisLabel' => $y_axis_label,
			'showGrid' => $show_grid,
			'gridColor' => $grid_color,
			'gridLineWidth' => $grid_line_width,
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

		?>
		<style>
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-wrapper canvas {
				max-height: 100%;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-header .omsar-line-chart-title {
				margin: 0;
				flex: 1;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon {
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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon[data-tooltip]:hover::before {
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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-line-chart-action-icon[data-tooltip]:hover::after {
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
			.omsar-line-chart-modal {
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
			.omsar-line-chart-modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-line-chart-modal-content {
				position: relative;
				background-color: #ffffff;
				border-radius: 8px;
				padding: 30px;
				max-width: 90%;
				max-height: 100%;
				overflow: hidden;
				box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
			}
			.omsar-line-chart-modal-close {
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
			}
			html[dir="rtl"] .omsar-line-chart-modal-close {
				left: 10px;  
				right: auto;  
			}
			.omsar-line-chart-modal-close:hover {
				background-color: #e5e7eb;
				color: #111827;
			}
			.omsar-line-chart-modal-close svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			.omsar-line-chart-modal-title {
				margin: 0 0 20px 0;
				font-size: 20px;
				font-weight: 600;
			}
			.omsar-line-chart-modal-container {
				width: 100%;
				height: auto;
				min-height: 400px;
			}
			.omsar-line-chart-modal-container canvas {
				max-width: 100%;
				height: auto !important;
			}
		</style>
		<div class="omsar-line-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>"<?php echo omsar_bd_chart_wrapper_data_attributes( 'line', $chart_config, $breakdance ); ?>>
			<?php if ( ! empty( $chart_title ) || $show_export_icon || $show_zoom_icon ) : ?>
				<div class="omsar-line-chart-header">
					<?php if ( ! empty( $chart_title ) ) : ?>
						<h3 class="omsar-line-chart-title"><?php echo esc_html( $chart_title ); ?></h3>
					<?php else : ?>
						<div></div>
					<?php endif; ?>
					<?php if ( $show_export_icon || $show_zoom_icon ) : ?>
						<div class="omsar-line-chart-header-actions">
							<?php if ( $show_export_icon ) : ?>
								<button type="button" class="omsar-line-chart-action-icon omsar-line-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<?php endif; ?>
							<?php if ( $show_zoom_icon ) : ?>
								<button type="button" class="omsar-line-chart-action-icon omsar-line-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="omsar-line-chart-container">
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
					type: 'line',
					data: {
						labels: config.labels,
						datasets: [{
							label: config.lineLabel,
							data: config.data,
							borderColor: config.lineColor,
							backgroundColor: config.fillArea ? config.fillColor : 'transparent',
							borderWidth: config.lineWidth,
							pointRadius: config.pointRadius,
							pointBackgroundColor: config.pointColor,
							pointBorderColor: config.pointColor,
							pointHoverRadius: config.pointRadius > 0 ? config.pointRadius + 2 : 0,
							fill: config.fillArea,
							tension: 0.1
						}]
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
										return context.dataset.label + ': ' + context.parsed.y;
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
												scale.ticks.push({
													value: numVal,
													label: config.yAxisLabels[index] || String(numVal)
												});
											});
										} else {
											// Mixed or non-numeric, use index-based positioning
											var maxDataValue = Math.max.apply(null, config.data);
											scale.max = maxDataValue * 1.1;
											
											config.yAxisLabels.forEach(function(label, index) {
												var numVal = config.yAxisNumericValues[index];
												if (numVal !== null && !isNaN(numVal)) {
													scale.ticks.push({
														value: numVal,
														label: label
													});
												} else {
													// Calculate position based on index
													var position = (index / (config.yAxisLabels.length - 1)) * scale.max;
													scale.ticks.push({
														value: position,
														label: label
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
									display: config.yAxisLabel ? true : false,
									text: config.yAxisLabel
								},
								grid: {
									display: config.showGrid,
									color: config.gridColor,
									lineWidth: config.gridLineWidth
								}
							},
							x: {
								grid: {
									display: config.showGrid,
									color: config.gridColor,
									lineWidth: config.gridLineWidth
								}
							}
						}
					}
				});

				// Store chart instance for export functionality
				var chartInstance = chart;

				// Export image functionality
				var exportIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-line-chart-export-icon');
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
				var zoomIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-line-chart-zoom-icon');
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
						var modalId = 'omsar-line-chart-modal-' + widgetId;
						var existingModal = document.getElementById(modalId);
						if (existingModal) {
							existingModal.remove();
						}

						var modal = document.createElement('div');
						modal.id = modalId;
						modal.className = 'omsar-line-chart-modal active';
						modal.setAttribute('role', 'dialog');
						modal.setAttribute('aria-modal', 'true');
						modal.setAttribute('aria-labelledby', modalId + '-title');

						var modalContent = document.createElement('div');
						modalContent.className = 'omsar-line-chart-modal-content container';

						var closeButton = document.createElement('button');
						closeButton.type = 'button';
						closeButton.className = 'omsar-line-chart-modal-close';
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
							modalTitle.className = 'omsar-line-chart-modal-title';
							modalTitle.textContent = chartTitle;
							modalContent.appendChild(modalTitle);
						}

						var modalContainer = document.createElement('div');
						modalContainer.className = 'omsar-line-chart-modal-container';

						// Create new canvas for modal
						var clonedCanvas = document.createElement('canvas');
						modalContainer.appendChild(clonedCanvas);

						// Recreate chart in modal with same data and configuration
						setTimeout(function() {
							var clonedCtx = clonedCanvas.getContext('2d');
							var clonedChart = new Chart(clonedCtx, {
								type: 'line',
								data: {
									labels: config.labels,
									datasets: [{
										label: config.lineLabel,
										data: JSON.parse(JSON.stringify(config.data)),
										borderColor: config.lineColor,
										backgroundColor: config.fillArea ? config.fillColor : 'transparent',
										borderWidth: config.lineWidth,
										pointRadius: config.pointRadius,
										pointBackgroundColor: config.pointColor,
										pointBorderColor: config.pointColor,
										pointHoverRadius: config.pointRadius > 0 ? config.pointRadius + 2 : 0,
										fill: config.fillArea,
										tension: 0.1
									}]
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
													return context.dataset.label + ': ' + context.parsed.y;
												}
											}
										}
									},
									scales: {
										y: {
											beginAtZero: true,
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
															scale.ticks.push({
																value: numVal,
																label: config.yAxisLabels[index] || String(numVal)
															});
														});
													} else {
														var maxDataValue = Math.max.apply(null, config.data);
														scale.max = maxDataValue * 1.1;
														config.yAxisLabels.forEach(function(label, index) {
															var numVal = config.yAxisNumericValues[index];
															if (numVal !== null && !isNaN(numVal)) {
																scale.ticks.push({
																	value: numVal,
																	label: label
																});
															} else {
																var position = (index / (config.yAxisLabels.length - 1)) * scale.max;
																scale.ticks.push({
																	value: position,
																	label: label
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
												display: config.yAxisLabel ? true : false,
												text: config.yAxisLabel
											},
											grid: {
												display: config.showGrid,
												color: config.gridColor,
												lineWidth: config.gridLineWidth
											}
										},
										x: {
											grid: {
												display: config.showGrid,
												color: config.gridColor,
												lineWidth: config.gridLineWidth
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
