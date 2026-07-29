<?php
/**
 * Elementor Procurement Notices Widget Class
 * 
 * Displays procurement notices in a card layout with customizable styling
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
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

class OMSAR_Procurement_Notices_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_procurement_notices';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Procurement Notices', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
		return [ 'procurement', 'notices', 'tenders', 'bids', 'grid', 'cards' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'max' => 100,
				'description' => esc_html__( 'Number of procurement notices to display.', 'omsar' ),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'date' => esc_html__( 'Date', 'omsar' ),
					'title' => esc_html__( 'Title', 'omsar' ),
					'meta_value' => esc_html__( 'Opening Date', 'omsar' ),
				],
				'default' => 'date',
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'DESC' => esc_html__( 'Descending', 'omsar' ),
					'ASC' => esc_html__( 'Ascending', 'omsar' ),
				],
				'default' => 'DESC',
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
			]
		);

		$this->add_control(
			'default_image',
			[
				'label' => esc_html__( 'Default Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Default image to use when a notice doesn\'t have a featured image.', 'omsar' ),
			]
		);

		$this->add_control(
			'filter_label',
			[
				'label' => esc_html__( 'Filter Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => function_exists( 'pll__' ) ? pll__( 'Filter by:' ) : 'Filter by:',
				'description' => esc_html__( 'Label shown before the status dropdown (e.g. "Filter by:"). Leave empty to use default.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Filters (Tabs) Style
		$this->start_controls_section(
			'filters_style_section',
			[
				'label' => esc_html__( 'Filter Tabs Style', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filters_dropdown_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-status-tab',
			]
		);

		// Text Color
		$this->add_control(
			'filters_dropdown_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab' => 'color: {{VALUE}};',
				],
			]
		);

		// Background Color
		$this->add_control(
			'filters_dropdown_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Border
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filters_dropdown_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-status-tab',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '2',
							'right' => '2',
							'bottom' => '2',
							'left' => '2',
							'unit' => 'px',
						],
					],
					'color' => [
						'default' => '#7db3e8',
					],
				],
			]
		);

		// Border Radius
		$this->add_control(
			'filters_dropdown_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Padding
		$this->add_control(
			'filters_dropdown_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '10',
					'right' => '24',
					'bottom' => '10',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Active State Heading
		$this->add_control(
			'filters_tab_active_heading',
			[
				'label' => esc_html__( 'Active Tab', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Active Text Color
		$this->add_control(
			'filters_tab_active_text_color',
			[
				'label' => esc_html__( 'Active Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab.active' => 'color: {{VALUE}};',
				],
			]
		);

		// Active Background Color
		$this->add_control(
			'filters_tab_active_background_color',
			[
				'label' => esc_html__( 'Active Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Active Border Color
		$this->add_control(
			'filters_tab_active_border_color',
			[
				'label' => esc_html__( 'Active Border Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab.active' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Hover State Heading
		$this->add_control(
			'filters_dropdown_hover_heading',
			[
				'label' => esc_html__( 'Hover State', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Hover Text Color
		$this->add_control(
			'filters_dropdown_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// Hover Background Color
		$this->add_control(
			'filters_dropdown_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Hover Border Color
		$this->add_control(
			'filters_dropdown_hover_border_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-status-tab:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Hover Box Shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_dropdown_hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-status-tab:hover',
			]
		);

		$this->end_controls_section();

		// Title Style Section
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-card-title',
				'default' => [
					'font_size' => [ 'size' => 18 ],
					'font_weight' => '700',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Badge Style - Open Section
		$this->start_controls_section(
			'badge_open_style_section',
			[
				'label' => esc_html__( 'Badge Style - Open', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_open_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-open',
			]
		);

		$this->add_control(
			'badge_open_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#28a745',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-open' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_open_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-open' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_open_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-open',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
						],
					],
					'color' => [
						'default' => '#28a745',
					],
				],
			]
		);

		$this->add_control(
			'badge_open_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-open' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_open_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '4',
					'right' => '12',
					'bottom' => '4',
					'left' => '12',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-open' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Badge Style - Closed Section
		$this->start_controls_section(
			'badge_closed_style_section',
			[
				'label' => esc_html__( 'Badge Style - Closed', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_closed_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-closed',
			]
		);

		$this->add_control(
			'badge_closed_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dc3545',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-closed' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_closed_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-closed' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_closed_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-closed',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
						],
					],
					'color' => [
						'default' => '#dc3545',
					],
				],
			]
		);

		$this->add_control(
			'badge_closed_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-closed' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_closed_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '4',
					'right' => '12',
					'bottom' => '4',
					'left' => '12',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-closed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Badge Style - Cancelled Section
		$this->start_controls_section(
			'badge_cancelled_style_section',
			[
				'label' => esc_html__( 'Badge Style - Cancelled', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_cancelled_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-cancelled',
			]
		);

		$this->add_control(
			'badge_cancelled_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6c757d',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-cancelled' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_cancelled_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-status-cancelled' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_cancelled_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-status-cancelled',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
						],
					],
					'color' => [
						'default' => '#6c757d',
					],
				],
			]
		);

		$this->add_control(
			'badge_cancelled_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-cancelled' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_cancelled_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '4',
					'right' => '12',
					'bottom' => '4',
					'left' => '12',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-status-cancelled' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Card Style Section
		$this->start_controls_section(
			'card_style_section',
			[
				'label' => esc_html__( 'Card Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'card_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-procurement-card',
				'default' => [
					'background' => [
						'color' => '#e3f2fd',
					],
				],
			]
		);

		$this->add_control(
			'card_border_radius',
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
					'{{WRAPPER}} .omsar-procurement-card' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-procurement-card-image' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .omsar-procurement-card-content' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-card',
			]
		);

		$this->add_control(
			'card_padding',
			[
				'label' => esc_html__( 'Card Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_gap',
			[
				'label' => esc_html__( 'Gap Between Cards', 'omsar' ),
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-notices-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_min_height',
			[
				'label' => esc_html__( 'Card Minimum Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-card' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Image Style Section
		$this->start_controls_section(
			'image_style_section',
			[
				'label' => esc_html__( 'Image Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'gradient_enabled',
			[
				'label' => esc_html__( 'Enable Gradient Overlay', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'gradient_overlay',
				'label' => esc_html__( 'Gradient Overlay', 'omsar' ),
				'types' => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-procurement-card-gradient',
				'condition' => [
					'gradient_enabled' => 'yes',
				],
				'default' => [
					'background' => [
						'color_a' => 'rgba(227, 242, 253, 0.8)',
						'color_b' => 'rgba(255, 255, 255, 0.6)',
						'angle' => 135,
					],
				],
			]
		);

		$this->add_control(
			'gradient_opacity',
			[
				'label' => esc_html__( 'Gradient Opacity', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '' ],
				'range' => [
					'' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-card-gradient' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'gradient_enabled' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// Procurement Description Style Section
		$this->start_controls_section(
			'description_style_section',
			[
				'label' => esc_html__( 'Procurement Description Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-description-info',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#666',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-description-info' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-description-info i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Date Style Section
		$this->start_controls_section(
			'date_style_section',
			[
				'label' => esc_html__( 'Date Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-date-range',
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#666',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-date-range' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'date_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-date-range i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Apply Button Style Section
		$this->start_controls_section(
			'apply_button_style_section',
			[
				'label' => esc_html__( 'Apply Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'apply_button_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-apply-button',
			]
		);

		$this->add_control(
			'apply_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'apply_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a3e7b',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'apply_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'apply_button_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '10',
					'right' => '24',
					'bottom' => '10',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'apply_button_margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '15',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Hover State
		$this->add_control(
			'apply_button_hover_heading',
			[
				'label' => esc_html__( 'Hover State', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'apply_button_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'apply_button_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-apply-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Load More Button Style
		$this->start_controls_section(
			'load_more_button_style_section',
			[
				'label' => esc_html__( 'Load More Button Style', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'load_more_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors'  => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'load_more_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '10',
					'right'  => '24',
					'bottom' => '10',
					'left'   => '24',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'load_more_button_typography',
				'label'    => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-procurement-load-more-btn',
				'fields_options' => [
					'typography' => [ 'default' => 'yes' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
					'font_weight' => [ 'default' => '600' ],
				],
			]
		);

		$this->add_control(
			'load_more_button_hover_heading',
			[
				'label'     => esc_html__( 'Hover State', 'omsar' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'load_more_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn:hover:not(:disabled)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_hover_text_color',
			[
				'label'     => esc_html__( 'Hover Text Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn:hover:not(:disabled)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_button_hover_border_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-btn:hover:not(:disabled)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_wrapper_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'omsar' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'omsar' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'omsar' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'omsar' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .omsar-procurement-load-more-wrapper' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'load_more_wrapper_margin',
			[
				'label'      => esc_html__( 'Wrapper Margin', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '24',
					'right'  => '0',
					'bottom' => '16',
					'left'   => '0',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .omsar-procurement-load-more-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'omsar_bd_render_procurement_notices_widget' ) ) {
			omsar_bd_render_procurement_notices_widget(
				omsar_bd_procurement_notices_settings_from_elementor( $settings ),
				(string) $this->get_id(),
				'elementor-element'
			);
			return;
		}

		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : 6;
		$orderby = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '3';
		$default_image = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
		$gradient_enabled = isset( $settings['gradient_enabled'] ) && $settings['gradient_enabled'] === 'yes';

		// Query procurement notices - fetch all posts to sort by status priority
		$query_args = array(
			'post_type' => 'procurement_notices',
			'posts_per_page' => -1, // Get all posts for proper sorting
			'post_status' => 'publish',
			'no_found_rows' => true, // Don't count for performance since we're getting all
		);

		$procurement_query = new WP_Query( $query_args );
		
		// Sort all posts by status priority (Open > Cancelled > Closed) and then by publication_custom_date descending
		$all_posts = $procurement_query->posts;
		if ( ! empty( $all_posts ) ) {
			$all_posts = omsar_sort_procurements_by_status_priority( $all_posts );
		}
		$total_in_db = count( $all_posts );
		
		// Default active tab is "Open" — filter by open for initial display (no "All" tab)
		$all_posts = omsar_filter_procurements_by_status( $all_posts, 'open' );
		$total_open = count( $all_posts );
		// Initial load: only show posts_per_page items for the active tab
		$all_posts = array_slice( $all_posts, 0, $posts_per_page );
		$has_more_open = $total_open > $posts_per_page;

		$procurement_query->posts = $all_posts;
		$procurement_query->post_count = count( $all_posts );
		$has_posts = ! empty( $all_posts );

		$widget_id = $this->get_id();
		
		// Get labels
		$open_label = function_exists( 'pll__' ) ? pll__( 'Open' ) : __( 'Open', 'omsar' );
		$closed_label = function_exists( 'pll__' ) ? pll__( 'Closed' ) : __( 'Closed', 'omsar' );
		$cancelled_label = function_exists( 'pll__' ) ? pll__( 'Cancelled' ) : __( 'Cancelled', 'omsar' );
		$apply_label = function_exists( 'pll__' ) ? pll__( 'Apply' ) : __( 'Apply', 'omsar' );
		$no_procurements_message = function_exists( 'pll__' ) ? pll__( 'No procurement notices are available at this time. Please check back later.' ) : __( 'No procurement notices are available at this time. Please check back later.', 'omsar' );
		$load_more_label = function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' );

		// Get filter labels (tabs: Open, Cancelled, Closed — no "All")
		$filter_label_default = null;
		$filter_label = ! empty( $settings['filter_label'] ) ? $settings['filter_label'] : $filter_label_default;
		$filter_aria_label = function_exists( 'pll__' ) ? pll__( 'Filter by status' ) : 'Filter by status';
		
		// If no posts at all from DB, display message instead of widget structure
		if ( $total_in_db <= 0 ) {
			?>
			<div class="omsar-procurement-notices-widget elementor-element-<?php echo esc_attr( $widget_id ); ?>">
				<div class="omsar-procurement-no-results">
					<p class="omsar-procurement-no-results-message"><?php echo esc_html( $no_procurements_message ); ?></p>
				</div>
			</div>
			<?php
			return;
		}
		?>

		<div class="omsar-procurement-notices-widget elementor-element-<?php echo esc_attr( $widget_id ); ?>"
			data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-order="<?php echo esc_attr( $order ); ?>"
			data-columns="<?php echo esc_attr( $columns ); ?>"
			data-default-image="<?php echo esc_attr( $default_image ); ?>"
			data-gradient-enabled="<?php echo esc_attr( $gradient_enabled ? 'yes' : 'no' ); ?>">
			
			<!-- Status Filter Tabs (Open default, Cancelled, Closed — no All) -->
			<div class="omsar-procurement-filters" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" role="group" aria-label="<?php echo esc_attr( $filter_aria_label ); ?>">
				<label class="omsar-procurement-filter-label"><?php echo esc_html( $filter_label ); ?></label>
				<div class="omsar-procurement-status-tabs" role="tablist">
					<button type="button" class="omsar-procurement-status-tab active" role="tab" aria-selected="true" data-status="open" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $open_label ); ?></button>
					<button type="button" class="omsar-procurement-status-tab" role="tab" aria-selected="false" data-status="cancelled" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $cancelled_label ); ?></button>
					<button type="button" class="omsar-procurement-status-tab" role="tab" aria-selected="false" data-status="closed" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $closed_label ); ?></button>
				</div>
			</div>
			
			<div id="procurement-cards-container" class="omsar-procurement-notices-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
				<?php
				if ( $has_posts ) :
					while ( $procurement_query->have_posts() ) :
						$procurement_query->the_post();
						$this->render_procurement_card( $settings, $default_image, $gradient_enabled, $open_label, $closed_label, $cancelled_label, $apply_label );
					endwhile;
				else :
					echo '<div class="omsar-procurement-no-results"><p class="omsar-procurement-no-results-message">' . esc_html( $no_procurements_message ) . '</p></div>';
				endif;
				wp_reset_postdata();
				?>
			</div>
			<div class="omsar-procurement-load-more-wrapper"<?php echo $has_more_open ? '' : ' style="display:none;"'; ?>>
				<button type="button" class="omsar-procurement-load-more-btn" data-page="1" data-status="open" data-total="<?php echo (int) $total_open; ?>" data-has-more="<?php echo $has_more_open ? '1' : '0'; ?>"><?php echo esc_html( $load_more_label ); ?></button>
			</div>
		</div>

		<?php
		if ( function_exists( 'omsar_procurement_render_apply_modal_once' ) ) {
			omsar_procurement_render_apply_modal_once();
		}
		?>

		<?php
	}

	/**
	 * Truncate text to a specified number of words
	 *
	 * @param string $text The text to truncate
	 * @param int $word_limit Number of words to keep
	 * @return string Truncated text with ellipsis if needed
	 */
	private function truncate_words( $text, $word_limit = 10 ) {
		if ( empty( $text ) ) {
			return '';
		}
		
		// Strip HTML tags and normalize whitespace
		$text = strip_tags( $text );
		$text = trim( $text );
		
		if ( empty( $text ) ) {
			return '';
		}
		
		// Split into words, filtering out empty strings
		$words = array_filter( explode( ' ', $text ), function( $word ) {
			return ! empty( trim( $word ) );
		} );
		$words = array_values( $words ); // Re-index array
		$word_count = count( $words );
		
		if ( $word_count > $word_limit ) {
			$words = array_slice( $words, 0, $word_limit );
			return implode( ' ', $words ) . '…';
		}
		
		return $text;
	}

	/**
	 * Render a single procurement notice card
	 *
	 * @param array $settings Widget settings
	 * @param string $default_image Default image URL
	 * @param bool $gradient_enabled Whether gradient overlay is enabled
	 * @param string $open_label Label for Open status
	 * @param string $closed_label Label for Closed status
	 * @param string $cancelled_label Label for Cancelled status
	 * @param string $apply_label Label for Apply button
	 */
	private function render_procurement_card( $settings, $default_image, $gradient_enabled, $open_label, $closed_label, $cancelled_label, $apply_label ) {
		$post_id = get_the_ID();

		$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
		$is_arabic = ( $current_lang === 'ar' );
		
		// Get custom fields
		$publication_custom_date = '';
		$submission_deadline = '';
		$procurement_description = '';
		$procurement_description_ar = '';
		$procurement_title_ar = '';
		
		if ( function_exists( 'get_field' ) ) {
			$publication_custom_date = get_field( 'publication_custom_date', $post_id );
			$submission_deadline = get_field( 'submission_deadline', $post_id );
			$procurement_description = get_field( 'procurement_description', $post_id );
			$procurement_description_ar = get_field( 'procurement_description_ar', $post_id );
			$procurement_title_ar = get_field( 'procurement_title_ar', $post_id );
		} else {
			// Fallback to post meta
			$publication_custom_date = get_post_meta( $post_id, 'publication_custom_date', true );
			$submission_deadline = get_post_meta( $post_id, 'submission_deadline', true );
			$procurement_description = get_post_meta( $post_id, 'procurement_description', true );
			$procurement_description_ar = get_post_meta( $post_id, 'procurement_description_ar', true );
			$procurement_title_ar = get_post_meta( $post_id, 'procurement_title_ar', true );
		}
		
		// Title: post title for English, procurement_title_ar for Arabic (fallback to post title)
		$card_title = $is_arabic && ! empty( $procurement_title_ar ) ? $procurement_title_ar : get_the_title();

		// Description: procurement_description for English, procurement_description_ar for Arabic (fallback to EN)
		$description_raw = $is_arabic && ! empty( $procurement_description_ar ) ? $procurement_description_ar : $procurement_description;

		// Truncate procurement description to 10 words
		$procurement_description = $this->truncate_words( $description_raw, 10 );

		// Format dates for display (publication date and submission deadline)
		$formatted_pub_date = '';
		$formatted_deadline = '';
		$deadline_timestamp = null;
		
		if ( $publication_custom_date ) {
			// Handle different date formats - prioritize d/m/Y format
			$publication_date_str = trim( (string) $publication_custom_date );
			
			// First try d/m/Y format (ACF Return Format)
			if ( preg_match( '/^\d{1,2}\/\d{1,2}\/\d{4}$/', $publication_date_str ) ) {
				$date_obj = DateTime::createFromFormat( 'd/m/Y', $publication_date_str );
				if ( $date_obj !== false ) {
					$formatted_pub_date = $date_obj->format( 'd/m/Y' );
				} else {
					$formatted_pub_date = $publication_custom_date;
				}
			}
			// Check if it's an 8-digit number (Ymd format)
			elseif ( is_numeric( $publication_custom_date ) && strlen( $publication_date_str ) == 8 ) {
				$date_obj = DateTime::createFromFormat( 'Ymd', $publication_date_str );
				if ( $date_obj !== false ) {
					$formatted_pub_date = $date_obj->format( 'd/m/Y' );
				} else {
					$formatted_pub_date = date( 'd/m/Y', $publication_custom_date );
				}
			}
			// Try other formats
			elseif ( is_numeric( $publication_custom_date ) ) {
				$formatted_pub_date = date( 'd/m/Y', $publication_custom_date );
			} else {
				$timestamp = strtotime( $publication_custom_date );
				$formatted_pub_date = $timestamp ? date( 'd/m/Y', $timestamp ) : $publication_custom_date;
			}
		}
		
		if ( $submission_deadline ) {
			// Parse submission deadline date
			// ACF Return Format is d/m/Y, so prioritize that format
			$submission_deadline_str = trim( (string) $submission_deadline );
			$str_length = strlen( $submission_deadline_str );
			
			// First try d/m/Y format (ACF Return Format: "20/01/2026")
			if ( preg_match( '/^\d{1,2}\/\d{1,2}\/\d{4}$/', $submission_deadline_str ) ) {
				$date_obj = DateTime::createFromFormat( 'd/m/Y', $submission_deadline_str );
				if ( $date_obj !== false ) {
					$date_obj->setTime( 0, 0, 0 );
					$deadline_timestamp = $date_obj->getTimestamp();
					$formatted_deadline = $date_obj->format( 'd/m/Y' );
				} else {
					$deadline_timestamp = null;
					$formatted_deadline = $submission_deadline;
				}
			}
			// Check if it's an 8-digit number (Ymd format like 20260120)
			elseif ( $str_length == 8 && ctype_digit( $submission_deadline_str ) ) {
				// Ymd format (ACF date field format: 20260120 = January 20, 2026)
				$date_obj = DateTime::createFromFormat( 'Ymd', $submission_deadline_str );
				if ( $date_obj !== false ) {
					$date_obj->setTime( 0, 0, 0 );
					$deadline_timestamp = $date_obj->getTimestamp();
					$formatted_deadline = $date_obj->format( 'd/m/Y' );
				} else {
					$deadline_timestamp = null;
					$formatted_deadline = $submission_deadline;
				}
			}
			// Check if it's a 10-digit Unix timestamp
			elseif ( $str_length == 10 && ctype_digit( $submission_deadline_str ) ) {
				$deadline_timestamp = (int) $submission_deadline;
				$formatted_deadline = date( 'd/m/Y', $deadline_timestamp );
			}
			// Try other date formats
			else {
				// Try strtotime for various formats
				$deadline_timestamp = strtotime( $submission_deadline );
				if ( $deadline_timestamp !== false ) {
					$formatted_deadline = date( 'd/m/Y', $deadline_timestamp );
				} else {
					// Try Y-m-d format
					$date_obj = DateTime::createFromFormat( 'Y-m-d', $submission_deadline_str );
					if ( $date_obj !== false ) {
						$date_obj->setTime( 0, 0, 0 );
						$deadline_timestamp = $date_obj->getTimestamp();
						$formatted_deadline = $date_obj->format( 'd/m/Y' );
					} else {
						$deadline_timestamp = null;
						$formatted_deadline = $submission_deadline;
					}
				}
			}
		}

		// Date labels (Polylang)
		$opening_label = function_exists( 'pll__' ) ? pll__( 'Opening Date' ) : __( 'Opening Date', 'omsar' );
		$closing_label = function_exists( 'pll__' ) ? pll__( 'Closing Date' ) : __( 'Closing Date', 'omsar' );

		// Determine status from dates and is_cancelled
		$status = function_exists( 'omsar_get_procurement_status' )
			? omsar_get_procurement_status( $post_id )
			: 'closed';
		
		// Get status label
		if ( $status === 'cancelled' ) {
			$status_label = $cancelled_label;
		} elseif ( $status === 'closed' ) {
			$status_label = $closed_label;
		} else {
			// Default to 'open' if status is empty or invalid
			$status_label = $open_label;
		}

		// Get featured image or use default
		$image_url = '';
		if ( has_post_thumbnail() ) {
			$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( ! empty( $default_image ) ) {
			$image_url = $default_image;
		}

		// Build inline style for background image
		$card_style = '';
		if ( $image_url ) {
			$card_style = 'background-image: url(' . esc_url( $image_url ) . ');';
		}
		
		// Get the procurement single page URL (using page template)
		$post_permalink = function_exists( 'omsar_get_procurement_single_page_url' ) 
			? omsar_get_procurement_single_page_url( $post_id ) 
			: get_permalink( $post_id );
		
		// Fallback to regular permalink if page template not found
		if ( ! $post_permalink ) {
			$post_permalink = get_permalink( $post_id );
		}
		?>
		<div class="omsar-procurement-card" <?php echo $card_style ? 'style="' . esc_attr( $card_style ) . '"' : ''; ?>>
			<?php if ( $gradient_enabled ) : ?>
			<div class="omsar-procurement-card-gradient"></div>
			<?php endif; ?>
			
			<a href="<?php echo esc_url( $post_permalink ); ?>" class="omsar-procurement-card-link">
				<div class="omsar-procurement-card-content">
					<div class="omsar-procurement-card-header">
						<h3 class="omsar-procurement-card-title"><?php echo esc_html( $card_title ); ?></h3>
						<span class="omsar-procurement-status-badge omsar-status-<?php echo esc_attr( $status ); ?>">
							<?php echo esc_html( $status_label ); ?>
						</span>
					</div>
					
					<?php if ( $formatted_pub_date || $formatted_deadline ) : ?>
					<div class="omsar-procurement-card-dates">
						<div class="omsar-procurement-date-range">
							<?php if ( $formatted_pub_date ) : ?>
								<div class="omsar-procurement-date-row">
									<i class="bi bi-calendar2-check"></i>
									<span class="omsar-procurement-date-label"><?php echo esc_html( $opening_label ); ?>:</span>
									<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_pub_date ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $formatted_deadline ) : ?>
								<div class="omsar-procurement-date-row">
									<i class="bi bi-calendar2-x"></i>
									<span class="omsar-procurement-date-label"><?php echo esc_html( $closing_label ); ?>:</span>
									<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_deadline ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $procurement_description ) ) : ?>
					<div class="omsar-procurement-card-description">
						<span class="omsar-procurement-description-info">
							<?php echo esc_html( $procurement_description ); ?>
						</span>
					</div>
					<?php endif; ?>
				</div>
			</a>
			<?php
			if ( function_exists( 'omsar_render_procurement_apply_button' ) ) {
				omsar_render_procurement_apply_button( $post_id, $apply_label, $status, 'card' );
			}
			?>
		</div>
		<?php
	}
}
