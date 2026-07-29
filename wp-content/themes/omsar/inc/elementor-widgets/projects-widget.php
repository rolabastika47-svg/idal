<?php
/**
 * Elementor Projects Widget Class
 * 
 * Displays projects in a grid layout with filtering capabilities (Style 4)
 * 
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class OMSAR_Projects_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_projects';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Projects', 'omsar' );
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
		return [ 'projects', 'posts', 'grid', 'filter', 'list' ];
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
			'post_type',
			[
				'label' => esc_html__( 'Post Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_post_types(),
				'default' => 'post',
			]
		);

		// Build ACF fields options - needed for multiple controls
		$acf_fields_options = [ '' => esc_html__( 'Select Custom Field', 'omsar' ) ];
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$field_groups = acf_get_field_groups();
			foreach ( $field_groups as $field_group ) {
				$fields = acf_get_fields( $field_group['ID'] );
				if ( $fields ) {
					foreach ( $fields as $field ) {
						$field_label = ! empty( $field['label'] ) ? $field['label'] : $field['name'];
						$field_type = ! empty( $field['type'] ) ? $field['type'] : '';
						$label = $field_label;
						if ( $field_type ) {
							$label .= ' (' . $field_type . ')';
						}
						$acf_fields_options[ $field['name'] ] = $label;
					}
				}
			}
		}

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 9,
				'min' => 1,
				'max' => 100,
				'description' => esc_html__( 'Number of posts to display initially. If "Load More" is enabled, this is how many posts show per page.', 'omsar' ),
			]
		);

		$this->add_control(
			'enable_load_more',
			[
				'label' => esc_html__( 'Enable Load More Button', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Show a "Load More" button to load additional posts', 'omsar' ),
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label' => esc_html__( 'Load More Button Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Load More', 'omsar' ),
				'placeholder' => esc_html__( 'Enter button text', 'omsar' ),
				'condition' => [
					'enable_load_more' => 'yes',
				],
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
					'menu_order' => esc_html__( 'Menu Order', 'omsar' ),
					'rand' => esc_html__( 'Random', 'omsar' ),
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
			'enable_search',
			[
				'label' => esc_html__( 'Enable Search', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'search_placeholder',
			[
				'label' => esc_html__( 'Search Placeholder', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search projects...', 'omsar' ),
				'condition' => [
					'enable_search' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_label',
			[
				'label' => esc_html__( 'Search Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search:', 'omsar' ),
				'condition' => [
					'enable_search' => 'yes',
				],
			]
		);

		// Dropdown Filter Section
		$this->add_control(
			'enable_dropdown_filter',
			[
				'label' => esc_html__( 'Enable Dropdown Filter', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Add a dropdown filter beside the search bar', 'omsar' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'dropdown_filter_label',
			[
				'label' => esc_html__( 'Dropdown Label', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Filter:', 'omsar' ),
				'condition' => [
					'enable_dropdown_filter' => 'yes',
				],
			]
		);

		// Get all taxonomies for dropdown options
		$all_taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$dropdown_taxonomy_options = [ '' => esc_html__( 'Select Taxonomy', 'omsar' ) ];
		foreach ( $all_taxonomies as $tax ) {
			$dropdown_taxonomy_options[ $tax->name ] = $tax->label;
		}

		$this->add_control(
			'dropdown_filter_taxonomy',
			[
				'label' => esc_html__( 'Dropdown Taxonomy', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $dropdown_taxonomy_options,
				'description' => esc_html__( 'Select the taxonomy to populate dropdown options', 'omsar' ),
				'condition' => [
					'enable_dropdown_filter' => 'yes',
				],
			]
		);

		$this->add_control(
			'dropdown_filter_custom_field',
			[
				'label' => esc_html__( 'Filter by Custom Field', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'e.g., pillar_option', 'omsar' ),
				'description' => esc_html__( 'Enter the ACF custom field name to filter posts by. This field value will be matched against the taxonomy terms.', 'omsar' ),
				'condition' => [
					'enable_dropdown_filter' => 'yes',
					'dropdown_filter_taxonomy!' => '',
				],
			]
		);

		$this->add_control(
			'default_image',
			[
				'label' => esc_html__( 'Default Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__( 'Default image to use when a post has no featured image', 'omsar' ),
			]
		);

		// Pillar Badge Section
		$this->add_control(
			'pillar_heading',
			[
				'label' => esc_html__( 'Pillar Badge', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'badge_source',
			[
				'label' => esc_html__( 'Pillar Badge Source', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom_field',
				'options' => [
					'none' => esc_html__( 'None', 'omsar' ),
					'taxonomy' => esc_html__( 'Taxonomy', 'omsar' ),
					'custom_field' => esc_html__( 'ACF Custom Field', 'omsar' ),
				],
				'description' => esc_html__( 'Choose the source for pillar badge text (recommended: ACF Custom Field)', 'omsar' ),
			]
		);

		// Badge Taxonomy Control
		$taxonomy_options = [ '' => esc_html__( 'Select Taxonomy', 'omsar' ) ];
		$all_taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		foreach ( $all_taxonomies as $tax ) {
			$taxonomy_options[ $tax->name ] = $tax->label;
		}

		$this->add_control(
			'badge_taxonomy',
			[
				'label' => esc_html__( 'Badge Taxonomy', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $taxonomy_options,
				'condition' => [
					'badge_source' => 'taxonomy',
				],
			]
		);

		// Badge Custom Field Control
		$this->add_control(
			'badge_custom_field',
			[
				'label' => esc_html__( 'Pillar ACF Field', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pillar_option',
				'options' => $acf_fields_options,
				'description' => esc_html__( 'Select the ACF field that contains the pillar information (default: pillar_option)', 'omsar' ),
				'condition' => [
					'badge_source' => 'custom_field',
				],
			]
		);

		$this->add_control(
			'badge_taxonomy_display',
			[
				'label' => esc_html__( 'Display Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => esc_html__( 'Name', 'omsar' ),
					'id' => esc_html__( 'ID', 'omsar' ),
				],
				'condition' => [
					'badge_source' => 'custom_field',
					'badge_custom_field!' => '',
				],
			]
		);

		$this->add_control(
			'make_card_clickable',
			[
				'label' => esc_html__( 'Make Card Clickable', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Make the entire card clickable (clicking anywhere on the card will navigate to the project page)', 'omsar' ),
			]
		);

		$this->add_control(
			'show_read_more',
			[
				'label' => esc_html__( 'Show Read More Button', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display the read more button on project cards', 'omsar' ),
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Read More Button Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => function_exists( 'pll__' ) ? pll__( 'Read More' ) : esc_html__( 'Read More', 'omsar' ),
				'condition' => [
					'show_read_more' => 'yes',
				],
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

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label' => esc_html__( 'Grid Columns', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-projects-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Read More Button
		$this->start_controls_section(
			'style_read_more_section',
			[
				'label' => esc_html__( 'Read More Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'read_more_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_bg_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0073aa',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-post-read-more',
			]
		);

		$this->add_control(
			'read_more_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '12',
					'right' => '24',
					'bottom' => '12',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'read_more_border_radius',
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
					'{{WRAPPER}} .omsar-post-read-more' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'read_more_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-post-read-more',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'read_more_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-post-read-more',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'read_more_hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-post-read-more:hover',
			]
		);

		$this->add_control(
			'read_more_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms' ],
				'range' => [
					's' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
					'ms' => [
						'min' => 0,
						'max' => 1000,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-post-read-more' => 'transition: all {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Load More Button
		$this->start_controls_section(
			'style_load_more_section',
			[
				'label' => esc_html__( 'Load More Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_load_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_more_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_hover_bg_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0073aa',
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'load_more_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-load-more-btn',
			]
		);

		$this->add_control(
			'load_more_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '12',
					'right' => '24',
					'bottom' => '12',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'load_more_margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'load_more_border_radius',
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
					'{{WRAPPER}} .omsar-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'load_more_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-load-more-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-load-more-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-load-more-btn:hover',
			]
		);

		$this->add_control(
			'load_more_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms' ],
				'range' => [
					's' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
					'ms' => [
						'min' => 0,
						'max' => 1000,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'transition: all {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'load_more_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get available post types
	 */
	private function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$options = [];
		foreach ( $post_types as $post_type ) {
			$options[ $post_type->name ] = $post_type->label;
		}
		return $options;
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$post_type = ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post';
		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : 9;
		$orderby = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$default_image = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
		$badge_source = isset( $settings['badge_source'] ) ? $settings['badge_source'] : 'none';
		$badge_taxonomy = isset( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : '';
		$badge_custom_field = isset( $settings['badge_custom_field'] ) ? $settings['badge_custom_field'] : '';
		$badge_taxonomy_display = isset( $settings['badge_taxonomy_display'] ) ? $settings['badge_taxonomy_display'] : 'name';
		$make_card_clickable = isset( $settings['make_card_clickable'] ) && $settings['make_card_clickable'] === 'yes';
		$show_read_more = isset( $settings['show_read_more'] ) && $settings['show_read_more'] === 'yes';
		$read_more_text = isset( $settings['read_more_text'] ) ? $settings['read_more_text'] : ( function_exists( 'pll__' ) ? pll__( 'Read More' ) : esc_html__( 'Read More', 'omsar' ) );
		$enable_search = isset( $settings['enable_search'] ) && $settings['enable_search'] === 'yes';
		$search_placeholder = ! empty( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : esc_html__( 'Search projects...', 'omsar' );
		$search_label = isset( $settings['search_label'] ) ? $settings['search_label'] : esc_html__( 'Search:', 'omsar' );
		$enable_load_more = isset( $settings['enable_load_more'] ) && $settings['enable_load_more'] === 'yes';
		$load_more_text = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : esc_html__( 'Load More', 'omsar' );
		$enable_dropdown_filter = isset( $settings['enable_dropdown_filter'] ) && $settings['enable_dropdown_filter'] === 'yes';
		$dropdown_filter_label = ! empty( $settings['dropdown_filter_label'] ) ? $settings['dropdown_filter_label'] : esc_html__( 'Filter:', 'omsar' );
		$dropdown_filter_taxonomy = ! empty( $settings['dropdown_filter_taxonomy'] ) ? $settings['dropdown_filter_taxonomy'] : '';
		$dropdown_filter_custom_field = ! empty( $settings['dropdown_filter_custom_field'] ) ? $settings['dropdown_filter_custom_field'] : '';
		// If no filter field is specified but badge uses custom field, use badge field for filtering
		if ( empty( $dropdown_filter_custom_field ) && $badge_source === 'custom_field' && ! empty( $badge_custom_field ) ) {
			$dropdown_filter_custom_field = $badge_custom_field;
		}
		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '3';

		$widget_id = 'omsar-projects-' . $this->get_id();

		// Get all posts first
		$all_posts = get_posts( [
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => $orderby,
			'order' => $order,
			'suppress_filters' => false,
		] );

		?>

		<div class="omsar-projects-widget" id="<?php echo esc_attr( $widget_id ); ?>">
			
			<?php if ( $enable_search || $enable_dropdown_filter ): ?>
			<!-- Search and Filter Container -->
			<div class="omsar-style4-filters-container">
				<div class="omsar-style4-filters-row">
					<?php if ( $enable_search ): ?>
					<!-- Search Box -->
					<div class="omsar-style4-filter-item omsar-search-filter-item">
						<?php if ( ! empty( $search_label ) ): ?>
							<label for="<?php echo esc_attr( $widget_id ); ?>-search" class="omsar-style4-filter-label"><?php echo esc_html( $search_label ); ?></label>
						<?php endif; ?>
						<div class="omsar-posts-search-wrapper-inline">
							<input type="text" 
								   class="omsar-posts-search-input" 
								   id="<?php echo esc_attr( $widget_id ); ?>-search" 
								   placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
								   aria-label="<?php echo esc_attr__( 'Search projects', 'omsar' ); ?>">
							<i class="bi bi-search omsar-search-icon"></i>
						</div>
					</div>
					<?php endif; ?>
					
					<?php if ( $enable_dropdown_filter && ! empty( $dropdown_filter_taxonomy ) ): 
						// Get taxonomy terms for dropdown (including terms with 0 posts)
						$filter_terms = get_terms( [
							'taxonomy' => $dropdown_filter_taxonomy,
							'hide_empty' => false,
							'orderby' => 'name',
							'order' => 'ASC',
						] );
					?>
					<!-- Dropdown Filter -->
					<div class="omsar-style4-filter-item omsar-dropdown-filter-item">
						<?php if ( ! empty( $dropdown_filter_label ) ): ?>
							<label for="<?php echo esc_attr( $widget_id ); ?>-dropdown-filter" class="omsar-style4-filter-label"><?php echo esc_html( $dropdown_filter_label ); ?></label>
						<?php endif; ?>
						<div class="omsar-style4-dropdown-input-wrapper">
							<select class="omsar-style4-dropdown" id="<?php echo esc_attr( $widget_id ); ?>-dropdown-filter" aria-label="<?php echo esc_attr__( 'Filter projects', 'omsar' ); ?>">
								<option value=""><?php echo pll__( 'All' ); ?></option>
								<?php if ( ! is_wp_error( $filter_terms ) && ! empty( $filter_terms ) ): ?>
									<?php foreach ( $filter_terms as $term ): ?>
										<option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<i class="bi bi-chevron-down omsar-style4-dropdown-icon"></i>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Posts Grid -->
			<?php 
			// Limit posts if load more is enabled
			$displayed_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
			$has_more_posts = $enable_load_more && $posts_per_page > 0 && count( $all_posts ) > $posts_per_page;
			?>
			<div class="omsar-projects-grid omsar-posts-grid style4" 
				 data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
				 data-post-type="<?php echo esc_attr( $post_type ); ?>"
				 data-tab="all"
				 data-orderby="<?php echo esc_attr( $orderby ); ?>"
				 data-order="<?php echo esc_attr( $order ); ?>"
				 data-listing-style="style4"
				 data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
				 data-current-page="1"
				 data-total-posts="<?php echo count( $all_posts ); ?>"
				 data-default-image="<?php echo esc_url( $default_image ); ?>"
				 data-badge-source="<?php echo esc_attr( $badge_source ); ?>"
				 data-badge-taxonomy="<?php echo esc_attr( $badge_taxonomy ); ?>"
				 data-badge-custom-field="<?php echo esc_attr( $badge_custom_field ); ?>"
				 data-badge-taxonomy-display="<?php echo esc_attr( $badge_taxonomy_display ); ?>"
				 data-read-more-text="<?php echo esc_attr( $read_more_text ); ?>"
				 data-show-read-more="<?php echo $show_read_more ? 'yes' : 'no'; ?>"
				 data-dropdown-filter-taxonomy="<?php echo esc_attr( $enable_dropdown_filter && ! empty( $dropdown_filter_taxonomy ) ? $dropdown_filter_taxonomy : '' ); ?>"
				 data-dropdown-filter-custom-field="<?php echo esc_attr( $enable_dropdown_filter && ! empty( $dropdown_filter_custom_field ) ? $dropdown_filter_custom_field : '' ); ?>"
				 style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
				<?php foreach ( $displayed_posts as $post ): 
					setup_postdata( $post );
					$this->render_project_card( $post, $widget_id, $default_image, $badge_source, $badge_taxonomy, $badge_custom_field, $badge_taxonomy_display, $read_more_text, $enable_dropdown_filter && ! empty( $dropdown_filter_taxonomy ) ? 'taxonomy' : '', $dropdown_filter_taxonomy, '', $dropdown_filter_custom_field, $show_read_more, $make_card_clickable );
				endforeach;
				wp_reset_postdata();
				?>
			</div>
			<?php if ( $enable_load_more && $has_more_posts ): ?>
				<div class="omsar-load-more-wrapper" data-tab="all">
					<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
						<?php echo esc_html( $load_more_text ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>

		<?php
		// JavaScript for filtering is handled by style4-filters.js (enqueued globally)
	}

	/**
	 * Render a single project card
	 */
	protected function render_project_card( $post, $widget_id = '', $default_image = '', $badge_source = 'none', $badge_taxonomy = '', $badge_custom_field = '', $badge_taxonomy_display = 'name', $read_more_text = '', $filter_dropdown_source = '', $filter_dropdown_taxonomy = '', $filter_dropdown_acf_field = '', $filter_by_acf_field = '', $show_read_more = true, $make_card_clickable = true ) {
		$post_id = $post->ID;
		
		// Get featured image or default image
		$post_image = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$post_image = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( ! empty( $default_image ) ) {
			$post_image = $default_image;
		}
		
		$post_title = get_the_title( $post_id );
		$post_link = get_permalink( $post_id );
		
		// Get excerpt
		$post_excerpt = '';
		if ( has_excerpt( $post_id ) ) {
			$post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );
		} elseif ( ! empty( $post->post_content ) ) {
			$post_excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 20, '...' );
		}
		
		// Get full excerpt for search
		$post_excerpt_for_search = '';
		if ( has_excerpt( $post_id ) ) {
			$post_excerpt_for_search = strtolower( strip_tags( get_the_excerpt( $post_id ) ) );
		} elseif ( ! empty( $post->post_content ) ) {
			$post_excerpt_for_search = strtolower( strip_tags( wp_trim_words( strip_shortcodes( $post->post_content ), 50, '' ) ) );
		}
		
		// Get badge text
		$badge_text = $this->get_badge_text( $post_id, $badge_source, $badge_taxonomy, $badge_custom_field, $badge_taxonomy_display );

		// Get filter value for dropdown filter
		$filter_value = '';
		if ( ! empty( $filter_dropdown_source ) ) {
			if ( $filter_dropdown_source === 'taxonomy' && ! empty( $filter_dropdown_taxonomy ) ) {
				// If filter_by_acf_field is set, get value from ACF field and match to taxonomy term
				if ( ! empty( $filter_by_acf_field ) && function_exists( 'get_field' ) ) {
					$acf_field_value = get_field( $filter_by_acf_field, $post_id );
					$acf_field_type = $this->get_acf_field_type( $filter_by_acf_field );
					
					if ( ! empty( $acf_field_value ) ) {
						// If ACF field is taxonomy type, get term ID directly
						if ( $acf_field_type === 'taxonomy' ) {
							$term_id = null;
							
							if ( is_array( $acf_field_value ) ) {
								$term_item = reset( $acf_field_value );
								if ( is_object( $term_item ) && isset( $term_item->term_id ) ) {
									$term_id = $term_item->term_id;
								} elseif ( is_numeric( $term_item ) ) {
									$term_id = $term_item;
								}
							} elseif ( is_object( $acf_field_value ) && isset( $acf_field_value->term_id ) ) {
								$term_id = $acf_field_value->term_id;
							} elseif ( is_numeric( $acf_field_value ) ) {
								$term_id = $acf_field_value;
							}
							
							if ( $term_id ) {
								// Verify the term belongs to the filter taxonomy
								$term = get_term( $term_id );
								if ( $term && ! is_wp_error( $term ) && $term->taxonomy === $filter_dropdown_taxonomy ) {
									$filter_value = (string) $term_id;
								}
							}
						} else {
							// For non-taxonomy fields, get value as text and match by name
							$acf_value_text = $this->get_badge_text( $post_id, 'custom_field', '', $filter_by_acf_field, 'name' );
							
							if ( ! empty( $acf_value_text ) ) {
								// Find taxonomy term that matches the ACF field value by name
								$filter_terms = get_terms( [
									'taxonomy' => $filter_dropdown_taxonomy,
									'hide_empty' => false,
								] );
								
								if ( ! is_wp_error( $filter_terms ) && ! empty( $filter_terms ) ) {
									foreach ( $filter_terms as $term ) {
										if ( strcasecmp( $term->name, $acf_value_text ) === 0 ) {
											$filter_value = (string) $term->term_id;
											break;
										}
									}
								}
							}
						}
					}
				} else {
					// Default: get from taxonomy assignment
					$filter_terms = get_the_terms( $post_id, $filter_dropdown_taxonomy );
					if ( $filter_terms && ! is_wp_error( $filter_terms ) && ! empty( $filter_terms ) ) {
						$filter_term = array_shift( $filter_terms );
						$filter_value = (string) $filter_term->term_id;
					}
				}
			} elseif ( $filter_dropdown_source === 'acf_field' && ! empty( $filter_dropdown_acf_field ) && function_exists( 'get_field' ) ) {
				// Use the same method as get_badge_text to ensure consistency with dropdown options
				$filter_field_value = get_field( $filter_dropdown_acf_field, $post_id );
				$filter_field_type = $this->get_acf_field_type( $filter_dropdown_acf_field );
				
				if ( ! empty( $filter_field_value ) ) {
					if ( $filter_field_type === 'taxonomy' ) {
						$term = null;
						$term_id = null;
						
						if ( is_array( $filter_field_value ) ) {
							$term_item = reset( $filter_field_value );
							if ( is_object( $term_item ) && isset( $term_item->term_id ) ) {
								$term_id = $term_item->term_id;
							} elseif ( is_numeric( $term_item ) ) {
								$term_id = $term_item;
							}
							if ( $term_id ) {
								$term = get_term( $term_id );
							}
						} elseif ( is_object( $filter_field_value ) && isset( $filter_field_value->term_id ) ) {
							$term_id = $filter_field_value->term_id;
							$term = $filter_field_value;
						} elseif ( is_numeric( $filter_field_value ) ) {
							$term_id = $filter_field_value;
							$term = get_term( $term_id );
						}

						if ( $term && ! is_wp_error( $term ) ) {
							// Use term_id to match dropdown options
							$filter_value = (string) $term->term_id;
						} elseif ( $term_id ) {
							$filter_value = (string) $term_id;
						}
					} else {
						// For non-taxonomy fields, extract the value as a string
						// This should match how get_dropdown_options extracts values
						$extracted_value = '';
						if ( is_array( $filter_field_value ) && ! empty( $filter_field_value[0] ) ) {
							$extracted_value = is_object( $filter_field_value[0] ) ? ( $filter_field_value[0]->name ?? (string) $filter_field_value[0] ) : (string) $filter_field_value[0];
						} else {
							$extracted_value = is_object( $filter_field_value ) ? ( $filter_field_value->name ?? (string) $filter_field_value ) : (string) $filter_field_value;
						}
						$filter_value = ! empty( $extracted_value ) ? $extracted_value : '';
					}
				}
			}
		}
		
		// Build style attribute for background image
		$item_style = '';
		if ( ! empty( $post_image ) ) {
			$item_style = 'style="background-image: url(' . esc_url( $post_image ) . ');"';
		}

		// Build data attributes for filtering
		$filter_data_attrs = '';
		if ( ! empty( $filter_value ) ) {
			$filter_data_attrs = 'data-filter-value="' . esc_attr( $filter_value ) . '"';
		}
		
		// Card clickable class and attributes
		$card_clickable_class = $make_card_clickable ? 'omsar-card-clickable' : '';
		$card_clickable_attr = $make_card_clickable ? 'data-card-link="' . esc_url( $post_link ) . '"' : '';
		
		// Title link
		$title_link = $make_card_clickable ? 'javascript:void(0);' : esc_url( $post_link );
		$title_link_attr = $make_card_clickable ? 'onclick="return false;"' : '';
		
		// Read more link
		$read_more_link = $make_card_clickable ? 'javascript:void(0);' : esc_url( $post_link );
		$read_more_link_attr = $make_card_clickable ? 'onclick="return false;"' : '';
		?>
		<div class="omsar-post-card omsar-post-card-style4 <?php echo esc_attr( $card_clickable_class ); ?>" 
			 data-post-title="<?php echo esc_attr( strtolower( $post_title ) ); ?>" 
			 data-post-excerpt="<?php echo esc_attr( $post_excerpt_for_search ); ?>" 
			 data-post-link="<?php echo esc_url( $post_link ); ?>"
			 <?php echo $item_style; ?> 
			 <?php echo $filter_data_attrs; ?>
			 <?php echo $card_clickable_attr; ?>>
			<!-- Style 4: Related Posts Style with gradient overlay -->
			<div class="omsar-post-content">
				<?php if ( ! empty( $badge_text ) ): ?>
					<span class="omsar-post-badge"><?php echo esc_html( $badge_text ); ?></span>
				<?php endif; ?>
				
				<h3 class="omsar-post-title">
					<a href="<?php echo $title_link; ?>" <?php echo $title_link_attr; ?>>
						<?php echo esc_html( $post_title ); ?>
					</a>
				</h3>
				
				<?php if ( ! empty( $post_excerpt ) ): ?>
					<div class="omsar-post-excerpt">
						<?php echo wp_kses_post( $post_excerpt ); ?>
					</div>
				<?php endif; ?>
				
				<?php if ( $show_read_more ): ?>
					<a href="<?php echo $read_more_link; ?>" class="omsar-post-read-more" <?php echo $read_more_link_attr; ?>>
						<?php pll_e('Read More'); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get badge text based on source
	 */
	private function get_badge_text( $post_id, $source, $taxonomy = '', $custom_field = '', $taxonomy_display = 'name' ) {
		if ( $source === 'none' ) {
			return '';
		}

		if ( $source === 'taxonomy' && ! empty( $taxonomy ) ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term = array_shift( $terms );
				return $term->name;
			}
		}

		if ( $source === 'custom_field' && ! empty( $custom_field ) ) {
			if ( function_exists( 'get_field' ) ) {
				$value = get_field( $custom_field, $post_id );
			} else {
				$value = get_post_meta( $post_id, $custom_field, true );
			}

			if ( ! empty( $value ) ) {
				$field_type = $this->get_acf_field_type( $custom_field );
				
				// Handle taxonomy field type
				if ( $field_type === 'taxonomy' ) {
					$term = null;
					
					// Handle array of terms
					if ( is_array( $value ) ) {
						if ( ! empty( $value[0] ) ) {
							$first_value = $value[0];
							// Check if it's a term object
							if ( is_object( $first_value ) && isset( $first_value->term_id ) ) {
								$term = $first_value;
							} elseif ( is_object( $first_value ) && isset( $first_value->name ) ) {
								// Term object with name property
								$term = $first_value;
							} elseif ( is_numeric( $first_value ) ) {
								// Term ID
								$term = get_term( $first_value );
							} elseif ( is_array( $first_value ) && isset( $first_value['term_id'] ) ) {
								// Array with term_id
								$term = get_term( $first_value['term_id'] );
							} elseif ( is_array( $first_value ) && isset( $first_value['value'] ) ) {
								// ACF format with value key
								$term_id = is_numeric( $first_value['value'] ) ? $first_value['value'] : null;
								if ( $term_id ) {
									$term = get_term( $term_id );
								}
							}
						}
					} elseif ( is_object( $value ) ) {
						// Single term object
						if ( isset( $value->term_id ) ) {
							$term = $value;
						} elseif ( isset( $value->name ) ) {
							// Term object with name
							$term = $value;
						} elseif ( isset( $value->ID ) ) {
							// Might be term ID in object
							$term = get_term( $value->ID );
						}
					} elseif ( is_numeric( $value ) ) {
						// Term ID (numeric)
						$term = get_term( $value );
					} elseif ( is_string( $value ) && is_numeric( $value ) ) {
						// Term ID (string)
						$term = get_term( (int) $value );
					} elseif ( is_array( $value ) && isset( $value['term_id'] ) ) {
						// Array with term_id
						$term = get_term( $value['term_id'] );
					} elseif ( is_array( $value ) && isset( $value['value'] ) ) {
						// ACF format with value key
						$term_id = is_numeric( $value['value'] ) ? $value['value'] : null;
						if ( $term_id ) {
							$term = get_term( $term_id );
						}
					}

					// Extract term name
					if ( $term && ! is_wp_error( $term ) ) {
						return $taxonomy_display === 'id' ? (string) $term->term_id : $term->name;
					} elseif ( is_object( $value ) && isset( $value->name ) ) {
						// Fallback: direct name property
						return $value->name;
					}
				} else {
					// Handle non-taxonomy fields (text, select, etc.)
					if ( is_array( $value ) ) {
						// If array, try to extract meaningful values
						$text_values = [];
						foreach ( $value as $item ) {
							if ( is_object( $item ) ) {
								if ( isset( $item->name ) ) {
									$text_values[] = $item->name;
								} elseif ( isset( $item->post_title ) ) {
									$text_values[] = $item->post_title;
								} elseif ( isset( $item->label ) ) {
									$text_values[] = $item->label;
								} else {
									$text_values[] = (string) $item;
								}
							} else {
								$text_values[] = (string) $item;
							}
						}
						return implode( ', ', array_filter( $text_values ) );
					} elseif ( is_object( $value ) ) {
						// Object with various possible properties
						if ( isset( $value->name ) ) {
							return $value->name;
						} elseif ( isset( $value->post_title ) ) {
							return $value->post_title;
						} elseif ( isset( $value->label ) ) {
							return $value->label;
						} elseif ( isset( $value->title ) ) {
							return $value->title;
						}
						return (string) $value;
					}
					// Direct string/number value
					return (string) $value;
				}
			}
		}

		return '';
	}

	/**
	 * Get ACF field type
	 */
	private function get_acf_field_type( $field_name ) {
		if ( ! function_exists( 'acf_get_field_groups' ) || empty( $field_name ) ) {
			return '';
		}

		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group['ID'] );
			
			if ( $fields ) {
				foreach ( $fields as $field ) {
					if ( $field['name'] === $field_name ) {
						return ! empty( $field['type'] ) ? $field['type'] : '';
					}
				}
			}
		}

		return '';
	}

	/**
	 * Get taxonomy name from ACF field configuration
	 */
	private function get_acf_field_taxonomy( $field_name ) {
		if ( ! function_exists( 'acf_get_field_groups' ) || empty( $field_name ) ) {
			return '';
		}

		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group['ID'] );
			
			if ( $fields ) {
				foreach ( $fields as $field ) {
					if ( $field['name'] === $field_name && $field['type'] === 'taxonomy' ) {
						return ! empty( $field['taxonomy'] ) ? $field['taxonomy'] : '';
					}
				}
			}
		}

		return '';
	}

	/**
	 * Get term ID from ACF field value (for taxonomy fields)
	 * 
	 * @param mixed $acf_value The ACF field value
	 * @param string $field_name The ACF field name
	 * @return int|false Term ID or false if not found
	 */
	private function get_term_id_from_acf_value( $acf_value, $field_name = '' ) {
		if ( empty( $acf_value ) ) {
			return false;
		}

		// Handle array of terms
		if ( is_array( $acf_value ) ) {
			if ( ! empty( $acf_value[0] ) ) {
				$first_value = $acf_value[0];
				// Check if it's a term object
				if ( is_object( $first_value ) && isset( $first_value->term_id ) ) {
					return (int) $first_value->term_id;
				} elseif ( is_numeric( $first_value ) ) {
					return (int) $first_value;
				} elseif ( is_array( $first_value ) && isset( $first_value['term_id'] ) ) {
					return (int) $first_value['term_id'];
				} elseif ( is_array( $first_value ) && isset( $first_value['value'] ) ) {
					$term_id = is_numeric( $first_value['value'] ) ? (int) $first_value['value'] : null;
					return $term_id ? $term_id : false;
				}
			}
		} elseif ( is_object( $acf_value ) ) {
			// Single term object
			if ( isset( $acf_value->term_id ) ) {
				return (int) $acf_value->term_id;
			} elseif ( isset( $acf_value->ID ) ) {
				return (int) $acf_value->ID;
			}
		} elseif ( is_numeric( $acf_value ) ) {
			// Term ID (numeric)
			return (int) $acf_value;
		} elseif ( is_string( $acf_value ) && is_numeric( $acf_value ) ) {
			// Term ID (string)
			return (int) $acf_value;
		} elseif ( is_array( $acf_value ) && isset( $acf_value['term_id'] ) ) {
			// Array with term_id
			return (int) $acf_value['term_id'];
		} elseif ( is_array( $acf_value ) && isset( $acf_value['value'] ) ) {
			// ACF format with value key
			$term_id = is_numeric( $acf_value['value'] ) ? (int) $acf_value['value'] : null;
			return $term_id ? $term_id : false;
		}

		return false;
	}

	/**
	 * Check if two term IDs are translations of each other (Polylang)
	 * 
	 * @param int $term_id_1 First term ID
	 * @param int $term_id_2 Second term ID
	 * @return bool True if they are translations of each other
	 */
	private function are_terms_translations( $term_id_1, $term_id_2 ) {
		if ( ! function_exists( 'pll_get_term_translations' ) || empty( $term_id_1 ) || empty( $term_id_2 ) ) {
			return false;
		}

		if ( $term_id_1 === $term_id_2 ) {
			return true;
		}

		$translations_1 = pll_get_term_translations( $term_id_1 );
		$translations_2 = pll_get_term_translations( $term_id_2 );

		if ( empty( $translations_1 ) || empty( $translations_2 ) ) {
			return false;
		}

		// Check if any translation of term 1 matches term 2
		foreach ( $translations_1 as $lang_code => $translated_term_id ) {
			if ( $translated_term_id == $term_id_2 ) {
				return true;
			}
		}

		// Check if any translation of term 2 matches term 1
		foreach ( $translations_2 as $lang_code => $translated_term_id ) {
			if ( $translated_term_id == $term_id_1 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get translated tab name based on current language
	 * 
	 * @param string $tab_name The original tab name
	 * @param string $tabs_acf_field The ACF field name used for tabs
	 * @return string Translated tab name
	 */
	private function get_translated_tab_name( $tab_name, $tabs_acf_field = '' ) {
		// Check if Polylang is active
		if ( ! function_exists( 'pll_current_language' ) ) {
			return $tab_name;
		}

		$current_lang = pll_current_language();
		
		// If ACF field is specified, check if it's a taxonomy field
		if ( ! empty( $tabs_acf_field ) && function_exists( 'get_field' ) ) {
			$field_type = $this->get_acf_field_type( $tabs_acf_field );
			
			// If it's a taxonomy field, try to get the term in current language
			if ( $field_type === 'taxonomy' ) {
				// Get the taxonomy name from ACF field configuration
				$taxonomy = $this->get_acf_field_taxonomy( $tabs_acf_field );
				
				if ( ! empty( $taxonomy ) ) {
					// Get terms in current language from the specific taxonomy
					$term_args = [
						'taxonomy' => $taxonomy,
						'hide_empty' => false,
						'lang' => $current_lang,
					];
					
					$terms = get_terms( $term_args );
					
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							// Check if this term's name matches
							if ( $term->name === $tab_name ) {
								// Already in current language
								return $term->name;
							}
							
							// Check if any translation of this term matches the tab name
							if ( function_exists( 'pll_get_term_translations' ) ) {
								$term_translations = pll_get_term_translations( $term->term_id );
								
								if ( ! empty( $term_translations ) ) {
									foreach ( $term_translations as $lang_code => $term_id ) {
										$translated_term = get_term( $term_id );
										if ( $translated_term && ! is_wp_error( $translated_term ) && $translated_term->name === $tab_name ) {
											// Found the term, return its name in current language
											return $term->name;
										}
									}
								}
							}
						}
					}
				} else {
					// Fallback: search all taxonomies if taxonomy not found in ACF config
					$taxonomies = get_taxonomies( [ 'public' => true ] );
					
					foreach ( $taxonomies as $tax ) {
						$term_args = [
							'taxonomy' => $tax,
							'hide_empty' => false,
							'lang' => $current_lang,
						];
						
						$terms = get_terms( $term_args );
						
						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								if ( $term->name === $tab_name ) {
									return $term->name;
								}
								
								if ( function_exists( 'pll_get_term_translations' ) ) {
									$term_translations = pll_get_term_translations( $term->term_id );
									
									if ( ! empty( $term_translations ) ) {
										foreach ( $term_translations as $lang_code => $term_id ) {
											$translated_term = get_term( $term_id );
											if ( $translated_term && ! is_wp_error( $translated_term ) && $translated_term->name === $tab_name ) {
												return $term->name;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// For text values, try to use Polylang translation
		if ( function_exists( 'pll__' ) ) {
			$translated = pll__( $tab_name );
			if ( $translated !== $tab_name ) {
				return $translated;
			}
		}

		// Fallback: return original name
		return $tab_name;
	}

	/**
	 * Get dropdown options for filter
	 */
	private function get_dropdown_options( $source, $taxonomy = '', $acf_field = '', $post_type = 'post', $filter_by_acf_field = '' ) {
		$options = [];

		if ( $source === 'taxonomy' && ! empty( $taxonomy ) ) {
			$terms = get_terms( [
				'taxonomy' => $taxonomy,
				'hide_empty' => true,
				'orderby' => 'name',
				'order' => 'ASC',
			] );

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}
			}
		} elseif ( $source === 'acf_field' && ! empty( $acf_field ) && function_exists( 'get_field' ) ) {
			$all_posts = get_posts( [
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'post_status' => 'publish',
			] );

			$unique_values = [];
			$field_type = $this->get_acf_field_type( $acf_field );

			foreach ( $all_posts as $post ) {
				$value = get_field( $acf_field, $post->ID );
				
				if ( ! empty( $value ) ) {
					if ( $field_type === 'taxonomy' ) {
						$terms_to_process = is_array( $value ) ? $value : [ $value ];

						foreach ( $terms_to_process as $term_item ) {
							$term = null;
							
							if ( is_object( $term_item ) && isset( $term_item->term_id ) ) {
								$term = $term_item;
							} elseif ( is_numeric( $term_item ) ) {
								$term = get_term( $term_item );
							}

							if ( $term && ! is_wp_error( $term ) ) {
								$unique_values[ $term->term_id ] = $term->name;
							}
						}
					} else {
						if ( is_array( $value ) ) {
							foreach ( $value as $val ) {
								$val_str = is_object( $val ) ? ( $val->name ?? (string) $val ) : (string) $val;
								if ( ! empty( $val_str ) ) {
									$unique_values[ $val_str ] = $val_str;
								}
							}
						} else {
							$val_str = is_object( $value ) ? ( $value->name ?? (string) $value ) : (string) $value;
							if ( ! empty( $val_str ) ) {
								$unique_values[ $val_str ] = $val_str;
							}
						}
					}
				}
			}

			$options = $unique_values;
		}

		asort( $options );

		return $options;
	}

	/**
	 * Sort posts by tab order for "All" tab
	 * Groups posts by their ACF field value and sorts groups by tab order
	 */
	private function sort_posts_by_tab_order( $posts, $tabs, $tabs_acf_field, $orderby = 'date', $order = 'DESC' ) {
		if ( empty( $posts ) || empty( $tabs ) || empty( $tabs_acf_field ) ) {
			return $posts;
		}

		// Group posts by their ACF field value
		$grouped_posts = [];
		$posts_without_tab = [];

		foreach ( $posts as $post ) {
			$acf_value_text = $this->get_badge_text( $post->ID, 'custom_field', '', $tabs_acf_field, 'name' );
			
			if ( ! empty( $acf_value_text ) ) {
				// Find matching tab
				$matched = false;
				foreach ( $tabs as $tab ) {
					if ( strcasecmp( $acf_value_text, $tab->value ) === 0 ) {
						if ( ! isset( $grouped_posts[ $tab->value ] ) ) {
							$grouped_posts[ $tab->value ] = [];
						}
						$grouped_posts[ $tab->value ][] = $post;
						$matched = true;
						break;
					}
				}
				
				// If no matching tab found, add to "without tab" group
				if ( ! $matched ) {
					$posts_without_tab[] = $post;
				}
			} else {
				// Posts without ACF value go to the end
				$posts_without_tab[] = $post;
			}
		}

		// Sort posts within each group by the specified orderby/order
		foreach ( $grouped_posts as $tab_value => $group_posts ) {
			usort( $group_posts, function( $a, $b ) use ( $orderby, $order ) {
				$result = 0;
				
				switch ( $orderby ) {
					case 'date':
						$result = strtotime( $a->post_date ) - strtotime( $b->post_date );
						break;
					case 'title':
						$result = strcmp( $a->post_title, $b->post_title );
						break;
					case 'menu_order':
						$result = $a->menu_order - $b->menu_order;
						break;
					case 'rand':
						$result = rand( -1, 1 );
						break;
				}
				
				return $order === 'ASC' ? $result : -$result;
			} );
			
			$grouped_posts[ $tab_value ] = $group_posts;
		}

		// Sort posts without tab by the specified orderby/order
		if ( ! empty( $posts_without_tab ) ) {
			usort( $posts_without_tab, function( $a, $b ) use ( $orderby, $order ) {
				$result = 0;
				
				switch ( $orderby ) {
					case 'date':
						$result = strtotime( $a->post_date ) - strtotime( $b->post_date );
						break;
					case 'title':
						$result = strcmp( $a->post_title, $b->post_title );
						break;
					case 'menu_order':
						$result = $a->menu_order - $b->menu_order;
						break;
					case 'rand':
						$result = rand( -1, 1 );
						break;
				}
				
				return $order === 'ASC' ? $result : -$result;
			} );
		}

		// Rebuild sorted array following tab order
		$sorted_posts = [];
		
		// Add posts in tab order
		foreach ( $tabs as $tab ) {
			if ( isset( $grouped_posts[ $tab->value ] ) ) {
				$sorted_posts = array_merge( $sorted_posts, $grouped_posts[ $tab->value ] );
			}
		}
		
		// Add posts without matching tab at the end
		$sorted_posts = array_merge( $sorted_posts, $posts_without_tab );

		return $sorted_posts;
	}
}

