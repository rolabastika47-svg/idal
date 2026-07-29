<?php
/**
 * Elementor Line Chart Widget Class
 * 
 * Displays a line chart with configurable data, colors, and title
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

class OMSAR_Line_Chart_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_line_chart';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Line Chart', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-line-chart';
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
		return [ 'chart', 'line chart', 'graph', 'data visualization', 'statistics', 'trend' ];
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
			'y_axis_label',
			[
				'label' => esc_html__( 'Y-Axis Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Y-axis label (optional)', 'omsar' ),
				'description' => esc_html__( 'Optional label for the Y-axis', 'omsar' ),
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
				'description' => esc_html__( 'Enable this for Arabic/RTL layouts. This will move the Y-axis to the right side and reverse the order of categories.', 'omsar' ),
			]
		);

		$y_axis_repeater = new Repeater();

		$y_axis_repeater->add_control(
			'y_axis_value',
			[
				'label' => esc_html__( 'Y-Axis Value', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '0',
				'placeholder' => esc_html__( 'Enter Y-axis value (e.g., 0, 25, 50, 75, 100)', 'omsar' ),
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
					[ 'y_axis_value' => '25' ],
					[ 'y_axis_value' => '50' ],
					[ 'y_axis_value' => '75' ],
					[ 'y_axis_value' => '100' ],
				],
				'title_field' => '{{{ y_axis_value }}}',
				'description' => esc_html__( 'Define the Y-axis scale values. Can be numbers or text labels.', 'omsar' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'x_axis_label',
			[
				'label' => esc_html__( 'X-Axis Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Jan', 'omsar' ),
				'placeholder' => esc_html__( 'Enter X-axis label', 'omsar' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'data_value',
			[
				'label' => esc_html__( 'Data Value', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'step' => 0.1,
				'description' => esc_html__( 'Numeric value for this data point', 'omsar' ),
			]
		);

		$this->add_control(
			'chart_data',
			[
				'label' => esc_html__( 'Chart Data Points', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'x_axis_label' => esc_html__( 'Jan', 'omsar' ),
						'data_value' => 28,
					],
					[
						'x_axis_label' => esc_html__( 'Feb', 'omsar' ),
						'data_value' => 45,
					],
					[
						'x_axis_label' => esc_html__( 'Mar', 'omsar' ),
						'data_value' => 38,
					],
					[
						'x_axis_label' => esc_html__( 'Apr', 'omsar' ),
						'data_value' => 55,
					],
					[
						'x_axis_label' => esc_html__( 'May', 'omsar' ),
						'data_value' => 68,
					],
					[
						'x_axis_label' => esc_html__( 'Jun', 'omsar' ),
						'data_value' => 75,
					],
					[
						'x_axis_label' => esc_html__( 'Jul', 'omsar' ),
						'data_value' => 80,
					],
				],
				'title_field' => '{{{ x_axis_label }}}',
			]
		);

		$this->add_control(
			'line_label',
			[
				'label' => esc_html__( 'Line Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Data', 'omsar' ),
				'placeholder' => esc_html__( 'Enter line label', 'omsar' ),
				'description' => esc_html__( 'Label for the line (used in legend and tooltips)', 'omsar' ),
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
				'description' => esc_html__( 'Display a legend showing the line with its name and color', 'omsar' ),
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

		// Line Style Section
		$this->start_controls_section(
			'line_style_section',
			[
				'label' => esc_html__( 'Line Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'line_color',
			[
				'label' => esc_html__( 'Line Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2563eb',
				'description' => esc_html__( 'Color of the line', 'omsar' ),
			]
		);

		$this->add_control(
			'line_width',
			[
				'label' => esc_html__( 'Line Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'description' => esc_html__( 'Width of the line', 'omsar' ),
			]
		);

		$this->add_control(
			'show_points',
			[
				'label' => esc_html__( 'Show Data Points', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display circular points on the line chart', 'omsar' ),
			]
		);

		$this->add_control(
			'point_radius',
			[
				'label' => esc_html__( 'Point Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'condition' => [
					'show_points' => 'yes',
				],
				'description' => esc_html__( 'Radius of data points', 'omsar' ),
			]
		);

		$this->add_control(
			'point_color',
			[
				'label' => esc_html__( 'Point Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2563eb',
				'condition' => [
					'show_points' => 'yes',
				],
				'description' => esc_html__( 'Color of data points', 'omsar' ),
			]
		);

		$this->add_control(
			'fill_area',
			[
				'label' => esc_html__( 'Fill Area Under Line', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(37, 99, 235, 0.1)',
				'condition' => [
					'fill_area' => 'yes',
				],
				'description' => esc_html__( 'Color for area fill under the line', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Grid Style Section
		$this->start_controls_section(
			'grid_style_section',
			[
				'label' => esc_html__( 'Grid Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_grid',
			[
				'label' => esc_html__( 'Show Grid Lines', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'grid_color',
			[
				'label' => esc_html__( 'Grid Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.1)',
				'condition' => [
					'show_grid' => 'yes',
				],
				'description' => esc_html__( 'Color of grid lines', 'omsar' ),
			]
		);

		$this->add_control(
			'grid_line_width',
			[
				'label' => esc_html__( 'Grid Line Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'condition' => [
					'show_grid' => 'yes',
				],
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
					'{{WRAPPER}} .omsar-line-chart-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-line-chart-title',
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
					'{{WRAPPER}} .omsar-line-chart-title' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-line-chart-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-line-chart-legend' => 'color: {{VALUE}};',
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
				'tablet_default' => [
					'unit' => 'px',
					'size' => 350,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-line-chart-container' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-line-chart-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-line-chart-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-line-chart-wrapper',
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
					'{{WRAPPER}} .omsar-line-chart-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-line-chart-wrapper',
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
					'{{WRAPPER}} .omsar-line-chart-wrapper' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-line-chart-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		if ( function_exists( 'omsar_bd_render_line_chart_widget' ) ) {
			omsar_bd_render_line_chart_widget(
				omsar_bd_line_chart_settings_from_elementor( $this->get_settings_for_display() ),
				(string) $this->get_id()
			);
			return;
		}

		$settings = $this->get_settings_for_display();

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
		$legend_position = ! empty( $settings['legend_position'] ) ? esc_attr( $settings['legend_position'] ) : 'top';
		$legend_alignment = ! empty( $settings['legend_alignment'] ) ? esc_attr( $settings['legend_alignment'] ) : 'center';
		$show_export_icon = ! empty( $settings['show_export_icon'] ) && $settings['show_export_icon'] === 'yes';
		$show_zoom_icon = ! empty( $settings['show_zoom_icon'] ) && $settings['show_zoom_icon'] === 'yes';
		$reverse_chart = ! empty( $settings['reverse_chart'] ) && $settings['reverse_chart'] === 'yes';

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-line-chart-' . $this->get_id();
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
		<div class="omsar-line-chart-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
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
		var lineLabel = settings.line_label || 'Data';
		var chartData = settings.chart_data || [];
		var yAxisValues = settings.y_axis_values || [];
		var lineColor = settings.line_color || '#2563eb';
		var lineWidth = settings.line_width && settings.line_width.size ? parseInt(settings.line_width.size) : 2;
		var showPoints = settings.show_points === 'yes';
		var pointRadius = showPoints && settings.point_radius && settings.point_radius.size ? parseInt(settings.point_radius.size) : 0;
		var pointColor = settings.point_color || '#2563eb';
		var fillArea = settings.fill_area === 'yes';
		var fillColor = settings.fill_color || 'rgba(37, 99, 235, 0.1)';
		var showGrid = settings.show_grid === 'yes';
		var gridColor = settings.grid_color || 'rgba(0, 0, 0, 0.1)';
		var gridLineWidth = settings.grid_line_width && settings.grid_line_width.size ? parseFloat(settings.grid_line_width.size) : 1;
		var showLegend = settings.show_legend === 'yes';
		var legendPosition = settings.legend_position || 'top';
		var legendAlignment = settings.legend_alignment || 'center';
		var legendTextColor = settings.legend_text_color || '#333333';
		var showExportIcon = settings.show_export_icon === 'yes';
		var showZoomIcon = settings.show_zoom_icon === 'yes';
		var reverseChart = settings.reverse_chart === 'yes';
		var widgetId = 'omsar-line-chart-' + view.getIDInt();
		var canvasId = widgetId + '-canvas';

		var labels = [];
		var values = [];

		_.each(chartData, function(item) {
			labels.push(item.x_axis_label || '');
			values.push(parseFloat(item.data_value) || 0);
		});

		// Reverse chart data if reverse_chart is enabled
		if (reverseChart) {
			labels = labels.reverse();
			values = values.reverse();
		}

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
			yAxisLabels = ['0', '25', '50', '75', '100'];
			yAxisNumericValues = [0, 25, 50, 75, 100];
		}

		var containerBackground = settings.container_background || '#ffffff';
		
		var chartConfig = {
			labels: labels,
			data: values,
			lineLabel: lineLabel,
			lineColor: lineColor,
			lineWidth: lineWidth,
			pointRadius: pointRadius,
			pointColor: pointColor,
			fillArea: fillArea,
			fillColor: fillColor,
			yAxisLabels: yAxisLabels,
			yAxisNumericValues: yAxisNumericValues,
			yAxisLabel: yAxisLabel,
			showGrid: showGrid,
			gridColor: gridColor,
			gridLineWidth: gridLineWidth,
			showLegend: showLegend,
			legendPosition: legendPosition,
			legendAlignment: legendAlignment,
			legendTextColor: legendTextColor,
			containerBackground: containerBackground,
			reverseChart: reverseChart
		};
		
		#>
		<style>
			#{{ widgetId }} .omsar-line-chart-wrapper canvas {
				max-height: 100%;
			}
			#{{ widgetId }} .omsar-line-chart-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20px;
			}
			#{{ widgetId }} .omsar-line-chart-header .omsar-line-chart-title {
				margin: 0;
				flex: 1;
			}
			#{{ widgetId }} .omsar-line-chart-header-actions {
				display: flex;
				gap: 8px;
				align-items: center;
			}
			#{{ widgetId }} .omsar-line-chart-action-icon {
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
			#{{ widgetId }} .omsar-line-chart-action-icon:hover {
				background-color: #f3f4f6;
				border-color: #9ca3af;
				color: #111827;
			}
			#{{ widgetId }} .omsar-line-chart-action-icon:active {
				background-color: #e5e7eb;
				transform: scale(0.95);
			}
			#{{ widgetId }} .omsar-line-chart-action-icon svg {
				width: 18px;
				height: 18px;
				fill: currentColor;
			}
			#{{ widgetId }} .omsar-line-chart-action-icon[data-tooltip]:hover::before {
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
			#{{ widgetId }} .omsar-line-chart-action-icon[data-tooltip]:hover::after {
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
		<div class="omsar-line-chart-wrapper" id="{{ widgetId }}">
			<# if (chartTitle || showExportIcon || showZoomIcon) { #>
				<div class="omsar-line-chart-header">
					<# if (chartTitle) { #>
						<h3 class="omsar-line-chart-title">{{{ chartTitle }}}</h3>
					<# } else { #>
						<div></div>
					<# } #>
					<# if (showExportIcon || showZoomIcon) { #>
						<div class="omsar-line-chart-header-actions">
							<# if (showExportIcon) { #>
								<button type="button" class="omsar-line-chart-action-icon omsar-line-chart-export-icon" data-tooltip="<?php echo esc_attr__( 'Download as Image', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Download chart as image', 'omsar' ); ?>" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
									</svg>
								</button>
							<# } #>
							<# if (showZoomIcon) { #>
								<button type="button" class="omsar-line-chart-action-icon omsar-line-chart-zoom-icon" data-tooltip="<?php echo esc_attr__( 'Open in Full View', 'omsar' ); ?>" aria-label="<?php echo esc_attr__( 'Open chart in full view', 'omsar' ); ?>" data-widget-id="{{{ widgetId }}}" data-canvas-id="{{{ canvasId }}}" data-chart-title="{{{ chartTitle }}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
									</svg>
								</button>
							<# } #>
						</div>
					<# } #>
				</div>
			<# } #>
			<div class="omsar-line-chart-container">
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
				var exportIcon = document.querySelector('#{{ widgetId }} .omsar-line-chart-export-icon');
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
				var zoomIcon = document.querySelector('#{{ widgetId }} .omsar-line-chart-zoom-icon');
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
		<?php
	}
}

