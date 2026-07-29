<?php
/**
 * Elementor Posts by Taxonomy Widget Class
 * 
 * Displays posts organized by taxonomy terms in horizontal tabs
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

class OMSAR_Posts_By_Taxonomy_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_posts_by_taxonomy';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'List Posts by Taxonomy', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';
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
		return [ 'posts', 'taxonomy', 'tabs', 'categories', 'list' ];
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

		// Get all registered taxonomies
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$taxonomy_options = [ '' => esc_html__( 'Select Taxonomy', 'omsar' ) ];
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_options[ $taxonomy->name ] = $taxonomy->label . ' (' . $taxonomy->name . ')';
		}

		$this->add_control(
			'taxonomy',
			[
				'label' => esc_html__( 'Taxonomy', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => $taxonomy_options,
				'default' => '',
				'description' => esc_html__( 'Select the taxonomy to use for tabs. Terms from this taxonomy will be displayed as tabs.', 'omsar' ),
			]
		);

		$this->add_control(
			'use_acf_field',
			[
				'label' => esc_html__( 'Use ACF Field for Filtering', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'If enabled, posts will be filtered by ACF custom field instead of taxonomy assignment', 'omsar' ),
				'condition' => [
					'taxonomy!' => '',
				],
			]
		);

		$this->add_control(
			'acf_field_name',
			[
				'label' => esc_html__( 'ACF Field Name', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'publication_category',
				'placeholder' => esc_html__( 'e.g., publication_category', 'omsar' ),
				'description' => esc_html__( 'Enter the ACF field name used to assign categories to posts', 'omsar' ),
				'condition' => [
					'taxonomy!' => '',
					'use_acf_field' => 'yes',
				],
			]
		);

		// Get all registered post types
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$post_type_options = [ '' => esc_html__( 'Select Post Type', 'omsar' ) ];
		foreach ( $post_types as $post_type ) {
			$post_type_options[ $post_type->name ] = $post_type->label . ' (' . $post_type->name . ')';
		}

		$this->add_control(
			'post_type',
			[
				'label' => esc_html__( 'Post Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => $post_type_options,
				'default' => 'post',
				'description' => esc_html__( 'Select the post type to display', 'omsar' ),
			]
		);

		$this->add_control(
			'show_empty_terms',
			[
				'label' => esc_html__( 'Show Terms with 0 Posts', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display taxonomy terms even if they have no posts', 'omsar' ),
			]
		);

		$this->add_control(
			'show_all_tab',
			[
				'label' => esc_html__( 'Show "All" Tab', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display an "All" tab showing all posts', 'omsar' ),
			]
		);

		$this->add_control(
			'hide_tabs_navigation',
			[
				'label' => esc_html__( 'Hide Tabs Navigation', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Hide the tabs navigation completely. When enabled, all posts will be displayed without tabs.', 'omsar' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'description' => esc_html__( 'Number of posts to display per tab initially.', 'omsar' ),
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
				'default' => 'no',
				'description' => esc_html__( 'Add a search box to filter posts by title', 'omsar' ),
			]
		);

		$this->add_control(
			'search_placeholder',
			[
				'label' => esc_html__( 'Search Placeholder', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search posts...', 'omsar' ),
				'placeholder' => esc_html__( 'Enter search placeholder text', 'omsar' ),
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
				'placeholder' => esc_html__( 'Enter search label text', 'omsar' ),
				'condition' => [
					'enable_search' => 'yes',
				],
			]
		);

		$this->add_control(
			'listing_style',
			[
				'label' => esc_html__( 'Listing Style', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'style1' => esc_html__( 'Style 1 - Grid (Image Top)', 'omsar' ),
					'style2' => esc_html__( 'Style 2 - Horizontal (Image Left)', 'omsar' ),
					'style3' => esc_html__( 'Style 3 - Content Above Image', 'omsar' ),
					'style5' => esc_html__( 'Style 5 - Modern Card with Categories', 'omsar' ),
				],
				'default' => 'style1',
				'description' => esc_html__( 'Choose how posts are displayed in the listing', 'omsar' ),
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => esc_html__( 'Show Date', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Enable to display the post publication date', 'omsar' ),
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
				'description' => esc_html__( 'Image to use when post has no featured image', 'omsar' ),
			]
		);

		$this->add_control(
			'clickable_cards',
			[
				'label' => esc_html__( 'Clickable Cards', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Make entire post cards clickable links', 'omsar' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'default' => '3',
				'description' => esc_html__( 'Number of columns to display posts in', 'omsar' ),
			]
		);

		// Style 5 Display Options
		$this->add_control(
			'style5_display_heading',
			[
				'label' => esc_html__( 'Style 5 Display Options', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_content_overlay',
			[
				'label' => esc_html__( 'Content Overlay on Image', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display content as overlay on top of the featured image background', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_side_layout',
			[
				'label' => esc_html__( 'Side Layout (Image Left)', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Display image on the left side and content on the right', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay!' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_show_categories',
			[
				'label' => esc_html__( 'Show Categories', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display post categories in Style 5', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_show_excerpt',
			[
				'label' => esc_html__( 'Show Description (Excerpt)', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display post excerpt/description in Style 5', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_show_date',
			[
				'label' => esc_html__( 'Show Publication Date', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display publication date in Style 5', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_show_author',
			[
				'label' => esc_html__( 'Show Author', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Display post author in Style 5', 'omsar' ),
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		// Style 5 Color Options
		$this->add_control(
			'style5_colors_heading',
			[
				'label' => esc_html__( 'Style 5 Colors', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'listing_style' => 'style5',
				],
			]
		);

		$this->add_control(
			'style5_title_color',
			[
				'label' => esc_html__( 'Title Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5-overlay-content .omsar-post-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .omsar-post-card-style5-overlay-content .omsar-post-title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_excerpt_color',
			[
				'label' => esc_html__( 'Description/Excerpt Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.95)',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5-overlay-content .omsar-post-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_category_bg_color',
			[
				'label' => esc_html__( 'Category Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0.2)',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5-overlay-content .omsar-post-category-tag' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_category_text_color',
			[
				'label' => esc_html__( 'Category Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5-overlay-content .omsar-post-category-tag' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay' => 'yes',
				],
			]
		);

		// Style 5 Normal Layout Colors (when overlay is disabled)
		$this->add_control(
			'style5_normal_title_color',
			[
				'label' => esc_html__( 'Title Color (Normal Layout)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5 .omsar-post-content .omsar-post-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .omsar-post-card-style5 .omsar-post-content .omsar-post-title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay!' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_normal_excerpt_color',
			[
				'label' => esc_html__( 'Description/Excerpt Color (Normal Layout)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5 .omsar-post-content .omsar-post-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay!' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_normal_category_bg_color',
			[
				'label' => esc_html__( 'Category Background Color (Normal Layout)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5 .omsar-post-content .omsar-post-category-tag' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay!' => 'yes',
				],
			]
		);

		$this->add_control(
			'style5_normal_category_text_color',
			[
				'label' => esc_html__( 'Category Text Color (Normal Layout)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-post-card-style5 .omsar-post-content .omsar-post-category-tag' => 'color: {{VALUE}};',
				],
				'condition' => [
					'listing_style' => 'style5',
					'style5_content_overlay!' => 'yes',
				],
			]
		);

		// Custom Field Filter Section
		$this->add_control(
			'custom_field_filter_heading',
			[
				'label' => esc_html__( 'Custom Field Filter', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'custom_field_meta_key',
			[
				'label' => esc_html__( 'Meta Key', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'e.g., post_type_option', 'omsar' ),
				'description' => esc_html__( 'Enter the custom field meta key to filter posts by', 'omsar' ),
			]
		);

		$this->add_control(
			'custom_field_meta_value',
			[
				'label' => esc_html__( 'Meta Value', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'e.g., news', 'omsar' ),
				'description' => esc_html__( 'Enter the meta value to filter posts. Only posts with this meta key matching this value will be displayed.', 'omsar' ),
				'condition' => [
					'custom_field_meta_key!' => '',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Tabs
		$this->start_controls_section(
			'style_tabs_section',
			[
				'label' => esc_html__( 'Tabs Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_alignment',
			[
				'label' => esc_html__( 'Tabs Alignment', 'omsar' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'omsar' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'omsar' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'omsar' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'flex-start',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_width_type',
			[
				'label' => esc_html__( 'Tab Width', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__( 'Auto', 'omsar' ),
					'fixed' => esc_html__( 'Fixed', 'omsar' ),
					'full' => esc_html__( 'Full Width', 'omsar' ),
				],
			]
		);

		$this->add_control(
			'tab_width',
			[
				'label' => esc_html__( 'Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 1,
						'max' => 30,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'condition' => [
					'tab_width_type' => 'fixed',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_width_full',
			[
				'label' => esc_html__( 'Full Width', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'tab_width_type' => 'full',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link' => 'flex: 1 1 0%;',
				],
			]
		);

		// Inactive Tab Colors
		$this->add_control(
			'tab_inactive_heading',
			[
				'label' => esc_html__( 'Inactive Tab', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_inactive_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#21759b',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:not(.active)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_inactive_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0e2e6',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:not(.active)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_inactive_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:not(.active)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_inactive_border_width',
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
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:not(.active)' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'tab_inactive_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
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
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:not(.active)' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Active Tab Colors
		$this->add_control(
			'tab_active_heading',
			[
				'label' => esc_html__( 'Active Tab', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_active_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5693ff',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_border_width',
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
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'tab_active_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
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
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_active_shadow_color',
			[
				'label' => esc_html__( 'Shadow Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(86, 147, 255, 0.3)',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active' => 'box-shadow: 0 4px 12px {{VALUE}};',
				],
			]
		);

		// Hover Tab Colors
		$this->add_control(
			'tab_hover_heading',
			[
				'label' => esc_html__( 'Hover State', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:hover:not(.active)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#d0d2d6',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:hover:not(.active)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:hover:not(.active)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_border_width',
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
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:hover:not(.active)' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'tab_hover_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
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
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link:hover:not(.active)' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_active_bg_color',
			[
				'label' => esc_html__( 'Active Tab Hover Background', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4a7cff',
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_hover_active_border_color',
			[
				'label' => esc_html__( 'Active Tab Hover Border', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link.active:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'label' => esc_html__( 'Tab Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-taxonomy-tabs .nav-link',
			]
		);

		$this->add_control(
			'tab_padding',
			[
				'label' => esc_html__( 'Tab Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '14',
					'right' => '28',
					'bottom' => '14',
					'left' => '28',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-taxonomy-tabs .nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'tab_gap',
			[
				'label' => esc_html__( 'Gap Between Tabs', 'omsar' ),
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
					'{{WRAPPER}} .omsar-taxonomy-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Search
		$this->start_controls_section(
			'style_search_section',
			[
				'label' => esc_html__( 'Search Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_search' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_alignment',
			[
				'label' => esc_html__( 'Search Bar Alignment', 'omsar' ),
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
			]
		);

		$this->add_control(
			'search_width',
			[
				'label' => esc_html__( 'Search Box Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 800,
						'step' => 10,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-posts-search-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'search_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0e2e6',
				'selectors' => [
					'{{WRAPPER}} .omsar-posts-search-input' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_border_color_focus',
			[
				'label' => esc_html__( 'Border Color (Focus)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5693ff',
				'selectors' => [
					'{{WRAPPER}} .omsar-posts-search-input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999',
				'selectors' => [
					'{{WRAPPER}} .omsar-search-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Posts
		$this->start_controls_section(
			'style_posts_section',
			[
				'label' => esc_html__( 'Posts Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'posts_per_row',
			[
				'label' => esc_html__( 'Posts Per Row', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'default' => '3',
				'description' => esc_html__( 'Number of posts to display per row', 'omsar' ),
				'selectors' => [
					'{{WRAPPER}} .omsar-posts-grid.style1' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					'{{WRAPPER}} .omsar-posts-grid.style3' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					'{{WRAPPER}} .omsar-posts-grid.style5' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
				'condition' => [
					'listing_style' => ['style1', 'style3', 'style5'],
				],
			]
		);

		$this->add_control(
			'posts_per_row_style2',
			[
				'label' => esc_html__( 'Posts Per Row', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'default' => '1',
				'description' => esc_html__( 'Number of posts to display per row (Style 2 uses horizontal layout)', 'omsar' ),
				'selectors' => [
					'{{WRAPPER}} .omsar-posts-grid.style2' => 'grid-template-columns: repeat({{VALUE}}, 1fr) !important;',
				],
				'condition' => [
					'listing_style' => 'style2',
				],
			]
		);

		$this->add_control(
			'image_fit',
			[
				'label' => esc_html__( 'Image Fit', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'cover' => esc_html__( 'Cover', 'omsar' ),
					'contain' => esc_html__( 'Contain', 'omsar' ),
				],
				'default' => 'cover',
				'description' => esc_html__( 'Choose how the image should fit within its container', 'omsar' ),
				'selectors' => [
					'{{WRAPPER}} .omsar-post-image img' => 'object-fit: {{VALUE}};',
					'{{WRAPPER}} .omsar-post-image-header img' => 'object-fit: {{VALUE}};',
					'{{WRAPPER}} .omsar-post-card-style5-overlay' => 'background-size: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		// Style Section - Load More Button
		$this->start_controls_section(
			'style_load_more_section',
			[
				'label' => esc_html__( 'Load More Button', 'omsar' ),
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
				'default' => '#5693ff',
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
				'default' => '#4a7cff',
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

		$this->add_responsive_control(
			'load_more_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '14',
					'right' => '32',
					'bottom' => '14',
					'left' => '32',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'load_more_border_radius',
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
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_width_type',
			[
				'label' => esc_html__( 'Width', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__( 'Auto', 'omsar' ),
					'full' => esc_html__( 'Full Width', 'omsar' ),
					'custom' => esc_html__( 'Custom', 'omsar' ),
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'width: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'auto' => 'auto',
					'full' => '100%',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_custom_width',
			[
				'label' => esc_html__( 'Custom Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 1,
						'max' => 50,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'condition' => [
					'load_more_width_type' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-btn' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
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
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-load-more-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Filter posts by custom field (post meta)
	 * 
	 * @param array $posts Array of post objects.
	 * @param string $meta_key Meta key to filter by.
	 * @param string $meta_value Meta value to match.
	 * @return array Filtered array of post objects.
	 */
	protected function filter_posts_by_custom_field( $posts, $meta_key, $meta_value ) {
		if ( empty( $meta_key ) || empty( $meta_value ) ) {
			return $posts;
		}

		$filtered_posts = [];
		foreach ( $posts as $post ) {
			$post_meta_value = get_post_meta( $post->ID, $meta_key, true );
			
			// Handle different meta value types
			if ( is_array( $post_meta_value ) ) {
				// If meta value is array, check if meta_value is in array
				if ( in_array( $meta_value, $post_meta_value, true ) ) {
					$filtered_posts[] = $post;
				}
			} else {
				// Direct comparison
				if ( (string) $post_meta_value === (string) $meta_value ) {
					$filtered_posts[] = $post;
				}
			}
		}

		return $filtered_posts;
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		if ( function_exists( 'omsar_bd_render_posts_by_taxonomy_widget' ) ) {
			omsar_bd_render_posts_by_taxonomy_widget( $this->get_settings_for_display(), (string) $this->get_id() );
			return;
		}

		$settings = $this->get_settings_for_display();

		$taxonomy = ! empty( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';
		$post_type = ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post';
		$show_empty = $settings['show_empty_terms'] === 'yes';
		$show_all = $settings['show_all_tab'] === 'yes';
		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : -1;
		$orderby = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$use_acf = $settings['use_acf_field'] === 'yes';
		$acf_field_name = ! empty( $settings['acf_field_name'] ) ? $settings['acf_field_name'] : 'publication_category';
		$enable_search = $settings['enable_search'] === 'yes';
		$search_placeholder = ! empty( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : esc_html__( 'Search posts...', 'omsar' );
		$search_label = isset( $settings['search_label'] ) ? $settings['search_label'] : esc_html__( 'Search:', 'omsar' );
		$search_alignment = ! empty( $settings['search_alignment'] ) ? $settings['search_alignment'] : 'left';
		$listing_style = ! empty( $settings['listing_style'] ) ? $settings['listing_style'] : 'style1';
		$clickable_cards = isset( $settings['clickable_cards'] ) && $settings['clickable_cards'] === 'yes';
		$enable_load_more = isset( $settings['enable_load_more'] ) && $settings['enable_load_more'] === 'yes';
		$load_more_text = ! empty( $settings['load_more_text'] ) ? $settings['load_more_text'] : esc_html__( 'Load More', 'omsar' );
		$default_image = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
		$show_date = isset( $settings['show_date'] ) && $settings['show_date'] === 'yes';
		$hide_tabs = isset( $settings['hide_tabs_navigation'] ) && $settings['hide_tabs_navigation'] === 'yes';
		$columns = ! empty( $settings['columns'] ) ? intval( $settings['columns'] ) : 3;
		$custom_field_meta_key = ! empty( $settings['custom_field_meta_key'] ) ? $settings['custom_field_meta_key'] : '';
		$custom_field_meta_value = ! empty( $settings['custom_field_meta_value'] ) ? $settings['custom_field_meta_value'] : '';
		
		// Style 5 display options
		$style5_content_overlay = isset( $settings['style5_content_overlay'] ) && $settings['style5_content_overlay'] === 'yes';
		$style5_side_layout = isset( $settings['style5_side_layout'] ) && $settings['style5_side_layout'] === 'yes';
		$style5_show_categories = isset( $settings['style5_show_categories'] ) && $settings['style5_show_categories'] === 'yes';
		$style5_show_excerpt = isset( $settings['style5_show_excerpt'] ) && $settings['style5_show_excerpt'] === 'yes';
		$style5_show_date = isset( $settings['style5_show_date'] ) && $settings['style5_show_date'] === 'yes';
		$style5_show_author = isset( $settings['style5_show_author'] ) && $settings['style5_show_author'] === 'yes';
		
		// If taxonomy is empty, automatically hide tabs and show all posts
		if ( empty( $taxonomy ) ) {
			$hide_tabs = true;
		}

		// Get all terms from the selected taxonomy (for tabs) - only if taxonomy is set and tabs are not hidden
		$terms = [];
		if ( ! empty( $taxonomy ) && ! $hide_tabs ) {
			$term_args = [
				'taxonomy' => $taxonomy,
				'hide_empty' => ! $show_empty,
				'orderby' => 'name',
				'order' => 'ASC',
			];
			
			// If Polylang is active, get terms for current language
			// But also include translations to ensure we can match posts correctly
			if ( function_exists( 'pll_current_language' ) ) {
				$current_lang = pll_current_language();
				if ( $current_lang ) {
					$term_args['lang'] = $current_lang;
				}
			}
			
			$terms = get_terms( $term_args );

			if ( is_wp_error( $terms ) ) {
				echo '<p>' . esc_html__( 'Error retrieving taxonomy terms: ', 'omsar' ) . $terms->get_error_message() . '</p>';
				return;
			}

			if ( empty( $terms ) && ! $show_empty ) {
				echo '<p>' . esc_html__( 'No taxonomy terms found for taxonomy: ', 'omsar' ) . esc_html( $taxonomy ) . '</p>';
				return;
			}
		}

		// If using ACF, we need to check which terms actually have posts assigned via ACF
		// Only process if we have taxonomy and not hiding tabs
		if ( ! empty( $taxonomy ) && ! $hide_tabs && $use_acf && function_exists( 'get_field' ) ) {
			// Get all posts of the post type
			$all_posts_check = get_posts( [
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'post_status' => 'publish',
			] );

			// Collect unique category values from ACF fields
			$acf_categories_found = [];
			foreach ( $all_posts_check as $post_check ) {
				$acf_category = get_field( $acf_field_name, $post_check->ID );
				if ( ! empty( $acf_category ) ) {
					if ( is_array( $acf_category ) ) {
						foreach ( $acf_category as $cat ) {
							$cat_name = is_array( $cat ) ? ( $cat['label'] ?? $cat['value'] ?? '' ) : $cat;
							if ( ! empty( $cat_name ) && ! in_array( $cat_name, $acf_categories_found ) ) {
								$acf_categories_found[] = $cat_name;
							}
						}
					} else {
						$cat_name = is_array( $acf_category ) ? ( $acf_category['label'] ?? $acf_category['value'] ?? '' ) : $acf_category;
						if ( ! empty( $cat_name ) && ! in_array( $cat_name, $acf_categories_found ) ) {
							$acf_categories_found[] = $cat_name;
						}
					}
				}
			}

			// Filter terms to only include those that match ACF categories or show all if show_empty is enabled
			if ( ! $show_empty ) {
				$terms = array_filter( $terms, function( $term ) use ( $acf_categories_found ) {
					return in_array( $term->name, $acf_categories_found );
				} );
				$terms = array_values( $terms ); // Re-index array
			}
		}

		// Get all posts for "All" tab or when tabs are hidden
		// If load more is enabled, get all posts first, then limit display
		$all_posts = [];
		if ( $show_all || $hide_tabs || empty( $taxonomy ) ) {
			$all_posts_args = [
				'post_type' => $post_type,
				'posts_per_page' => $enable_load_more ? -1 : $posts_per_page,
				'post_status' => 'publish',
				'orderby' => $orderby,
				'order' => $order,
				'suppress_filters' => false,
				'no_found_rows' => true,
			];
			
			// Add meta query if custom field filter is set
			if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
				$all_posts_args['meta_query'] = [
					[
						'key' => $custom_field_meta_key,
						'value' => $custom_field_meta_value,
						'compare' => '=',
					],
				];
			}
			
			// Use WP_Query for better Polylang support
			$all_posts_query_obj = new WP_Query( $all_posts_args );
			$all_posts = $all_posts_query_obj->posts;
			
			// Additional filtering for array meta values (meta_query doesn't handle arrays well)
			if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
				$all_posts = $this->filter_posts_by_custom_field( $all_posts, $custom_field_meta_key, $custom_field_meta_value );
			}
		}

		$widget_id = 'omsar-posts-taxonomy-' . $this->get_id();
		?>

		<div class="omsar-posts-by-taxonomy-widget" id="<?php echo esc_attr( $widget_id ); ?>">
			
			<?php if ( $enable_search ): ?>
			<!-- Search Box (for non-Style 4) -->
			<div class="omsar-posts-search-wrapper omsar-search-align-<?php echo esc_attr( $search_alignment ); ?>">
				<?php if ( ! empty( $search_label ) ): ?>
					<label for="<?php echo esc_attr( $widget_id ); ?>-search" class="omsar-posts-search-label"><?php echo esc_html( $search_label ); ?></label>
				<?php endif; ?>
				<input type="text" 
					   class="omsar-posts-search-input" 
					   id="<?php echo esc_attr( $widget_id ); ?>-search" 
					   placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
					   aria-label="<?php echo esc_attr__( 'Search posts', 'omsar' ); ?>">
				<i class="bi bi-search omsar-search-icon"></i>
			</div>
			<?php endif; ?>

			<!-- Tabs Navigation -->
			<?php if ( ! $hide_tabs && ! empty( $taxonomy ) && ! empty( $terms ) ): ?>
			<div class="omsar-taxonomy-tabs-nav">
				<ul class="nav nav-tabs omsar-taxonomy-tabs" role="tablist">
					<?php if ( $show_all && ! empty( $all_posts ) ): ?>
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="<?php echo esc_attr( $widget_id ); ?>-all-tab" 
									data-bs-toggle="tab" 
									data-bs-target="#<?php echo esc_attr( $widget_id ); ?>-all" 
									type="button" 
									role="tab">
								<?php echo function_exists( 'pll__' ) ? pll__( 'All' ) : esc_html__( 'All', 'omsar' ); ?>
							</button>
						</li>
					<?php endif; ?>
					
					<?php foreach ( $terms as $index => $term ): 
						$term_slug = sanitize_title( $term->slug );
						$is_first = ( $index === 0 && ( ! $show_all || empty( $all_posts ) ) );
					?>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php echo $is_first ? 'active' : ''; ?>" 
									id="<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>-tab" 
									data-bs-toggle="tab" 
									data-bs-target="#<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>" 
									type="button" 
									role="tab">
								<?php echo esc_html( $term->name ); ?>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>


			<!-- Tabs Content -->
			<?php if ( $hide_tabs || empty( $taxonomy ) || empty( $terms ) ): ?>
				<!-- Show all posts directly when tabs are hidden -->
				<?php 
				// When tabs are hidden, show all posts
				$displayed_all_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
				$has_more_all_posts = $enable_load_more && $posts_per_page > 0 && count( $all_posts ) > $posts_per_page;
				?>
				<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>" 
					 style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);"
					 data-tab="all"
					 data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
					 data-post-type="<?php echo esc_attr( $post_type ); ?>"
					 data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
					 data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
					 data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
					 data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
					 data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
					 data-orderby="<?php echo esc_attr( $orderby ); ?>"
					 data-order="<?php echo esc_attr( $order ); ?>"
					 data-default-image="<?php echo esc_url( $default_image ); ?>"
					 data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
					 data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
					 data-columns="<?php echo esc_attr( $columns ); ?>"
					 data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
					 data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
					 data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
					 data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
					 data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
					 data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
					 data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
					 data-current-page="1"
					 data-total-posts="<?php echo count( $all_posts ); ?>">
					<?php foreach ( $displayed_all_posts as $post ): 
						setup_postdata( $post );
						$this->render_post_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
					endforeach;
					wp_reset_postdata();
					?>
				</div>
				<?php if ( $enable_load_more && $has_more_all_posts ): ?>
					<div class="omsar-load-more-wrapper" data-tab="all">
						<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
							<?php echo esc_html( $load_more_text ); ?>
						</button>
					</div>
				<?php endif; ?>
			<?php else: ?>
			<div class="tab-content omsar-taxonomy-tabs-content">
				
				<!-- All Posts Tab -->
				<?php if ( $show_all && ! empty( $all_posts ) ): 
					// Limit posts if load more is enabled
					$displayed_all_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $all_posts, 0, $posts_per_page ) : $all_posts;
					$has_more_all_posts = $enable_load_more && $posts_per_page > 0 && count( $all_posts ) > $posts_per_page;
				?>
					<div class="tab-pane fade show active" 
						 id="<?php echo esc_attr( $widget_id ); ?>-all" 
						 role="tabpanel">
						<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>" 
							 style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);"
							 data-tab="all"
							 data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
							 data-post-type="<?php echo esc_attr( $post_type ); ?>"
							 data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
							 data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
							 data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
							 data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
							 data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
							 data-orderby="<?php echo esc_attr( $orderby ); ?>"
							 data-order="<?php echo esc_attr( $order ); ?>"
							 data-default-image="<?php echo esc_url( $default_image ); ?>"
							 data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
							 data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
							 data-columns="<?php echo esc_attr( $columns ); ?>"
							 data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
							 data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
							 data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
							 data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
							 data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
							 data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
							 data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
							 data-current-page="1"
							 data-total-posts="<?php echo count( $all_posts ); ?>">
								<?php foreach ( $displayed_all_posts as $post ): 
								setup_postdata( $post );
								$this->render_post_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
							endforeach;
							wp_reset_postdata();
							?>
						</div>
							<?php if ( $enable_load_more && $has_more_all_posts ): ?>
								<div class="omsar-load-more-wrapper" data-tab="all">
									<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="all">
										<?php echo esc_html( $load_more_text ); ?>
									</button>
								</div>
							<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Term Tabs -->
				<?php foreach ( $terms as $index => $term ): 
					$term_slug = sanitize_title( $term->slug );
					$is_first = ( $index === 0 && ( ! $show_all || empty( $all_posts ) ) );
					
					// Get posts for this term
					if ( $use_acf && function_exists( 'get_field' ) ) {
						// Filter by ACF field value matching term ID
						$all_posts_for_filter_args = [
							'post_type' => $post_type,
							'posts_per_page' => -1,
							'post_status' => 'publish',
						];
						
						// Add meta query if custom field filter is set
						if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
							$all_posts_for_filter_args['meta_query'] = [
								[
									'key' => $custom_field_meta_key,
									'value' => $custom_field_meta_value,
									'compare' => '=',
								],
							];
						}
						
						$all_posts_for_filter = get_posts( $all_posts_for_filter_args );
						
						$term_posts = [];
						foreach ( $all_posts_for_filter as $post_filter ) {
							$acf_category = get_field( $acf_field_name, $post_filter->ID );
							$matches = false;
							
							if ( ! empty( $acf_category ) ) {
								// Handle taxonomy term object from ACF
								if ( is_object( $acf_category ) && isset( $acf_category->term_id ) ) {
									// ACF returns taxonomy term object - compare by term_id
									if ( $acf_category->term_id == $term->term_id ) {
										$matches = true;
									}
								} elseif ( is_array( $acf_category ) ) {
									foreach ( $acf_category as $cat ) {
										// Handle term object
										if ( is_object( $cat ) && isset( $cat->term_id ) ) {
											if ( $cat->term_id == $term->term_id ) {
												$matches = true;
												break;
											}
										} elseif ( is_object( $cat ) && isset( $cat->name ) ) {
											// Fallback: compare by name
											if ( $cat->name === $term->name || $cat->term_id == $term->term_id ) {
												$matches = true;
												break;
											}
										} else {
											// Handle array or string - could be term ID or name
											$cat_value = is_array( $cat ) ? ( $cat['value'] ?? $cat['label'] ?? '' ) : $cat;
											// Compare as term ID (numeric) or name (string)
											if ( $cat_value == $term->term_id || $cat_value === $term->name ) {
												$matches = true;
												break;
											}
										}
									}
								} else {
									// Handle string/integer - ACF field stores term ID as number
									// Compare directly with term_id (most common case)
									if ( $acf_category == $term->term_id ) {
										$matches = true;
									} elseif ( is_object( $acf_category ) && isset( $acf_category->term_id ) ) {
										// Fallback: term object
										if ( $acf_category->term_id == $term->term_id ) {
											$matches = true;
										}
									} else {
										// Last resort: compare as string/name
										$cat_value = is_array( $acf_category ) ? ( $acf_category['value'] ?? $acf_category['label'] ?? '' ) : $acf_category;
										if ( $cat_value == $term->term_id || $cat_value === $term->name ) {
											$matches = true;
										}
									}
								}
							}
							
							if ( $matches ) {
								$term_posts[] = $post_filter;
							}
						}
						
						// Apply custom field filter if set
						if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
							$term_posts = $this->filter_posts_by_custom_field( $term_posts, $custom_field_meta_key, $custom_field_meta_value );
						}
						
						// Sort posts
						if ( $orderby === 'date' ) {
							usort( $term_posts, function( $a, $b ) use ( $order ) {
								$date_a = strtotime( $a->post_date );
								$date_b = strtotime( $b->post_date );
								return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
							} );
						} elseif ( $orderby === 'title' ) {
							usort( $term_posts, function( $a, $b ) use ( $order ) {
								$cmp = strcasecmp( $a->post_title, $b->post_title );
								return $order === 'DESC' ? -$cmp : $cmp;
							} );
						}
						
						// Don't limit here if load more is enabled - we'll limit in display
					} else {
						// Use standard taxonomy query
						// If load more is enabled, get all posts first
						
						// First, try with WP_Query for better Polylang compatibility
						$query_args = [
							'post_type' => $post_type,
							'posts_per_page' => $enable_load_more ? -1 : $posts_per_page,
							'post_status' => 'publish',
							'tax_query' => [
								[
									'taxonomy' => $taxonomy,
									'field' => 'term_id',
									'terms' => $term->term_id,
									'include_children' => false,
								],
							],
							'orderby' => $orderby,
							'order' => $order,
							'suppress_filters' => false,
							'no_found_rows' => true,
						];
						
						// Add meta query if custom field filter is set
						if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
							$query_args['meta_query'] = [
								[
									'key' => $custom_field_meta_key,
									'value' => $custom_field_meta_value,
									'compare' => '=',
								],
							];
						}
						
						// Use WP_Query instead of get_posts for better Polylang support
						$term_query = new WP_Query( $query_args );
						$term_posts = $term_query->posts;
						
						// Apply custom field filter if set (for array meta values)
						if ( ! empty( $custom_field_meta_key ) && ! empty( $custom_field_meta_value ) ) {
							$term_posts = $this->filter_posts_by_custom_field( $term_posts, $custom_field_meta_key, $custom_field_meta_value );
						}
						
						// If no posts found and Polylang is active, try getting all language versions of the term
						if ( empty( $term_posts ) && function_exists( 'pll_get_term_translations' ) ) {
							$term_translations = pll_get_term_translations( $term->term_id );
							if ( ! empty( $term_translations ) && count( $term_translations ) > 1 ) {
								// Get all term IDs (all language versions)
								$all_term_ids = array_values( $term_translations );
								$query_args['tax_query'][0]['terms'] = $all_term_ids;
								$term_query = new WP_Query( $query_args );
								$term_posts = $term_query->posts;
							}
						}
						
						// If still no posts, try with slug instead of term_id
						if ( empty( $term_posts ) ) {
							$query_args['tax_query'][0]['field'] = 'slug';
							$query_args['tax_query'][0]['terms'] = $term->slug;
							$term_query = new WP_Query( $query_args );
							$term_posts = $term_query->posts;
						}
						
						// Last resort: Get all posts and filter manually by checking their terms
						if ( empty( $term_posts ) ) {
							$all_posts_check = get_posts( [
								'post_type' => $post_type,
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'suppress_filters' => false,
							] );
							
							$term_posts = [];
							// Get all term IDs including translations
							$term_ids_to_check = [ $term->term_id ];
							if ( function_exists( 'pll_get_term_translations' ) ) {
								$term_translations = pll_get_term_translations( $term->term_id );
								if ( ! empty( $term_translations ) ) {
									$term_ids_to_check = array_merge( $term_ids_to_check, array_values( $term_translations ) );
								}
							}
							
							foreach ( $all_posts_check as $post_check ) {
								$post_terms = wp_get_post_terms( $post_check->ID, $taxonomy, [ 'fields' => 'ids' ] );
								
								// Check if post has any of the term IDs (including translations)
								$has_term = ! empty( array_intersect( $post_terms, $term_ids_to_check ) );
								if ( $has_term ) {
									$term_posts[] = $post_check;
								}
							}
							
							// Sort the manually filtered posts
							if ( $orderby === 'date' ) {
								usort( $term_posts, function( $a, $b ) use ( $order ) {
									$date_a = strtotime( $a->post_date );
									$date_b = strtotime( $b->post_date );
									return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
								} );
							} elseif ( $orderby === 'title' ) {
								usort( $term_posts, function( $a, $b ) use ( $order ) {
									$cmp = strcasecmp( $a->post_title, $b->post_title );
									return $order === 'DESC' ? -$cmp : $cmp;
								} );
							}
						}
					}
				?>
					<div class="tab-pane fade <?php echo $is_first ? 'show active' : ''; ?>" 
						 id="<?php echo esc_attr( $widget_id ); ?>-<?php echo esc_attr( $term_slug ); ?>" 
						 role="tabpanel">
						<?php if ( ! empty( $term_posts ) ): 
							// Limit posts if load more is enabled
							$displayed_term_posts = $enable_load_more && $posts_per_page > 0 ? array_slice( $term_posts, 0, $posts_per_page ) : $term_posts;
							$has_more_term_posts = $enable_load_more && $posts_per_page > 0 && count( $term_posts ) > $posts_per_page;
							$term_id_for_acf = $use_acf ? $term->term_id : $term->term_id;
						?>
							<div class="omsar-posts-grid <?php echo esc_attr( $listing_style ); ?>" 
								 style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);"
								 data-tab="<?php echo esc_attr( $term_slug ); ?>"
								 data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
								 data-post-type="<?php echo esc_attr( $post_type ); ?>"
								 data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
								 data-term-id="<?php echo esc_attr( $term->term_id ); ?>"
								 data-term-name="<?php echo esc_attr( $term->name ); ?>"
								 data-use-acf="<?php echo $use_acf ? '1' : '0'; ?>"
								 data-acf-field="<?php echo esc_attr( $acf_field_name ); ?>"
								 data-listing-style="<?php echo esc_attr( $listing_style ); ?>"
								 data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
								 data-orderby="<?php echo esc_attr( $orderby ); ?>"
								 data-order="<?php echo esc_attr( $order ); ?>"
								 data-default-image="<?php echo esc_url( $default_image ); ?>"
								 data-custom-field-meta-key="<?php echo esc_attr( $custom_field_meta_key ); ?>"
								 data-custom-field-meta-value="<?php echo esc_attr( $custom_field_meta_value ); ?>"
								 data-columns="<?php echo esc_attr( $columns ); ?>"
								 data-style5-show-categories="<?php echo $style5_show_categories ? '1' : '0'; ?>"
								 data-style5-show-excerpt="<?php echo $style5_show_excerpt ? '1' : '0'; ?>"
								 data-style5-show-date="<?php echo $style5_show_date ? '1' : '0'; ?>"
								 data-style5-show-author="<?php echo $style5_show_author ? '1' : '0'; ?>"
								 data-style5-content-overlay="<?php echo $style5_content_overlay ? '1' : '0'; ?>"
								 data-style5-side-layout="<?php echo $style5_side_layout ? '1' : '0'; ?>"
								 data-clickable-cards="<?php echo $clickable_cards ? '1' : '0'; ?>"
								 data-current-page="1"
								 data-total-posts="<?php echo count( $term_posts ); ?>">
								<?php foreach ( $displayed_term_posts as $post ): 
									setup_postdata( $post );
									$this->render_post_card( $post, $widget_id, $listing_style, $clickable_cards, $default_image, $show_date, $style5_show_categories, $style5_show_excerpt, $style5_show_date, $style5_show_author, $style5_content_overlay, $style5_side_layout );
								endforeach;
								wp_reset_postdata();
								?>
							</div>
							<?php if ( $enable_load_more && $has_more_term_posts ): ?>
								<div class="omsar-load-more-wrapper" data-tab="<?php echo esc_attr( $term_slug ); ?>">
									<button class="omsar-load-more-btn" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-tab="<?php echo esc_attr( $term_slug ); ?>">
										<?php echo esc_html( $load_more_text ); ?>
									</button>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="omsar-no-posts">
								<p><?php echo pll_e( 'No posts found in this category.'); ?></p>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

			</div>
			<?php endif; ?>

		</div>

		<?php
	}

	/**
	 * Render a single post card
	 */
	protected function render_post_card( $post, $widget_id = '', $style = 'style1', $clickable = true, $default_image = '', $show_date = true, $style5_show_categories = true, $style5_show_excerpt = true, $style5_show_date = true, $style5_show_author = true, $style5_content_overlay = false, $style5_side_layout = false ) {
		$post_id = $post->ID;
		
		// Get featured image or default image
		$post_image = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$post_image = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( ! empty( $default_image ) ) {
			$post_image = $default_image;
		}
		
		$post_title = get_the_title( $post_id );
		$post_date = get_the_date( 'M j, Y', $post_id );
		$post_link = get_permalink( $post_id );
		
		// Get post excerpt for display (style5)
		$post_excerpt = '';
		if ( has_excerpt( $post_id ) ) {
			$post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );
		} elseif ( ! empty( $post->post_content ) ) {
			$post_excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 20, '...' );
		}
		
		// Get full excerpt for search (all styles)
		$post_excerpt_for_search = '';
		if ( has_excerpt( $post_id ) ) {
			$post_excerpt_for_search = strtolower( strip_tags( get_the_excerpt( $post_id ) ) );
		} elseif ( ! empty( $post->post_content ) ) {
			$post_excerpt_for_search = strtolower( strip_tags( wp_trim_words( strip_shortcodes( $post->post_content ), 50, '' ) ) );
		}
		
		// Get post author
		$post_author = get_the_author_meta( 'display_name', $post->post_author );
		
		// Get post categories for style5
		$post_categories = [];
		if ( $style === 'style5' ) {
			$categories = get_the_category( $post_id );
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					$post_categories[] = $category;
				}
			}
		}
		
		// Get PDFs from documents repeater field
		$publication_pdfs = [];
		if ( function_exists( 'get_field' ) && function_exists( 'have_rows' ) ) {
			if ( have_rows( 'documents', $post_id ) ) {
				while ( have_rows( 'documents', $post_id ) ) {
					the_row();
					$pdf_file = get_sub_field( 'pdf' );
					if ( $pdf_file ) {
						// Handle both file ID and file array
						if ( is_numeric( $pdf_file ) ) {
							$pdf_url = wp_get_attachment_url( $pdf_file );
							$pdf_title = get_the_title( $pdf_file );
						} elseif ( is_array( $pdf_file ) ) {
							$pdf_url = $pdf_file['url'] ?? '';
							$pdf_title = $pdf_file['title'] ?? basename( $pdf_url );
						} else {
							$pdf_url = $pdf_file;
							$pdf_title = basename( $pdf_url );
						}
						
						if ( $pdf_url ) {
							$publication_pdfs[] = [
								'url' => $pdf_url,
								'title' => $pdf_title,
							];
						}
					}
				}
			}
		}
		
		$card_classes = 'omsar-post-card omsar-post-card-' . esc_attr( $style );
		if ( $clickable ) {
			$card_classes .= ' omsar-post-card-clickable';
		}
		
		?>
		<div class="<?php echo esc_attr( $card_classes ); ?>" 
			 data-post-title="<?php echo esc_attr( strtolower( $post_title ) ); ?>" 
			 data-post-excerpt="<?php echo esc_attr( $post_excerpt_for_search ); ?>"
			 <?php echo $clickable ? 'data-post-link="' . esc_url( $post_link ) . '"' : ''; ?>>
			<?php if ( $style === 'style5' ): ?>
				<?php if ( $style5_content_overlay && $post_image ): ?>
					<!-- Style 5: Content Overlay on Image -->
					<div class="omsar-post-card-style5-overlay" style="background-image: url(<?php echo esc_url( $post_image ); ?>);">
						<div class="omsar-post-card-style5-overlay-content">
							<?php if ( $style5_show_categories && ! empty( $post_categories ) ): ?>
								<div class="omsar-post-categories">
									<?php foreach ( $post_categories as $category ): ?>
										<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							
							<h3 class="omsar-post-title">
								<?php if ( $clickable ): ?>
									<a href="<?php echo esc_url( $post_link ); ?>">
										<?php echo esc_html( $post_title ); ?>
									</a>
								<?php else: ?>
									<?php echo esc_html( $post_title ); ?>
								<?php endif; ?>
							</h3>
							
							<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ): ?>
								<div class="omsar-post-excerpt">
									<?php echo esc_html( $post_excerpt ); ?>
								</div>
							<?php endif; ?>
							
							<?php if ( $style5_show_date || $style5_show_author ): ?>
								<div class="omsar-post-meta">
									<?php if ( $style5_show_date && $post_date ): ?>
										<span class="omsar-post-date"><?php echo esc_html( $post_date ); ?></span>
									<?php endif; ?>
									<?php if ( $style5_show_author && ! empty( $post_author ) ): ?>
										<span class="omsar-post-author"><?php echo esc_html__( 'By', 'omsar' ); ?> <?php echo esc_html( $post_author ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php elseif ( $style5_side_layout ): ?>
					<!-- Style 5: Side Layout (Image Left, Content Right) -->
					<?php if ( $post_image ): ?>
						<div class="omsar-post-image-side">
							<?php if ( $clickable ): ?>
								<a href="<?php echo esc_url( $post_link ); ?>">
									<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
								</a>
							<?php else: ?>
								<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
							<?php endif; ?>
						</div>
					<?php endif; ?>
					
					<div class="omsar-post-content-side">
						<?php if ( $style5_show_categories && ! empty( $post_categories ) ): ?>
							<div class="omsar-post-categories">
								<?php foreach ( $post_categories as $category ): ?>
									<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<h3 class="omsar-post-title">
							<?php if ( $clickable ): ?>
								<a href="<?php echo esc_url( $post_link ); ?>">
									<?php echo esc_html( $post_title ); ?>
								</a>
							<?php else: ?>
								<?php echo esc_html( $post_title ); ?>
							<?php endif; ?>
						</h3>
						
						<?php if ( $style5_show_date && $post_date ): ?>
							<div class="omsar-post-date-side">
								<?php echo esc_html( $post_date ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ): ?>
							<div class="omsar-post-excerpt">
								<?php echo esc_html( $post_excerpt ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php else: ?>
					<!-- Style 5: Normal Layout (Image and Content Separate) -->
					<?php if ( $post_image ): ?>
						<div class="omsar-post-image-header">
							<?php if ( $clickable ): ?>
								<a href="<?php echo esc_url( $post_link ); ?>">
									<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
								</a>
							<?php else: ?>
								<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
							<?php endif; ?>
						</div>
					<?php endif; ?>
					
					<div class="omsar-post-content">
						<?php if ( $style5_show_categories && ! empty( $post_categories ) ): ?>
							<div class="omsar-post-categories">
								<?php foreach ( $post_categories as $category ): ?>
									<span class="omsar-post-category-tag"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<h3 class="omsar-post-title">
							<?php if ( $clickable ): ?>
								<a href="<?php echo esc_url( $post_link ); ?>">
									<?php echo esc_html( $post_title ); ?>
								</a>
							<?php else: ?>
								<?php echo esc_html( $post_title ); ?>
							<?php endif; ?>
						</h3>
						
						<?php if ( $style5_show_excerpt && ! empty( $post_excerpt ) ): ?>
							<div class="omsar-post-excerpt">
								<?php echo esc_html( $post_excerpt ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( $style5_show_date || $style5_show_author ): ?>
							<div class="omsar-post-meta">
								<?php if ( $style5_show_date && $post_date ): ?>
									<span class="omsar-post-date"><?php echo esc_html( $post_date ); ?></span>
								<?php endif; ?>
								<?php if ( $style5_show_author && ! empty( $post_author ) ): ?>
									<span class="omsar-post-author"><?php echo esc_html__( 'By', 'omsar' ); ?> <?php echo esc_html( $post_author ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php elseif ( $style === 'style3' ): ?>
				<!-- Style 3: Content above image -->
				<div class="omsar-post-content">
					<h3 class="omsar-post-title">
						<?php if ( $clickable ): ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo esc_html( $post_title ); ?>
							</a>
						<?php else: ?>
							<?php echo esc_html( $post_title ); ?>
						<?php endif; ?>
					</h3>
					
					<?php if ( $show_date && $post_date ): ?>
						<div class="omsar-post-date">
							<i class="bi bi-calendar3"></i>
							<?php echo esc_html( $post_date ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $publication_pdfs ) ): ?>
						<div class="omsar-post-pdfs">
							<strong><?php echo function_exists( 'pll__' ) ? pll__( 'Downloads' ) : esc_html__( 'Downloads', 'omsar' ); ?>:</strong>
							<ul class="pdf-list">
								<?php foreach ( $publication_pdfs as $pdf ):
									$pdf_url = $pdf['url'] ?? '';
									$pdf_title = $pdf['title'] ?? basename( $pdf_url );
									if ( $pdf_url ):
								?>
									<li>
										<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
											<i class="bi bi-file-earmark-pdf"></i>
											<?php echo esc_html( $pdf_title ); ?>
										</a>
									</li>
								<?php
									endif;
								endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $post_image && $style !== 'style5' ): ?>
				<div class="omsar-post-image">
					<?php if ( $clickable ): ?>
						<a href="<?php echo esc_url( $post_link ); ?>">
							<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
						</a>
					<?php else: ?>
						<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( $post_title ); ?>">
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php if ( $style !== 'style3' && $style !== 'style5' ): ?>
				<div class="omsar-post-content">
					<h3 class="omsar-post-title">
						<?php if ( $clickable ): ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo esc_html( $post_title ); ?>
							</a>
						<?php else: ?>
							<?php echo esc_html( $post_title ); ?>
						<?php endif; ?>
					</h3>
					
					<?php if ( $show_date && $post_date ): ?>
						<div class="omsar-post-date">
							<i class="bi bi-calendar3"></i>
							<?php echo esc_html( $post_date ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $publication_pdfs ) ): ?>
						<div class="omsar-post-pdfs">
							<strong><?php echo function_exists( 'pll__' ) ? pll__( 'Downloads' ) : esc_html__( 'Downloads', 'omsar' ); ?>:</strong>
							<ul class="pdf-list">
								<?php foreach ( $publication_pdfs as $pdf ):
									$pdf_url = $pdf['url'] ?? '';
									$pdf_title = $pdf['title'] ?? basename( $pdf_url );
									if ( $pdf_url ):
								?>
									<li>
										<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
											<i class="bi bi-file-earmark-pdf"></i>
											<?php echo esc_html( $pdf_title ); ?>
										</a>
									</li>
								<?php
									endif;
								endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var taxonomy = settings.taxonomy || '';
		var postType = settings.post_type || 'post';
		var showEmpty = settings.show_empty_terms === 'yes';
		var showAll = settings.show_all_tab === 'yes';
		var widgetId = 'omsar-posts-taxonomy-' + view.getIDInt();
		#>
		
		<div class="omsar-posts-by-taxonomy-widget" id="{{ widgetId }}">
			<div class="elementor-alert elementor-alert-info">
				<?php echo esc_html__( 'Posts by Taxonomy widget - Configure in settings to see preview', 'omsar' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get badge text based on source
	 * 
	 * @param int $post_id Post ID.
	 * @param string $source Badge source (taxonomy, custom_field, none).
	 * @param string $taxonomy Taxonomy name.
	 * @param string $custom_field Custom field name.
	 * @param string $taxonomy_display Display type for taxonomy fields (name or id).
	 * @return string Badge text.
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
			// Check if ACF is available and use get_field
			if ( function_exists( 'get_field' ) ) {
				$value = get_field( $custom_field, $post_id );
			} else {
				// Fallback to get_post_meta
				$value = get_post_meta( $post_id, $custom_field, true );
			}

			if ( ! empty( $value ) ) {
				// Check if this is a taxonomy field
				$field_type = $this->get_acf_field_type( $custom_field );
				
				if ( $field_type === 'taxonomy' ) {
					// Handle taxonomy field
					$term = null;
					
					if ( is_array( $value ) ) {
						// Multiple terms - get first one
						if ( ! empty( $value[0] ) ) {
							if ( is_object( $value[0] ) ) {
								$term = $value[0];
							} elseif ( is_numeric( $value[0] ) ) {
								$term = get_term( $value[0] );
							}
						}
					} elseif ( is_object( $value ) ) {
						// Single term object
						$term = $value;
					} elseif ( is_numeric( $value ) ) {
						// Term ID
						$term = get_term( $value );
					} else {
						// Try to get term by slug or name
						// Get field object to find taxonomy
						if ( function_exists( 'get_field_object' ) ) {
							$field_object = get_field_object( $custom_field, $post_id );
							$taxonomy_name = ! empty( $field_object['taxonomy'] ) ? $field_object['taxonomy'] : '';
							
							if ( $taxonomy_name ) {
								$term = get_term_by( 'slug', $value, $taxonomy_name );
								if ( ! $term ) {
									$term = get_term_by( 'name', $value, $taxonomy_name );
								}
							}
						}
					}

					if ( $term && ! is_wp_error( $term ) ) {
						// Return name or ID based on display type
						return $taxonomy_display === 'id' ? (string) $term->term_id : $term->name;
					}
				} else {
					// Regular custom field - return as is
					if ( is_array( $value ) ) {
						// If array, join with comma
						return implode( ', ', array_filter( $value ) );
					} elseif ( is_object( $value ) ) {
						// If object, try to get a string representation
						if ( isset( $value->name ) ) {
							return $value->name;
						} elseif ( isset( $value->post_title ) ) {
							return $value->post_title;
						}
						return (string) $value;
					}
					return (string) $value;
				}
			}
		}

		return '';
	}

	/**
	 * Get ACF field type
	 * 
	 * @param string $field_name Field name.
	 * @return string Field type.
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
	 * Get dropdown options for Style 4 filter
	 * 
	 * @param string $source Source type (taxonomy or acf_field).
	 * @param string $taxonomy Taxonomy name.
	 * @param string $acf_field ACF field name.
	 * @param string $post_type Post type.
	 * @return array Array of value => label pairs.
	 */
	private function get_style4_dropdown_options( $source, $taxonomy = '', $acf_field = '', $post_type = 'post' ) {
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
			// Get all posts to collect unique values from ACF field
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
						// Handle taxonomy field - extract term names
						$terms_to_process = [];
						
						if ( is_array( $value ) ) {
							$terms_to_process = $value;
						} else {
							$terms_to_process = [ $value ];
						}

						foreach ( $terms_to_process as $term_item ) {
							$term = null;
							
							if ( is_object( $term_item ) && isset( $term_item->term_id ) ) {
								$term = $term_item;
							} elseif ( is_numeric( $term_item ) ) {
								$term = get_term( $term_item );
							} elseif ( is_array( $term_item ) && isset( $term_item['term_id'] ) ) {
								$term = get_term( $term_item['term_id'] );
							}

							if ( $term && ! is_wp_error( $term ) ) {
								$unique_values[ $term->term_id ] = $term->name;
							}
						}
					} else {
						// Regular custom field - use value as is
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

		// Sort options by label
		asort( $options );

		return $options;
	}

}

