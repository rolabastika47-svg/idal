<?php
/**
 * Elementor Chart Widget Class
 * 
 * Displays a bar chart with configurable data, colors, and title
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

class OMSAR_Chart_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_chart';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Bar Chart', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-bar-chart';
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
		return [ 'chart', 'bar chart', 'graph', 'data visualization', 'statistics' ];
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
				'default' => esc_html__( 'Figure 4.1 - Responsibilities of the Coordinating Office', 'omsar' ),
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
			'y_axis_label',
			[
				'label' => esc_html__( 'Y-Axis Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '%', 'omsar' ),
				'placeholder' => esc_html__( 'Enter Y-axis label (e.g., %, $, kg)', 'omsar' ),
				'description' => esc_html__( 'This label will be automatically appended to all Y-axis values (e.g., 100%, 80%, 60%)', 'omsar' ),
			]
		);

		$this->add_control(
			'show_y_axis_label_title',
			[
				'label' => esc_html__( 'Show Y-Axis Label Title', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'When enabled, the Y-axis label title is displayed. When disabled, the label is hidden but still appended to Y-axis values.', 'omsar' ),
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
				'description' => esc_html__( 'Enable this for Arabic/RTL layouts. This will move the Y-axis to the right side and reverse the order of categories and legends.', 'omsar' ),
			]
		);

		$y_axis_repeater = new Repeater();

		$y_axis_repeater->add_control(
			'y_axis_value',
			[
				'label' => esc_html__( 'Y-Axis Value', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '0',
				'placeholder' => esc_html__( 'Enter Y-axis value (e.g., 0, 20, 40, 60, 80, 100)', 'omsar' ),
				'label_block' => true,
				'description' => esc_html__( 'Can be numbers or text labels', 'omsar' ),
			]
		);

		$this->add_control(
			'y_axis_values',
			[
				'label' => esc_html__( 'Y-Axis Values', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $y_axis_repeater->get_controls(),
				'default' => [
					[ 'y_axis_value' => '0' ],
					[ 'y_axis_value' => '20' ],
					[ 'y_axis_value' => '40' ],
					[ 'y_axis_value' => '60' ],
					[ 'y_axis_value' => '80' ],
					[ 'y_axis_value' => '100' ],
				],
				'title_field' => '{{{ y_axis_value }}}',
				'description' => esc_html__( 'Define the Y-axis scale values. Can be numbers or text labels.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Bars Configuration Section
		$this->start_controls_section(
			'bars_config_section',
			[
				'label' => esc_html__( 'Bars Configuration', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$bars_repeater = new Repeater();

		$bars_repeater->add_control(
			'bar_name',
			[
				'label' => esc_html__( 'Bar Name', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Bar', 'omsar' ),
				'placeholder' => esc_html__( 'Enter bar name', 'omsar' ),
				'label_block' => true,
				'description' => esc_html__( 'Name for this bar (used in tooltips and legend)', 'omsar' ),
			]
		);

		$bars_repeater->add_control(
			'bar_color',
			[
				'label' => esc_html__( 'Bar Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2563eb',
				'description' => esc_html__( 'Color for this bar', 'omsar' ),
			]
		);

		$this->add_control(
			'bars_config',
			[
				'label' => esc_html__( 'Bars', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $bars_repeater->get_controls(),
				'default' => [
					[
						'bar_name' => esc_html__( 'Bar 1', 'omsar' ),
						'bar_color' => '#2563eb',
					],
					[
						'bar_name' => esc_html__( 'Bar 2', 'omsar' ),
						'bar_color' => '#60a5fa',
					],
				],
				'title_field' => '{{{ bar_name }}}',
				'description' => esc_html__( 'Configure the bars for your chart. Add as many bars as needed.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Chart Data Section
		$this->start_controls_section(
			'chart_data_section',
			[
				'label' => esc_html__( 'Chart Data', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'category_label',
			[
				'label' => esc_html__( 'Category Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Category A', 'omsar' ),
				'placeholder' => esc_html__( 'Enter category label', 'omsar' ),
				'label_block' => true,
			]
		);

		// Add a repeater for bar values
		$bar_values_repeater = new Repeater();
		$bar_values_repeater->add_control(
			'bar_value',
			[
				'label' => esc_html__( 'Value', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'step' => 1,
				'description' => esc_html__( 'Value for this bar in this category', 'omsar' ),
			]
		);

		$repeater->add_control(
			'bar_values',
			[
				'label' => esc_html__( 'Bar Values', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $bar_values_repeater->get_controls(),
				'default' => [
					[ 'bar_value' => 84 ],
					[ 'bar_value' => 64 ],
				],
				'title_field' => esc_html__( 'Value: {{{ bar_value }}}', 'omsar' ),
				'description' => esc_html__( 'Add one value for each bar configured above. The order should match the bars configuration.', 'omsar' ),
			]
		);

		// Keep backward compatibility with value_1 and value_2
		$repeater->add_control(
			'value_1',
			[
				'label' => esc_html__( 'Bar 1 Value (Legacy)', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 84,
				'min' => 0,
				'step' => 1,
				'description' => esc_html__( 'Legacy field - use Bar Values repeater above instead', 'omsar' ),
			]
		);

		$repeater->add_control(
			'value_2',
			[
				'label' => esc_html__( 'Bar 2 Value (Legacy)', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 64,
				'min' => 0,
				'step' => 1,
				'description' => esc_html__( 'Legacy field - use Bar Values repeater above instead', 'omsar' ),
			]
		);

		$this->add_control(
			'chart_data',
			[
				'label' => esc_html__( 'Chart Data', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'category_label' => esc_html__( 'Category A', 'omsar' ),
						'value_1' => 84,
						'value_2' => 64,
					],
					[
						'category_label' => esc_html__( 'Category B', 'omsar' ),
						'value_1' => 72,
						'value_2' => 58,
					],
					[
						'category_label' => esc_html__( 'Category C', 'omsar' ),
						'value_1' => 90,
						'value_2' => 74,
					],
					[
						'category_label' => esc_html__( 'Category D', 'omsar' ),
						'value_1' => 68,
						'value_2' => 52,
					],
					[
						'category_label' => esc_html__( 'Category E', 'omsar' ),
						'value_1' => 78,
						'value_2' => 62,
					],
					[
						'category_label' => esc_html__( 'Category F', 'omsar' ),
						'value_1' => 82,
						'value_2' => 70,
					],
				],
				'title_field' => '{{{ category_label }}}',
			]
		);

		$this->end_controls_section();

		// Bar Style Section
		$this->start_controls_section(
			'bar_style_section',
			[
				'label' => esc_html__( 'Bar Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'bar_border_radius',
			[
				'label' => esc_html__( 'Bar Border Radius', 'omsar' ),
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
					'size' => 0,
				],
				'description' => esc_html__( 'Set the border radius (rounding) for the bars. 0 = square bars, higher values = more rounded.', 'omsar' ),
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
				'description' => esc_html__( 'Display a legend showing all bar groups with their names and colors', 'omsar' ),
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
					'{{WRAPPER}} .omsar-chart-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-chart-title',
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
					'{{WRAPPER}} .omsar-chart-title' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-chart-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-chart-legend' => 'color: {{VALUE}};',
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

		$this->add_control(
			'chart_height',
			[
				'label' => esc_html__( 'Chart Height', 'omsar' ),
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
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-chart-container' => 'height: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .omsar-chart-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-chart-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-chart-wrapper',
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-chart-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-chart-wrapper',
			]
		);

		$this->end_controls_section();

		// Advanced Section - Position/Placement
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
					'{{WRAPPER}} .omsar-chart-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'chart_width',
			[
				'label' => esc_html__( 'Chart Width', 'omsar' ),
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
					'{{WRAPPER}} .omsar-chart-wrapper' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-chart-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		if ( function_exists( 'omsar_bd_render_bar_chart_widget' ) ) {
			omsar_bd_render_bar_chart_widget(
				omsar_bd_bar_chart_settings_from_elementor( $this->get_settings_for_display() ),
				(string) $this->get_id()
			);
			return;
		}

		$settings = $this->get_settings_for_display();

		$chart_title = ! empty( $settings['chart_title'] ) ? esc_html( $settings['chart_title'] ) : '';
		$y_axis_label = ! empty( $settings['y_axis_label'] ) ? esc_html( $settings['y_axis_label'] ) : '';
		$show_y_axis_label_title = ! empty( $settings['show_y_axis_label_title'] ) && $settings['show_y_axis_label_title'] === 'yes';
		$bar_border_radius = ! empty( $settings['bar_border_radius']['size'] ) ? intval( $settings['bar_border_radius']['size'] ) : 0;
		$chart_data = ! empty( $settings['chart_data'] ) ? $settings['chart_data'] : [];
		$y_axis_values = ! empty( $settings['y_axis_values'] ) ? $settings['y_axis_values'] : [];
		$bars_config = ! empty( $settings['bars_config'] ) ? $settings['bars_config'] : [];
		$show_legend = ! empty( $settings['show_legend'] ) && $settings['show_legend'] === 'yes';
		$legend_position = ! empty( $settings['legend_position'] ) ? esc_attr( $settings['legend_position'] ) : 'top';
		$legend_alignment = ! empty( $settings['legend_alignment'] ) ? esc_attr( $settings['legend_alignment'] ) : 'center';
		$reverse_chart = ! empty( $settings['reverse_chart'] ) && $settings['reverse_chart'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-chart-' . $this->get_id();
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
		<div class="omsar-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
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
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var chartTitle = settings.chart_title || '';
		var yAxisLabel = settings.y_axis_label || '';
		var showYAxisLabelTitle = settings.show_y_axis_label_title === 'yes';
		var barBorderRadius = settings.bar_border_radius && settings.bar_border_radius.size ? parseInt(settings.bar_border_radius.size) : 0;
		var chartData = settings.chart_data || [];
		var yAxisValues = settings.y_axis_values || [];
		var barsConfig = settings.bars_config || [];
		var showLegend = settings.show_legend === 'yes';
		var legendPosition = settings.legend_position || 'top';
		var legendAlignment = settings.legend_alignment || 'center';
		var legendTextColor = settings.legend_text_color || '#333333';
		var showExportIcon = settings.show_export_icon === 'yes';
		var showZoomIcon = settings.show_zoom_icon === 'yes';
		var reverseChart = settings.reverse_chart === 'yes';
		var widgetId = 'omsar-chart-' + view.getIDInt();
		var canvasId = widgetId + '-canvas';

		// Prepare bars configuration
		var bars = [];
		_.each(barsConfig, function(barConfig) {
			bars.push({
				name: barConfig.bar_name || 'Bar',
				color: barConfig.bar_color || '#2563eb'
			});
		});

		// If no bars configured, use default 2 bars for backward compatibility
		if (bars.length === 0) {
			bars = [
				{
					name: 'Bar 1',
					color: settings.bar_color_1 || '#2563eb'
				},
				{
					name: 'Bar 2',
					color: settings.bar_color_2 || '#60a5fa'
				}
			];
		}

		var labels = [];
		var datasetsData = [];

		// Initialize datasets arrays
		for (var i = 0; i < bars.length; i++) {
			datasetsData[i] = [];
		}

		_.each(chartData, function(item) {
			labels.push(item.category_label || '');
			
			// Check if using new bar_values repeater
			if (item.bar_values && item.bar_values.length > 0) {
				// Use new bar_values repeater
				for (var barIndex = 0; barIndex < bars.length; barIndex++) {
					var barValue = 0;
					if (item.bar_values[barIndex] && item.bar_values[barIndex].bar_value !== undefined) {
						barValue = parseFloat(item.bar_values[barIndex].bar_value) || 0;
					}
					datasetsData[barIndex].push(barValue);
				}
			} else {
				// Use legacy value_1 and value_2 for backward compatibility
				datasetsData[0].push(parseFloat(item.value_1) || 0);
				if (datasetsData[1]) {
					datasetsData[1].push(parseFloat(item.value_2) || 0);
				}
			}
		});

		// Prepare Y-axis values
		var yAxisLabels = [];
		var yAxisNumericValues = [];
		
		_.each(yAxisValues, function(yItem) {
			var yValue = (yItem.y_axis_value || '').trim();
			if (yValue !== '') {
				yAxisLabels.push(yValue);
				// Try to convert to number for positioning, if it's numeric
				var numericValue = !isNaN(yValue) && yValue !== '' ? parseFloat(yValue) : null;
				yAxisNumericValues.push(numericValue);
			}
		});

		// If no Y-axis values provided, use default numeric scale
		if (yAxisLabels.length === 0) {
			yAxisLabels = ['0', '20', '40', '60', '80', '100'];
			yAxisNumericValues = [0, 20, 40, 60, 80, 100];
		}

		// Build datasets from bars configuration
		var datasets = [];
		_.each(bars, function(bar, index) {
			datasets.push({
				label: bar.name,
				data: datasetsData[index] || [],
				backgroundColor: bar.color,
				borderColor: bar.color,
					borderWidth: 0,
					borderRadius: barBorderRadius
			});
		});

		// Reverse chart data if reverse_chart is enabled
		if (reverseChart) {
			labels = labels.reverse();
			datasets = datasets.reverse();
			// Reverse data within each dataset
			_.each(datasets, function(dataset) {
				dataset.data = dataset.data.reverse();
			});
		}

		var containerBackground = settings.container_background || '#ffffff';
		
		var chartConfig = {
			labels: labels,
			datasets: datasets,
			yAxisLabels: yAxisLabels,
			yAxisNumericValues: yAxisNumericValues,
			yAxisLabel: yAxisLabel,
			showYAxisLabelTitle: showYAxisLabelTitle,
			showLegend: showLegend,
			legendPosition: legendPosition,
			legendAlignment: legendAlignment,
			legendTextColor: legendTextColor,
			containerBackground: containerBackground,
			reverseChart: reverseChart
		};
		
		#>
		<style>
			#{{ widgetId }} .omsar-chart-wrapper canvas {
				max-height: 100%;
			}
			#{{ widgetId }} .omsar-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#{{ widgetId }} .omsar-chart-header .omsar-chart-title {
				margin: 0;
				flex: 1;
			}
			#{{ widgetId }} .omsar-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#{{ widgetId }} .omsar-chart-action-icon {
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
			#{{ widgetId }} .omsar-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#{{ widgetId }} .omsar-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#{{ widgetId }} .omsar-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#{{ widgetId }} .omsar-chart-action-icon[data-tooltip]:hover::before {
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
			#{{ widgetId }} .omsar-chart-action-icon[data-tooltip]:hover::after {
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
			}
		</style>
		<div class="omsar-chart-wrapper" id="{{ widgetId }}">
			<# if (chartTitle || showExportIcon || showZoomIcon) { #>
				<div class="omsar-chart-header">
			<# if (chartTitle) { #>
				<h3 class="omsar-chart-title">{{{ chartTitle }}}</h3>
					<# } else { #>
						<div></div>
					<# } #>
					<# if (showExportIcon || showZoomIcon) { #>
						<div class="omsar-chart-header-actions">
							<# if (showExportIcon) { #>
								<button type="button" class="omsar-chart-action-icon omsar-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<# } #>
							<# if (showZoomIcon) { #>
								<button type="button" class="omsar-chart-action-icon omsar-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="{{{ widgetId }}}" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<# } #>
						</div>
					<# } #>
				</div>
			<# } #>
			<div class="omsar-chart-container">
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
										var label = context.dataset.label + ': ' + context.parsed.y;
										if (config.yAxisLabel) {
											label += ' ' + config.yAxisLabel.replace(/[()]/g, '');
										}
										return label;
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
				var exportIcon = document.querySelector('#{{ widgetId }} .omsar-chart-export-icon');
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
				var zoomIcon = document.querySelector('#{{ widgetId }} .omsar-chart-zoom-icon');
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
		<?php
	}
}

