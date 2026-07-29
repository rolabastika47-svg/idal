<?php
/**
 * Elementor Circular Chart Widget Class
 * 
 * Displays a pie or donut chart with configurable data, colors, and title
 * 
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if Elementor is installed and active
 */
if ( ! did_action( 'elementor/loaded' ) ) {
	return;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class OMSAR_Circular_Chart_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_circular_chart';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Circular Chart', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-pie-chart';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'omsar-elements' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'chart', 'pie chart', 'donut chart', 'circular chart', 'graph', 'data visualization', 'statistics' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Chart Content', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'chart_title',
			[
				'label' => esc_html__( 'Chart Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Chart title goes here', 'omsar' ),
				'placeholder' => esc_html__( 'Enter chart title', 'omsar' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_export_icon',
			[
				'label' => esc_html__( 'Show Export Image Icon', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display an icon to download the chart as an image', 'omsar' ),
			]
		);

		$this->add_control(
			'show_zoom_icon',
			[
				'label' => esc_html__( 'Show Zoom/Expand Icon', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display an icon to open the chart in full view/zoom mode', 'omsar' ),
			]
		);

		$this->add_control(
			'chart_type',
			[
				'label' => esc_html__( 'Chart Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'doughnut',
				'options' => [
					'pie' => esc_html__( 'Pie Chart', 'omsar' ),
					'doughnut' => esc_html__( 'Donut Chart', 'omsar' ),
				],
				'description' => esc_html__( 'Choose between pie or donut chart style', 'omsar' ),
			]
		);

		$this->add_control(
			'reverse_chart',
			[
				'label' => esc_html__( 'Reverse Chart Display', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Enable this for Arabic/RTL layouts. This will reverse the order of segments and legends.', 'omsar' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'segment_label',
			[
				'label' => esc_html__( 'Segment Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Segment', 'omsar' ),
				'placeholder' => esc_html__( 'Enter segment label', 'omsar' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'segment_value',
			[
				'label' => esc_html__( 'Segment Value', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 25,
				'min' => 0,
				'step' => 0.1,
				'description' => esc_html__( 'Numeric value for this segment', 'omsar' ),
			]
		);

		$repeater->add_control(
			'segment_color',
			[
				'label' => esc_html__( 'Segment Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2563eb',
				'description' => esc_html__( 'Color for this segment', 'omsar' ),
			]
		);

		$this->add_control(
			'chart_data',
			[
				'label' => esc_html__( 'Chart Segments', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'segment_label' => esc_html__( 'Segment 1', 'omsar' ),
						'segment_value' => 40,
						'segment_color' => '#2563eb',
					],
					[
						'segment_label' => esc_html__( 'Segment 2', 'omsar' ),
						'segment_value' => 25,
						'segment_color' => '#60a5fa',
					],
					[
						'segment_label' => esc_html__( 'Segment 3', 'omsar' ),
						'segment_value' => 20,
						'segment_color' => '#93c5fd',
					],
					[
						'segment_label' => esc_html__( 'Segment 4', 'omsar' ),
						'segment_value' => 15,
						'segment_color' => '#bfdbfe',
					],
				],
				'title_field' => '{{{ segment_label }}}',
			]
		);

		$this->end_controls_section();

		// Legend Section
		$this->start_controls_section(
			'legend_section',
			[
				'label' => esc_html__( 'Legend', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_legend',
			[
				'label' => esc_html__( 'Show Legend', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display a legend showing all segments with their names and colors', 'omsar' ),
			]
		);

		$this->add_control(
			'legend_position',
			[
				'label' => esc_html__( 'Legend Position', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'top' => esc_html__( 'Top', 'omsar' ),
					'bottom' => esc_html__( 'Bottom', 'omsar' ),
					'left' => esc_html__( 'Left', 'omsar' ),
					'right' => esc_html__( 'Right', 'omsar' ),
				],
				'condition' => [
					'show_legend' => 'yes',
				],
			]
		);

		$this->add_control(
			'legend_alignment',
			[
				'label' => esc_html__( 'Legend Alignment', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'start' => esc_html__( 'Start', 'omsar' ),
					'center' => esc_html__( 'Center', 'omsar' ),
					'end' => esc_html__( 'End', 'omsar' ),
				],
				'condition' => [
					'show_legend' => 'yes',
				],
				'description' => esc_html__( 'Alignment of legend items (only applies to top/bottom positions)', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Chart Type Options
		$this->start_controls_section(
			'chart_type_section',
			[
				'label' => esc_html__( 'Chart Type Options', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'donut_inner_radius',
			[
				'label' => esc_html__( 'Donut Inner Radius (%)', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 90,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'condition' => [
					'chart_type' => 'doughnut',
				],
				'description' => esc_html__( 'Inner radius percentage for donut chart (0% = pie chart, higher = thinner ring)', 'omsar' ),
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => esc_html__( 'Segment Border Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'description' => esc_html__( 'Width of border between segments', 'omsar' ),
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Segment Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'description' => esc_html__( 'Color of border between segments', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Title
		$this->start_controls_section(
			'style_title_section',
			[
				'label' => esc_html__( 'Title Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-circular-chart-title',
				'default' => [
					'font_weight' => '600',
					'font_size' => [
						'size' => '20',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'omsar' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'omsar' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'omsar' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'omsar' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin Bottom', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Legend
		$this->start_controls_section(
			'style_legend_section',
			[
				'label' => esc_html__( 'Legend Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_legend' => 'yes',
				],
			]
		);

		$this->add_control(
			'legend_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Chart Container
		$this->start_controls_section(
			'style_chart_section',
			[
				'label' => esc_html__( 'Chart Container', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'chart_size',
			[
				'label' => esc_html__( 'Chart Size', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 800,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 350,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 250,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-container' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'description' => esc_html__( 'Set the width and height of the chart. You can set different sizes for desktop, tablet, and mobile.', 'omsar' ),
			]
		);

		$this->add_control(
			'container_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'right' => '20',
					'bottom' => '20',
					'left' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'container_background',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-circular-chart-wrapper',
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-circular-chart-wrapper',
			]
		);

		$this->end_controls_section();

		// Advanced Section
		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__( 'Advanced', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'chart_alignment',
			[
				'label' => esc_html__( 'Chart Alignment', 'omsar' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'omsar' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'omsar' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'omsar' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'chart_width',
			[
				'label' => esc_html__( 'Container Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 10,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-circular-chart-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		if ( function_exists( 'omsar_bd_render_circular_chart_widget' ) ) {
			omsar_bd_render_circular_chart_widget(
				omsar_bd_circular_chart_settings_from_elementor( $this->get_settings_for_display() ),
				(string) $this->get_id()
			);
			return;
		}

		$settings = $this->get_settings_for_display();

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
		$widget_id = 'omsar-circular-chart-' . $this->get_id();
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
		<div class="omsar-circular-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
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
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var chartTitle = settings.chart_title || '';
		var chartType = settings.chart_type || 'doughnut';
		var chartData = settings.chart_data || [];
		var donutInnerRadius = settings.donut_inner_radius && settings.donut_inner_radius.size ? parseFloat(settings.donut_inner_radius.size) : 50;
		var borderWidth = settings.border_width && settings.border_width.size ? parseInt(settings.border_width.size) : 2;
		var borderColor = settings.border_color || '#ffffff';
		var showLegend = settings.show_legend === 'yes';
		var legendPosition = settings.legend_position || 'bottom';
		var legendAlignment = settings.legend_alignment || 'center';
		var legendTextColor = settings.legend_text_color || '#333333';
		var showExportIcon = settings.show_export_icon === 'yes';
		var showZoomIcon = settings.show_zoom_icon === 'yes';
		var reverseChart = settings.reverse_chart === 'yes';
		var widgetId = 'omsar-circular-chart-' + view.getIDInt();
		var canvasId = widgetId + '-canvas';

		var labels = [];
		var values = [];
		var colors = [];

		_.each(chartData, function(item) {
			labels.push(item.segment_label || '');
			values.push(parseFloat(item.segment_value) || 0);
			colors.push(item.segment_color || '#2563eb');
		});

		// Reverse chart data if reverse_chart is enabled
		if (reverseChart) {
			labels = labels.reverse();
			values = values.reverse();
			colors = colors.reverse();
		}

		var cutoutValue = 0;
		if (chartType === 'doughnut') {
			cutoutValue = donutInnerRadius + '%';
		}

		var containerBackground = settings.container_background || '#ffffff';
		
		var chartConfig = {
			type: chartType,
			labels: labels,
			data: values,
			backgroundColor: colors,
			borderColor: borderColor,
			borderWidth: borderWidth,
			cutout: cutoutValue,
			showLegend: showLegend,
			legendPosition: legendPosition,
			legendAlignment: legendAlignment,
			legendTextColor: legendTextColor,
			containerBackground: containerBackground,
			reverseChart: reverseChart
		};
		
		#>
		<style>
			#{{ widgetId }} .omsar-circular-chart-wrapper canvas {
				max-height: 100%;
			}
			#{{ widgetId }} .omsar-circular-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#{{ widgetId }} .omsar-circular-chart-header .omsar-circular-chart-title {
				margin: 0;
				flex: 1;
			}
			#{{ widgetId }} .omsar-circular-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#{{ widgetId }} .omsar-circular-chart-action-icon {
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
			#{{ widgetId }} .omsar-circular-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#{{ widgetId }} .omsar-circular-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#{{ widgetId }} .omsar-circular-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#{{ widgetId }} .omsar-circular-chart-action-icon[data-tooltip]:hover::before {
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
			#{{ widgetId }} .omsar-circular-chart-action-icon[data-tooltip]:hover::after {
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
		<div class="omsar-circular-chart-wrapper" id="{{ widgetId }}">
			<# if (chartTitle || showExportIcon || showZoomIcon) { #>
				<div class="omsar-circular-chart-header">
					<# if (chartTitle) { #>
						<h3 class="omsar-circular-chart-title">{{{ chartTitle }}}</h3>
					<# } else { #>
						<div></div>
					<# } #>
					<# if (showExportIcon || showZoomIcon) { #>
						<div class="omsar-circular-chart-header-actions">
							<# if (showExportIcon) { #>
								<button type="button" class="omsar-circular-chart-action-icon omsar-circular-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<# } #>
							<# if (showZoomIcon) { #>
								<button type="button" class="omsar-circular-chart-action-icon omsar-circular-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="{{{ widgetId }}}" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<# } #>
						</div>
					<# } #>
				</div>
			<# } #>
			<div class="omsar-circular-chart-container">
				<canvas id="{{{ canvasId }}}"></canvas>
			</div>
		</div>

		<script type="text/javascript">
		(function() {
			'use strict';
			
			var initChart = function() {
				if (typeof Chart === 'undefined') {
					setTimeout(initChart, 100);
					return;
				}

				var canvas = document.getElementById('{{{ canvasId }}}');
				if (!canvas) {
					return;
				}

				var ctx = canvas.getContext('2d');
				var config = <# print(JSON.stringify(chartConfig)); #>;

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
				var exportIcon = document.querySelector('#{{ widgetId }} .omsar-circular-chart-export-icon');
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
				var zoomIcon = document.querySelector('#{{ widgetId }} .omsar-circular-chart-zoom-icon');
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
		<?php
	}
}

