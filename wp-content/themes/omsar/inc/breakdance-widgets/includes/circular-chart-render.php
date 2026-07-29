<?php
/**
 * Circular Chart rendering (shared between Elementor widget and Breakdance element).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function omsar_bd_circular_chart_default_settings() {
	return array(
		'chart_title'          => __( 'Chart title goes here', 'omsar' ),
		'show_export_icon'     => 'no',
		'show_zoom_icon'       => 'no',
		'chart_type'           => 'doughnut',
		'reverse_chart'        => 'no',
		'chart_data'           => array(
			array( 'segment_label' => __( 'Segment 1', 'omsar' ), 'segment_value' => 40, 'segment_color' => '#2563eb' ),
			array( 'segment_label' => __( 'Segment 2', 'omsar' ), 'segment_value' => 25, 'segment_color' => '#60a5fa' ),
			array( 'segment_label' => __( 'Segment 3', 'omsar' ), 'segment_value' => 20, 'segment_color' => '#93c5fd' ),
			array( 'segment_label' => __( 'Segment 4', 'omsar' ), 'segment_value' => 15, 'segment_color' => '#bfdbfe' ),
		),
		'donut_inner_radius'   => array( 'unit' => '%', 'size' => 50 ),
		'border_width'         => array( 'unit' => 'px', 'size' => 2 ),
		'border_color'         => '#ffffff',
		'show_legend'          => 'yes',
		'legend_position'      => 'bottom',
		'legend_alignment'     => 'center',
		'legend_text_color'    => '#333333',
		'container_background' => '#ffffff',
	);
}

function omsar_bd_circular_chart_default_design_properties() {
	return array(
		'chart_type' => array(
			'donut_inner_radius' => array( 'number' => 50, 'unit' => '%', 'style' => '50%' ),
			'border_width'       => array( 'number' => 2, 'unit' => 'px', 'style' => '2px' ),
			'border_color'       => '#ffffff',
		),
		'title'      => array(
			'color'     => '#000000',
			'alignment' => 'left',
			'margin'    => array(
				'margin' => array(
					'bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
				),
			),
		),
		'legend'     => array( 'text_color' => '#333333' ),
		'container'  => array(
			'chart_size'       => array( 'number' => 350, 'unit' => 'px', 'style' => '350px' ),
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
		'advanced'   => array(
			'chart_alignment' => 'center',
			'chart_width'     => array( 'number' => 100, 'unit' => '%', 'style' => '100%' ),
		),
	);
}

function omsar_bd_circular_chart_yes_no( $value, $default = 'no' ) {
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

function omsar_bd_normalize_circular_chart_settings( $settings ) {
	$defaults = omsar_bd_circular_chart_default_settings();
	$settings = wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );

	$settings['show_export_icon'] = omsar_bd_circular_chart_yes_no( $settings['show_export_icon'] ?? 'no', 'no' );
	$settings['show_zoom_icon']   = omsar_bd_circular_chart_yes_no( $settings['show_zoom_icon'] ?? 'no', 'no' );
	$settings['reverse_chart']    = omsar_bd_circular_chart_yes_no( $settings['reverse_chart'] ?? 'no', 'no' );
	$settings['show_legend']      = omsar_bd_circular_chart_yes_no( $settings['show_legend'] ?? 'yes', 'yes' );

	if ( ! is_array( $settings['chart_data'] ) ) {
		$settings['chart_data'] = $defaults['chart_data'];
	}

	foreach ( array( 'donut_inner_radius', 'border_width' ) as $slider_key ) {
		if ( empty( $settings[ $slider_key ] ) || ! is_array( $settings[ $slider_key ] ) ) {
			$settings[ $slider_key ] = $defaults[ $slider_key ];
		} elseif ( isset( $settings[ $slider_key ]['number'] ) && ! isset( $settings[ $slider_key ]['size'] ) ) {
			$settings[ $slider_key ]['size'] = (float) $settings[ $slider_key ]['number'];
			$settings[ $slider_key ]['unit'] = $settings[ $slider_key ]['unit'] ?? ( $slider_key === 'donut_inner_radius' ? '%' : 'px' );
		}
	}

	return $settings;
}

function omsar_bd_circular_chart_merge_design_into_settings( $settings, $design ) {
	$design = wp_parse_args( is_array( $design ) ? $design : array(), omsar_bd_circular_chart_default_design_properties() );
	$chart_type_design = $design['chart_type'] ?? array();

	if ( isset( $chart_type_design['donut_inner_radius'] ) ) {
		$settings['donut_inner_radius'] = array(
			'size' => (float) ( $chart_type_design['donut_inner_radius']['number'] ?? 50 ),
			'unit' => $chart_type_design['donut_inner_radius']['unit'] ?? '%',
		);
	}
	if ( isset( $chart_type_design['border_width'] ) ) {
		$settings['border_width'] = array(
			'size' => (int) ( $chart_type_design['border_width']['number'] ?? 2 ),
			'unit' => $chart_type_design['border_width']['unit'] ?? 'px',
		);
	}
	if ( ! empty( $chart_type_design['border_color'] ) ) {
		$settings['border_color'] = $chart_type_design['border_color'];
	}
	if ( ! empty( $design['legend']['text_color'] ) ) {
		$settings['legend_text_color'] = $design['legend']['text_color'];
	}
	if ( ! empty( $design['container']['background_color'] ) ) {
		$settings['container_background'] = $design['container']['background_color'];
	}

	return $settings;
}

function omsar_bd_circular_chart_settings_from_breakdance( $properties_data ) {
	$content = $properties_data['content'] ?? array();
	$chart   = $content['chart'] ?? array();
	$data    = $content['data'] ?? array();
	$legend  = $content['legend'] ?? array();
	$design  = $properties_data['design'] ?? array();

	$settings = array(
		'chart_title'      => isset( $chart['chart_title'] ) ? trim( (string) $chart['chart_title'] ) : '',
		'show_export_icon' => ! empty( $chart['show_export_icon'] ),
		'show_zoom_icon'   => ! empty( $chart['show_zoom_icon'] ),
		'chart_type'       => $chart['chart_type'] ?? 'doughnut',
		'reverse_chart'    => ! empty( $chart['reverse_chart'] ),
		'chart_data'       => $data['chart_data'] ?? array(),
		'show_legend'      => ! isset( $legend['show_legend'] ) || ! empty( $legend['show_legend'] ),
		'legend_position'  => $legend['legend_position'] ?? 'bottom',
		'legend_alignment' => $legend['legend_alignment'] ?? 'center',
	);

	$settings = omsar_bd_normalize_circular_chart_settings( $settings );
	return omsar_bd_circular_chart_merge_design_into_settings( $settings, $design );
}

function omsar_bd_circular_chart_settings_from_elementor( $settings ) {
	return omsar_bd_normalize_circular_chart_settings( $settings );
}

function omsar_bd_render_circular_chart_widget( $settings, $element_id, $args = array() ) {
	$settings = omsar_bd_normalize_circular_chart_settings( $settings );
	$breakdance = ! empty( $args['breakdance'] );
	$chart_title = ! empty( $settings['chart_title'] ) ? esc_html( $settings['chart_title'] ) : '';
		$chart_type = ! empty( $settings['chart_type'] ) ? esc_attr( $settings['chart_type'] ) : 'doughnut';
		$chart_data = ! empty( $settings['chart_data'] ) ? $settings['chart_data'] : [];
		$donut_inner_radius = ! empty( $settings['donut_inner_radius']['size'] ) ? floatval( $settings['donut_inner_radius']['size'] ) : 50;
		$border_width = ! empty( $settings['border_width']['size'] ) ? intval( $settings['border_width']['size'] ) : 2;
		$border_color = ! empty( $settings['border_color'] ) ? esc_attr( $settings['border_color'] ) : '#ffffff';
		$show_legend = ! empty( $settings['show_legend'] ) && $settings['show_legend'] === 'yes';
		$legend_position = ! empty( $settings['legend_position'] ) ? esc_attr( $settings['legend_position'] ) : 'bottom';
		$legend_alignment = ! empty( $settings['legend_alignment'] ) ? esc_attr( $settings['legend_alignment'] ) : 'center';
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';
		$reverse_chart = ! empty( $settings['reverse_chart'] ) && $settings['reverse_chart'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-circular-chart-' . $element_id;
		$canvas_id = $widget_id . '-canvas';

		// Prepare data arrays
		$labels = [];
		$values = [];
		$colors = [];

		foreach ( $chart_data as $item ) {
			$labels[] = ! empty( $item['segment_label'] ) ? esc_html( $item['segment_label'] ) : '';
			$values[] = ! empty( $item['segment_value'] ) ? floatval( $item['segment_value'] ) : 0;
			$colors[] = ! empty( $item['segment_color'] ) ? esc_attr( $item['segment_color'] ) : '#2563eb';
		}

		// Reverse chart data if reverse_chart is enabled
		if ( $reverse_chart ) {
			$labels = array_reverse( $labels );
			$values = array_reverse( $values );
			$colors = array_reverse( $colors );
		}

		// Calculate cutout value
		$cutout_value = 0;
		if ( $chart_type === 'doughnut' ) {
			// Convert percentage to number (Chart.js accepts percentage as string or number in pixels)
			// For percentage string, use format like "50%"
			$cutout_value = $donut_inner_radius . '%';
		}

		// Convert to JSON for JavaScript
		$chart_config = [
			'type' => $chart_type,
			'labels' => $labels,
			'data' => $values,
			'backgroundColor' => $colors,
			'borderColor' => $border_color,
			'borderWidth' => $border_width,
			'cutout' => $cutout_value,
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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-wrapper canvas {
				max-height: 100%;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-container {
				/* Center the chart container inside the wrapper */
				margin-left: auto;
				margin-right: auto;
				/* Center the canvas inside the container */
				display: flex;
				align-items: center;
				justify-content: center;
				box-sizing: border-box;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-container canvas {
				display: block;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-header .omsar-circular-chart-title {
				margin: 0;
				flex: 1;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon {
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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon[data-tooltip]:hover::before {
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
			#<?php echo esc_attr( $widget_id ); ?> .omsar-circular-chart-action-icon[data-tooltip]:hover::after {
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
			.omsar-circular-chart-modal {
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
			.omsar-circular-chart-modal.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-circular-chart-modal-content {
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
			.omsar-circular-chart-modal-close {
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

			html[dir="rtl"] .omsar-circular-chart-modal-close {
				left: 10px;  
				right: auto;  
			}
			.omsar-circular-chart-modal-close:hover {
				background-color: #e5e7eb;
				color: #111827;
			}
			.omsar-circular-chart-modal-close svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			.omsar-circular-chart-modal-title {
				margin: 0 0 20px 0;
				font-size: 20px;
				font-weight: 600;
				flex-shrink: 0;
			}
			.omsar-circular-chart-modal-container {
				width: 100%;
				flex: 1;
				min-height: 0;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.omsar-circular-chart-modal-container canvas {
				max-width: 100%;
				max-height: 100%;
				height: auto !important;
			}
		</style>
		<div class="omsar-circular-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>"<?php echo omsar_bd_chart_wrapper_data_attributes( 'circular', $chart_config, $breakdance ); ?>>
			<?php if ( ! empty( $chart_title ) || $show_export_icon || $show_zoom_icon ) : ?>
				<div class="omsar-circular-chart-header">
					<?php if ( ! empty( $chart_title ) ) : ?>
						<h3 class="omsar-circular-chart-title"><?php echo esc_html( $chart_title ); ?></h3>
					<?php else : ?>
						<div></div>
					<?php endif; ?>
					<?php if ( $show_export_icon || $show_zoom_icon ) : ?>
						<div class="omsar-circular-chart-header-actions">
							<?php if ( $show_export_icon ) : ?>
								<button type="button" class="omsar-circular-chart-action-icon omsar-circular-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<?php endif; ?>
							<?php if ( $show_zoom_icon ) : ?>
								<button type="button" class="omsar-circular-chart-action-icon omsar-circular-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-canvas-id="<?php echo esc_attr( $canvas_id ); ?>" data-chart-title="<?php echo esc_attr( $chart_title ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="omsar-circular-chart-container">
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
					type: config.type,
					data: {
						labels: config.labels,
						datasets: [{
							data: config.data,
							backgroundColor: config.backgroundColor,
							borderColor: config.borderColor,
							borderWidth: config.borderWidth
						}]
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
										var value = context.parsed || 0;
										var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
										var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
										return value + ' (' + percentage + '%)';
									}
								}
							}
						},
						cutout: config.cutout || 0
					}
				});

				// Store chart instance for export functionality
				var chartInstance = chart;

				// Export image functionality
				var exportIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-circular-chart-export-icon');
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
				var zoomIcon = document.querySelector('#<?php echo esc_js( $widget_id ); ?> .omsar-circular-chart-zoom-icon');
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
						var modalId = 'omsar-circular-chart-modal-' + widgetId;
						var existingModal = document.getElementById(modalId);
						if (existingModal) {
							existingModal.remove();
						}

						var modal = document.createElement('div');
						modal.id = modalId;
						modal.className = 'omsar-circular-chart-modal active';
						modal.setAttribute('role', 'dialog');
						modal.setAttribute('aria-modal', 'true');
						modal.setAttribute('aria-labelledby', modalId + '-title');

						var modalContent = document.createElement('div');
						modalContent.className = 'omsar-circular-chart-modal-content container';

						var closeButton = document.createElement('button');
						closeButton.type = 'button';
						closeButton.className = 'omsar-circular-chart-modal-close';
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
							modalTitle.className = 'omsar-circular-chart-modal-title';
							modalTitle.textContent = chartTitle;
							modalContent.appendChild(modalTitle);
						}

						var modalContainer = document.createElement('div');
						modalContainer.className = 'omsar-circular-chart-modal-container';

						// Create new canvas for modal
						var clonedCanvas = document.createElement('canvas');
						modalContainer.appendChild(clonedCanvas);

						// Recreate chart in modal with same data and configuration
						setTimeout(function() {
							var clonedCtx = clonedCanvas.getContext('2d');
							var clonedChart = new Chart(clonedCtx, {
								type: config.type,
								data: {
									labels: config.labels,
									datasets: [{
										data: JSON.parse(JSON.stringify(config.data)),
										backgroundColor: JSON.parse(JSON.stringify(config.backgroundColor)),
										borderColor: config.borderColor,
										borderWidth: config.borderWidth
									}]
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
													var value = context.parsed || 0;
													var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
													var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
													return value + ' (' + percentage + '%)';
												}
											}
										}
									},
									cutout: config.cutout || 0
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
