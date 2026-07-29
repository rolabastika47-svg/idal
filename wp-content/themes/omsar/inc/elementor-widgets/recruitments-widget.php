<?php
/**
 * Elementor Recruitments Widget Class
 * 
 * Displays recruitments in a grid layout with job title, entity, dates, status badge, and apply button
 * 
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

class OMSAR_Recruitments_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_recruitments';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Recruitments', 'omsar' );
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
		return [ 'recruitments', 'jobs', 'careers', 'hiring', 'grid', 'list' ];
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
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'date' => esc_html__( 'Date', 'omsar' ),
					'title' => esc_html__( 'Title', 'omsar' ),
					'meta_value' => esc_html__( 'Closing Date', 'omsar' ),
				],
				'default' => 'meta_value',
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
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 9,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'description' => esc_html__( 'Number of items to show per tab initially and per Load More click.', 'omsar' ),
			]
		);

		$this->add_control(
			'filter_label',
			[
				'label' => esc_html__( 'Filter Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => function_exists( 'pll__' ) ? pll__( 'Filter by:' ) : 'Filter by:',
				'description' => esc_html__( 'Label shown before the status tabs (e.g. "Filter by:")', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Filters (Tabs) Style — style only; tabs are fixed: Open, Upcoming, Closed
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
				'name' => 'filters_tabs_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-status-tab',
			]
		);

		// Text Color
		$this->add_control(
			'filters_tabs_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab' => 'color: {{VALUE}};',
				],
			]
		);

		// Background Color
		$this->add_control(
			'filters_tabs_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Border
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filters_tabs_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-status-tab',
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
			'filters_tabs_border_radius',
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
					'{{WRAPPER}} .omsar-recruitment-status-tab' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Padding
		$this->add_control(
			'filters_tabs_padding',
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
					'{{WRAPPER}} .omsar-recruitment-status-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Spacing between tabs
		$this->add_control(
			'filters_tabs_gap',
			[
				'label' => esc_html__( 'Gap Between Tabs', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 40,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Hover State Heading
		$this->add_control(
			'filters_tabs_hover_heading',
			[
				'label' => esc_html__( 'Hover State', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Hover Text Color
		$this->add_control(
			'filters_tabs_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab:hover:not(.active)' => 'color: {{VALUE}};',
				],
			]
		);

		// Hover Background Color
		$this->add_control(
			'filters_tabs_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab:hover:not(.active)' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Hover Border Color
		$this->add_control(
			'filters_tabs_hover_border_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab:hover:not(.active)' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Hover Box Shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_tabs_hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-status-tab:hover:not(.active)',
			]
		);

		// Active State Heading
		$this->add_control(
			'filters_tabs_active_heading',
			[
				'label' => esc_html__( 'Active Tab', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Active Text Color
		$this->add_control(
			'filters_tabs_active_text_color',
			[
				'label' => esc_html__( 'Active Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab.active' => 'color: {{VALUE}};',
				],
			]
		);

		// Active Background Color
		$this->add_control(
			'filters_tabs_active_background_color',
			[
				'label' => esc_html__( 'Active Background Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Active Border Color
		$this->add_control(
			'filters_tabs_active_border_color',
			[
				'label' => esc_html__( 'Active Border Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-status-tab.active' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Active Box Shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_tabs_active_box_shadow',
				'label' => esc_html__( 'Active Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-status-tab.active',
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Card Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_background_type',
			[
				'label' => esc_html__( 'Background Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'color' => esc_html__( 'Color', 'omsar' ),
					'image' => esc_html__( 'Image', 'omsar' ),
				],
			]
		);

		$this->add_control(
			'card_background_color',
			[
				'label' => esc_html__( 'Card Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f0f7ff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'card_background_type' => 'color',
				],
			]
		);

		$this->add_control(
			'card_background_image',
			[
				'label' => esc_html__( 'Card Background Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => get_template_directory_uri() . '/assets/images/project-2.png',
				],
				'condition' => [
					'card_background_type' => 'image',
				],
			]
		);

		$this->add_control(
			'card_background_image_size',
			[
				'label' => esc_html__( 'Background Size', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover' => esc_html__( 'Cover', 'omsar' ),
					'contain' => esc_html__( 'Contain', 'omsar' ),
					'auto' => esc_html__( 'Auto', 'omsar' ),
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'background-size: {{VALUE}};',
				],
				'condition' => [
					'card_background_type' => 'image',
				],
			]
		);

		$this->add_control(
			'card_background_image_position',
			[
				'label' => esc_html__( 'Background Position', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'omsar' ),
					'center top' => esc_html__( 'Center Top', 'omsar' ),
					'center bottom' => esc_html__( 'Center Bottom', 'omsar' ),
					'left center' => esc_html__( 'Left Center', 'omsar' ),
					'left top' => esc_html__( 'Left Top', 'omsar' ),
					'left bottom' => esc_html__( 'Left Bottom', 'omsar' ),
					'right center' => esc_html__( 'Right Center', 'omsar' ),
					'right top' => esc_html__( 'Right Top', 'omsar' ),
					'right bottom' => esc_html__( 'Right Bottom', 'omsar' ),
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'background-position: {{VALUE}};',
				],
				'condition' => [
					'card_background_type' => 'image',
				],
			]
		);

		$this->add_control(
			'card_background_image_repeat',
			[
				'label' => esc_html__( 'Background Repeat', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => esc_html__( 'No Repeat', 'omsar' ),
					'repeat' => esc_html__( 'Repeat', 'omsar' ),
					'repeat-x' => esc_html__( 'Repeat X', 'omsar' ),
					'repeat-y' => esc_html__( 'Repeat Y', 'omsar' ),
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'background-repeat: {{VALUE}};',
				],
				'condition' => [
					'card_background_type' => 'image',
				],
			]
		);

		$this->add_control(
			'card_background_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'card_background_type' => 'image',
				],
			]
		);

		$this->add_control(
			'card_background_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'card_background_type' => 'image',
					'card_background_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'card_gradient_color_from',
			[
				'label' => esc_html__( 'Gradient Color From', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(173, 216, 230, 0.4)',
				'description' => esc_html__( 'Start color of the gradient overlay', 'omsar' ),
			]
		);

		$this->add_control(
			'card_gradient_color_to',
			[
				'label' => esc_html__( 'Gradient Color To', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.95)',
				'description' => esc_html__( 'End color of the gradient overlay', 'omsar' ),
			]
		);

		$this->add_control(
			'card_gradient_angle',
			[
				'label' => esc_html__( 'Gradient Angle', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'deg',
					'size' => 135,
				],
				'description' => esc_html__( 'Gradient angle in degrees (0 = top to bottom, 90 = left to right, 135 = diagonal)', 'omsar' ),
			]
		);

		$this->add_control(
			'card_gradient_overlay_opacity',
			[
				'label' => esc_html__( 'Gradient Overlay Opacity', 'omsar' ),
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
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card::before' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'card_overlay_border_radius',
			[
				'label' => esc_html__( 'Overlay Border Radius', 'omsar' ),
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
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card::before' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-card',
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
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-card',
			]
		);

		$this->add_control(
			'card_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '24',
					'right' => '24',
					'bottom' => '24',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Title Style
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-title',
			]
		);

		$this->end_controls_section();

		// Details Style
		$this->start_controls_section(
			'details_style_section',
			[
				'label' => esc_html__( 'Details Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'details_text_color',
			[
				'label' => esc_html__( 'Details Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7db3e8',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-entity' => 'color: {{VALUE}};',
					'{{WRAPPER}} .omsar-recruitment-dates' => 'color: {{VALUE}};',
					'{{WRAPPER}} .omsar-recruitment-details .detail-value' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'details_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-entity, {{WRAPPER}} .omsar-recruitment-dates',
			]
		);

		$this->end_controls_section();

		// Badge Style
		$this->start_controls_section(
			'badge_style_section',
			[
				'label' => esc_html__( 'Badge Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Open Status Badge
		$this->add_control(
			'badge_open_heading',
			[
				'label' => esc_html__( 'Open Status Badge', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'badge_open_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-recruitment-badge.status-open',
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#6ee7b7',
					],
					'color_b' => [
						'default' => '#34d399',
					],
				],
			]
		);

		$this->add_control(
			'badge_open_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-open' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_open_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-open' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Closed Status Badge
		$this->add_control(
			'badge_closed_heading',
			[
				'label' => esc_html__( 'Closed Status Badge', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'badge_closed_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-recruitment-badge.status-closed',
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#fca5a5',
					],
					'color_b' => [
						'default' => '#f87171',
					],
				],
			]
		);

		$this->add_control(
			'badge_closed_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-closed' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_closed_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-closed' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Upcoming Status Badge
		$this->add_control(
			'badge_upcoming_heading',
			[
				'label' => esc_html__( 'Upcoming Status Badge', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'badge_upcoming_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-recruitment-badge.status-upcoming',
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#fbbf24',
					],
					'color_b' => [
						'default' => '#f59e0b',
					],
				],
			]
		);

		$this->add_control(
			'badge_upcoming_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-upcoming' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_upcoming_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type'  => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge.status-upcoming' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_border_radius',
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-badge' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-badge',
			]
		);

		$this->end_controls_section();

		// Apply Button Style
		$this->start_controls_section(
			'button_style_section',
			[
				'label' => esc_html__( 'Apply Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'omsar' ),
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-apply-btn',
			]
		);

		$this->add_control(
			'button_border_radius',
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
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-recruitment-apply-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'load_more_button_typography',
				'label'    => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-recruitment-load-more-btn',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn:hover:not(:disabled)' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn:hover:not(:disabled)' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-btn:hover:not(:disabled)' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-wrapper' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .omsar-recruitment-load-more-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$orderby = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'meta_value';
		$order = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '3';
		$posts_per_page = isset( $settings['posts_per_page'] ) ? max( 1, intval( $settings['posts_per_page'] ) ) : 9;
		$background_type = isset( $settings['card_background_type'] ) ? $settings['card_background_type'] : 'image';
		
		// Get configured background image - handle both URL and ID formats
		$background_image = '';
		if ( isset( $settings['card_background_image'] ) ) {
			if ( isset( $settings['card_background_image']['url'] ) && ! empty( $settings['card_background_image']['url'] ) ) {
				$background_image = $settings['card_background_image']['url'];
			} elseif ( isset( $settings['card_background_image']['id'] ) && ! empty( $settings['card_background_image']['id'] ) ) {
				// If stored as ID, get the URL
				$image_url = wp_get_attachment_image_url( $settings['card_background_image']['id'], 'full' );
				if ( $image_url ) {
					$background_image = $image_url;
				}
			}
		}
		
		// Use configured default image if background type is image but no image is set
		// Only fall back to hardcoded default if no image is configured at all
		if ( $background_type === 'image' && empty( $background_image ) ) {
			// Use the control's default value (same as register_controls default)
			$background_image = get_template_directory_uri() . '/assets/images/project-2.png';
		}
		$background_overlay = isset( $settings['card_background_overlay'] ) && $settings['card_background_overlay'] === 'yes';

		// Get gradient settings
		$gradient_color_from = ! empty( $settings['card_gradient_color_from'] ) ? $settings['card_gradient_color_from'] : 'rgba(173, 216, 230, 0.4)';
		$gradient_color_to = ! empty( $settings['card_gradient_color_to'] ) ? $settings['card_gradient_color_to'] : 'rgba(255, 255, 255, 0.95)';
		$gradient_angle = ! empty( $settings['card_gradient_angle']['size'] ) ? $settings['card_gradient_angle']['size'] : 135;
		$gradient_unit = ! empty( $settings['card_gradient_angle']['unit'] ) ? $settings['card_gradient_angle']['unit'] : 'deg';
		$gradient_opacity = ! empty( $settings['card_gradient_overlay_opacity']['size'] ) ? $settings['card_gradient_overlay_opacity']['size'] : 0.8;
		$overlay_border_radius = ! empty( $settings['card_overlay_border_radius']['size'] ) ? $settings['card_overlay_border_radius']['size'] : 12;
		$overlay_border_radius_unit = ! empty( $settings['card_overlay_border_radius']['unit'] ) ? $settings['card_overlay_border_radius']['unit'] : 'px';
		
		// Build gradient CSS
		$gradient_css = sprintf(
			'background: linear-gradient(%s%s, %s 0%%, %s 100%%); opacity: %s; border-radius: %s%s;',
			esc_attr( $gradient_angle ),
			esc_attr( $gradient_unit ),
			esc_attr( $gradient_color_from ),
			esc_attr( $gradient_color_to ),
			esc_attr( $gradient_opacity ),
			esc_attr( $overlay_border_radius ),
			esc_attr( $overlay_border_radius_unit )
		);

		// Build query args - fetch all posts to sort by status priority
		$args = array(
			'post_type' => 'recruitments',
			'posts_per_page' => -1, // Get all posts for proper sorting
			'post_status' => 'publish',
			'no_found_rows' => true, // Don't count for performance since we're getting all
		);

		$query = new WP_Query( $args );
		
		// Sort all posts by status priority (Open > Upcoming > Closed) and then by opening_date descending
		$all_posts = $query->posts;
		if ( ! empty( $all_posts ) ) {
			$all_posts = omsar_sort_recruitments_by_status_priority( $all_posts );
		}
		// Default active tab is "Open" — filter by open for initial display
		$all_posts = omsar_filter_recruitments_by_status( $all_posts, 'open' );
		$total_open = count( $all_posts );
		$has_more_open = $total_open > $posts_per_page;
		// Initial load: only show first posts_per_page items for the active tab
		$display_posts = array_slice( $all_posts, 0, $posts_per_page );
		
		$query->posts = $display_posts;
		$query->post_count = count( $display_posts );
		
		$has_posts = ! empty( $display_posts );

		// Check if current language is Arabic
		$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
		$is_arabic = ( $current_lang === 'ar' );

		// Get labels
		$open_label      = function_exists( 'pll__' ) ? pll__( 'Open' ) : 'Open';
		$closed_label    = function_exists( 'pll__' ) ? pll__( 'Closed' ) : 'Closed';
		$upcoming_label  = function_exists( 'pll__' ) ? pll__( 'Upcoming' ) : 'Upcoming';
		$apply_label     = function_exists( 'pll__' ) ? pll__( 'Apply' ) : 'Apply';
		$notify_me_label = function_exists( 'pll__' ) ? pll__( 'Notify me' ) : 'Notify me';

		$open_label_tab      = function_exists( 'pll__' ) ? pll__( 'Open for applications' ) : 'Open for applications';
		$upcoming_label_tab  = function_exists( 'pll__' ) ? pll__( 'Upcoming positions' ) : 'Upcoming positions';
		$closed_label_tab    = function_exists( 'pll__' ) ? pll__( 'Closed positions' ) : 'Closed positions';

		$widget_id = $this->get_id();
		
		// Get filter labels (no "All" — tabs are Open, Upcoming, Closed only)
		// $filter_label_default = function_exists( 'pll__' ) ? pll__( 'Filter by:' ) : 'Filter by:';
		$filter_label = ! empty( $settings['filter_label'] ) ? $settings['filter_label'] : null;
		$filter_aria_label = function_exists( 'pll__' ) ? pll__( 'Filter by status' ) : 'Filter by status';
		$load_more_label = function_exists( 'pll__' ) ? pll__( 'Load More' ) : 'Load More';
		?>

		<div class="omsar-recruitments-widget elementor-element-<?php echo esc_attr( $widget_id ); ?>"<?php if ( $is_arabic ) : ?> dir="rtl"<?php endif; ?>
			data-background-type="<?php echo esc_attr( $background_type ); ?>"
			data-background-image="<?php echo esc_attr( $background_image ); ?>"
			data-background-overlay="<?php echo esc_attr( $background_overlay ? 'yes' : 'no' ); ?>"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-order="<?php echo esc_attr( $order ); ?>"
			data-columns="<?php echo esc_attr( $columns ); ?>"
			data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
			<style>
				.elementor-element-<?php echo esc_attr( $widget_id ); ?> .omsar-recruitment-card::before {
					<?php echo esc_html( $gradient_css ); ?>
				}
			</style>
			
			<!-- Status Filter Tabs (Open, Upcoming, Closed — default active: Open) -->
			<div class="omsar-recruitments-filters" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" role="group" aria-label="<?php echo esc_attr( $filter_aria_label ); ?>">
				<?php if ( ! empty( $filter_label ) ) : ?>
					<span class="omsar-recruitment-filter-label"><?php echo esc_html( $filter_label ); ?></span>
				<?php endif; ?>
				<div class="omsar-recruitment-status-tabs" role="tablist">
					<button type="button" class="omsar-recruitment-status-tab active" role="tab" aria-selected="true" data-status="open" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $open_label_tab ); ?></button>
					<button type="button" class="omsar-recruitment-status-tab" role="tab" aria-selected="false" data-status="upcoming" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $upcoming_label_tab ); ?></button>
					<button type="button" class="omsar-recruitment-status-tab" role="tab" aria-selected="false" data-status="closed" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $closed_label_tab ); ?></button>
				</div>
			</div>
			
			<div id="recruitments-cards-container" class="omsar-recruitments-grid" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
				<?php if ( $has_posts ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post();
						$post_id = get_the_ID();
						
						// Get post data - use Arabic fields if Arabic, otherwise use English
						if ( $is_arabic ) {
							$job_title_ar = get_field( 'job_title_ar' );
							$entity_ar = get_field( 'entity_ar' );
							// Use Arabic fields if they exist, otherwise fallback to English
							$job_title = ! empty( $job_title_ar ) ? $job_title_ar : get_the_title();
							$entity = ! empty( $entity_ar ) ? $entity_ar : get_field( 'entity' );
						} else {
							$job_title = get_the_title();
							$entity = get_field( 'entity' );
						}
						
						// Get featured image or use configured default
						$item_background_image = $background_image;
						if ( $background_type === 'image' ) {
							if ( has_post_thumbnail( $post_id ) ) {
								$item_background_image = get_the_post_thumbnail_url( $post_id, 'large' );
							} elseif ( ! empty( $background_image ) ) {
								$item_background_image = $background_image;
							}
						}
						
						$opening_date = get_field( 'opening_date' );
						$closing_date = get_field( 'closing_date' );
						$job_link = $current_lang === 'ar' ? get_field( 'job_link_ar' ) : get_field( 'job_link' );
						
						// Format dates
						$opening_date_formatted = '';
						$closing_date_formatted = '';
						$opening_date_obj       = null;
						$closing_date_obj       = null;
						
						if ( $opening_date ) {
							if ( is_numeric( $opening_date ) && strlen( $opening_date ) == 8 ) {
								$date_obj = DateTime::createFromFormat( 'Ymd', $opening_date );
								if ( $date_obj ) {
									$opening_date_obj       = $date_obj;
									$opening_date_formatted = $date_obj->format( 'd/m/Y' );
								}
							} else {
								$date_obj = DateTime::createFromFormat( 'Y-m-d', $opening_date );
								if ( ! $date_obj ) {
									$date_obj = DateTime::createFromFormat( 'd/m/Y', $opening_date );
								}
								if ( $date_obj ) {
									$opening_date_obj       = $date_obj;
									$opening_date_formatted = $date_obj->format( 'd/m/Y' );
								} else {
									$opening_date_formatted = $opening_date;
								}
							}
						}
						
						if ( $closing_date ) {
							if ( is_numeric( $closing_date ) && strlen( $closing_date ) == 8 ) {
								$closing_date_obj = DateTime::createFromFormat( 'Ymd', $closing_date );
								if ( $closing_date_obj ) {
									$closing_date_formatted = $closing_date_obj->format( 'd/m/Y' );
								}
							} else {
								$closing_date_obj = DateTime::createFromFormat( 'Y-m-d', $closing_date );
								if ( ! $closing_date_obj ) {
									$closing_date_obj = DateTime::createFromFormat( 'd/m/Y', $closing_date );
								}
								if ( $closing_date_obj ) {
									$closing_date_formatted = $closing_date_obj->format( 'd/m/Y' );
								} else {
									$closing_date_formatted = $closing_date;
								}
							}
						}
						
						// Get recruitment status from ACF field (use helper function if available)
						if (function_exists('omsar_get_recruitment_status_from_acf')) {
							$status_type = omsar_get_recruitment_status_from_acf(get_the_ID());
						} else {
							// Fallback: direct ACF field read - check both field name variations
							// Try typo version first (the actual field name: recruitement_status)
							$status = get_field('recruitement_status', get_the_ID());
							if (empty($status) || $status === false) {
								$status = get_field('recruitment_status', get_the_ID());
							}
							if (is_array($status)) {
								$status = reset($status);
							}
							if (!empty($status) && $status !== false) {
								$status_type = strtolower(trim($status));
								if (!in_array($status_type, array('open', 'closed', 'upcoming'))) {
									$status_type = 'closed';
								}
							} else {
								$status_type = 'closed';
							}
						}

						if ( 'upcoming' === $status_type ) {
							$status_badge = $upcoming_label;
						} elseif ( 'open' === $status_type ) {
							$status_badge = $open_label;
						} else {
							$status_badge = $closed_label;
						}

						$status_class = 'status-' . $status_type;
					?>
						<div class="omsar-recruitment-item">
							<div class="omsar-recruitment-card <?php echo $is_arabic ? 'rtl-card' : ''; ?><?php echo $background_overlay ? ' has-overlay' : ''; ?>"
								<?php if ( $is_arabic ) : ?> dir="rtl"<?php endif; ?>
								<?php if ( $background_type === 'image' && ! empty( $item_background_image ) ) : ?>
									style="background-image: url('<?php echo esc_url( $item_background_image ); ?>');"
								<?php endif; ?>>
								<div class="omsar-recruitment-content">
									<div class="omsar-recruitment-header">
										<h3 class="omsar-recruitment-title"><?php echo esc_html( $job_title ); ?></h3>
										<div class="omsar-recruitment-badge <?php echo esc_attr( $status_class ); ?>">
											<?php echo esc_html( $status_badge ); ?>
										</div>
									</div>
									
									<div class="omsar-recruitment-details">
										<?php if ( $entity ) : ?>
											<p class="omsar-recruitment-entity">
												<i class="bi bi-building" aria-hidden="true"></i>
												<span class="detail-value"><?php echo esc_html( $entity ); ?></span>
											</p>
										<?php endif; ?>
										
										<?php if ( $opening_date_formatted || $closing_date_formatted ) : ?>
											<p class="omsar-recruitment-dates">
												<i class="bi bi-calendar3" aria-hidden="true"></i>
												<?php if ( $opening_date_formatted && $closing_date_formatted ) : ?>
													<span class="detail-value"><?php echo esc_html( $opening_date_formatted ); ?> - <?php echo esc_html( $closing_date_formatted ); ?></span>
												<?php elseif ( $opening_date_formatted ) : ?>
													<span class="detail-value"><?php echo esc_html( $opening_date_formatted ); ?></span>
												<?php elseif ( $closing_date_formatted ) : ?>
													<span class="detail-value"><?php echo esc_html( $closing_date_formatted ); ?></span>
												<?php endif; ?>
											</p>
										<?php endif; ?>
									</div>
									
									<?php if ( 'open' === $status_type && ! empty( $job_link ) ) : ?>
										<div class="omsar-recruitment-actions">
											<a href="<?php echo esc_url( $job_link ); ?>" class="omsar-recruitment-apply-btn" target="_blank" rel="noopener noreferrer">
												<?php echo esc_html( $apply_label ); ?>
											</a>
										</div>
									<?php elseif ( 'upcoming' === $status_type ) : ?>
										<div class="omsar-recruitment-actions">
											<button type="button" class="omsar-recruitment-apply-btn omsar-recruitment-notify-btn" data-recruitment-id="<?php echo esc_attr( get_the_ID() ); ?>">
												<?php echo esc_html( $notify_me_label ); ?>
											</button>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endwhile; 
					wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="omsar-recruitment-item omsar-recruitment-empty-state omsar-recruitment-no-open-positions" data-status="open">
						<p class="omsar-recruitment-empty-message"><?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.' ) : 'There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
			<div class="omsar-recruitment-load-more-wrapper"<?php echo $has_more_open ? '' : ' style="display:none;"'; ?>>
				<button type="button" class="omsar-recruitment-load-more-btn" data-page="1" data-status="open" data-total="<?php echo (int) $total_open; ?>" data-has-more="<?php echo $has_more_open ? '1' : '0'; ?>"><?php echo esc_html( $load_more_label ); ?></button>
			</div>
		</div>

		<?php
		// Output Recruitment Notify Me Modal once per page (shared with carousel shortcode)
		if ( empty( $GLOBALS['omsar_recruitment_notify_modal_printed'] ) ) {
			$GLOBALS['omsar_recruitment_notify_modal_printed'] = true;
		?>
		<!-- Recruitment Notify Me Modal -->
		<div id="omsar-recruitment-notify-modal" class="omsar-recruitment-notify-modal" role="dialog" aria-labelledby="omsar-recruitment-notify-modal-title" aria-hidden="true">
			<div class="omsar-recruitment-notify-modal-overlay"></div>
			<div class="omsar-recruitment-notify-modal-content">
				<button type="button" class="omsar-recruitment-notify-modal-close" aria-label="<?php echo esc_attr( function_exists( 'pll__' ) ? pll__( 'Close' ) : 'Close' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="omsar-recruitment-notify-modal-body">
					<h2 id="omsar-recruitment-notify-modal-title" class="omsar-recruitment-notify-modal-title">
						<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Notify me' ) : 'Notify Me' ); ?>
					</h2>
					<p class="omsar-recruitment-notify-modal-message">
						<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Share your email to be notified when this position becomes available.' ) : 'Share your email to be notified when this position becomes available.' ); ?>
					</p>
					<form id="omsar-recruitment-notify-form" class="omsar-recruitment-notify-form">
						<input type="hidden" name="recruitment_id" id="omsar-recruitment-notify-recruitment-id" value="">
						<div class="omsar-recruitment-notify-form-group">
							<label for="omsar-recruitment-notify-email" class="omsar-recruitment-notify-label">
								<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Email Address' ) : 'Email Address' ); ?>
							</label>
							<input 
								type="email" 
								id="omsar-recruitment-notify-email" 
								name="email" 
								class="omsar-recruitment-notify-input" 
								required 
								aria-required="true"
								aria-invalid="false"
								aria-describedby="omsar-recruitment-notify-email-error"
							>
							<span id="omsar-recruitment-notify-email-error" class="omsar-recruitment-notify-error" role="alert" aria-live="polite"></span>
						</div>
						<div class="omsar-recruitment-notify-form-actions">
							<button type="submit" class="omsar-recruitment-notify-submit-btn">
								<span class="omsar-recruitment-notify-submit-text"><?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Submit' ) : 'Submit' ); ?></span>
								<span class="omsar-recruitment-notify-submit-loader" style="display: none;">
									<span class="spinner"></span>
									<?php echo esc_html( function_exists( 'pll__' ) ? pll__( 'Submitting...' ) : 'Submitting...' ); ?>
								</span>
							</button>
						</div>
						<div id="omsar-recruitment-notify-success" class="omsar-recruitment-notify-success" role="alert" aria-live="polite" style="display: none;"></div>
					</form>
				</div>
			</div>
		</div>
		<?php
		}
		?>

		<?php
	}
}

