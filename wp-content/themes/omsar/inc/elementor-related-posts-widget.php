<?php
/**
 * Elementor Related Posts Widget
 * 
 * Custom Elementor widget for displaying related posts in list format.
 * 
 * @package OMSAR
 * @since 1.0.0
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

/**
 * Register the Related Posts widget
 */
function omsar_register_related_posts_widget( $widgets_manager ) {
	/**
	 * Related Posts Widget Class
	 *
	 * Elementor widget for displaying posts in list format.
	 *
	 * @since 1.0.0
	 */
	class OMSAR_Related_Posts_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve posts widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'omsar_related_posts';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve posts widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Related Posts', 'omsar' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve posts widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the posts widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'posts', 'post', 'blog', 'list', 'grid', 'related' ];
	}

	/**
	 * Register posts widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'omsar' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// Info notice
		$this->add_control(
			'related_posts_info',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<div style="background: #f0f0f1; padding: 10px; border-radius: 4px; margin-bottom: 10px;"><strong>' . esc_html__( 'Note:', 'omsar' ) . '</strong> ' . esc_html__( 'This widget automatically excludes the current post when displayed on single post pages. It always displays 3 items per row.', 'omsar' ) . '</div>',
			]
		);

		// Post Type Control
		$this->add_control(
			'post_type',
			[
				'label' => esc_html__( 'Post Type', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => $this->get_post_types(),
			]
		);

		// Number of Posts Control
		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Number of Posts', 'omsar' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'max' => 50,
				'step' => 1,
			]
		);

		// Badge Source Control
		$this->add_control(
			'badge_source',
			[
				'label' => esc_html__( 'Badge Source', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'omsar' ),
					'taxonomy' => esc_html__( 'Taxonomy', 'omsar' ),
					'custom_field' => esc_html__( 'Custom Field', 'omsar' ),
				],
			]
		);

		// Badge Taxonomy Control
		$this->add_control(
			'badge_taxonomy',
			[
				'label' => esc_html__( 'Badge Taxonomy', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_taxonomies(),
				'condition' => [
					'badge_source' => 'taxonomy',
				],
			]
		);

		// Badge Custom Field Control - Now a dropdown listing ACF fields
		$this->add_control(
			'badge_custom_field',
			[
				'label' => esc_html__( 'Custom Field', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_acf_fields(),
				'condition' => [
					'badge_source' => 'custom_field',
				],
			]
		);

		// Taxonomy Display Type Control (for taxonomy custom fields)
		$this->add_control(
			'badge_taxonomy_display',
			[
				'label' => esc_html__( 'Display Type', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => esc_html__( 'Name', 'omsar' ),
					'id' => esc_html__( 'ID', 'omsar' ),
				],
				'condition' => [
					'badge_source' => 'custom_field',
					'badge_custom_field!' => '',
				],
				'description' => esc_html__( 'Choose to display the taxonomy name or ID (only applies to taxonomy fields)', 'omsar' ),
			]
		);

		// Description Length Control
		$this->add_control(
			'excerpt_length',
			[
				'label' => esc_html__( 'Description Length (words)', 'omsar' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 20,
				'min' => 0,
				'max' => 100,
				'step' => 1,
			]
		);

		// Read More Button Text
		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Read More Button Text', 'omsar' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'omsar' ),
			]
		);

		// Default Image Control
		$this->add_control(
			'default_image',
			[
				'label' => esc_html__( 'Default Image', 'omsar' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Image to use when post has no featured image', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'omsar' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_background',
			[
				'label' => esc_html__( 'Card Background Overlay', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => 'rgba(255, 255, 255, 0)',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-item::before' => 'background: linear-gradient(135deg, {{VALUE}} 0%, rgba(255, 255, 255, 0.95) 100%);',
				],
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label' => esc_html__( 'Overlay Opacity', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '' ],
				'range' => [
					'' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .custom-posts-item::before' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Title Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .custom-posts-title',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Description Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => esc_html__( 'Description Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .custom-posts-excerpt',
			]
		);

		// Badge Style
		$this->add_control(
			'badge_heading',
			[
				'label' => esc_html__( 'Badge', 'omsar' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'badge_background',
			[
				'label' => esc_html__( 'Badge Background', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => esc_html__( 'Badge Text Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label' => esc_html__( 'Badge Border Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-badge' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => esc_html__( 'Badge Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .custom-posts-badge',
			]
		);

		// Button Style
		$this->add_control(
			'button_heading',
			[
				'label' => esc_html__( 'Read More Button', 'omsar' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_background',
			[
				'label' => esc_html__( 'Button Background', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-read-more' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => esc_html__( 'Button Text Color', 'omsar' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .custom-posts-read-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Button Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .custom-posts-read-more',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Button Border Radius', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'%' => [
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
					'{{WRAPPER}} .custom-posts-read-more' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Button Padding', 'omsar' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '12',
					'right' => '24',
					'bottom' => '12',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .custom-posts-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'gap',
			[
				'label' => esc_html__( 'Gap Between Items', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
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
					'{{WRAPPER}} .custom-posts-list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Card Shape & Border Radius
		$this->add_control(
			'card_shape_heading',
			[
				'label' => esc_html__( 'Card Shape & Border', 'omsar' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'card_shape_style',
			[
				'label' => esc_html__( 'Card Shape', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'rectangle',
				'options' => [
					'rectangle' => esc_html__( 'Rectangle', 'omsar' ),
					'square' => esc_html__( 'Square', 'omsar' ),
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .custom-posts-item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Content Padding
		$this->add_control(
			'content_padding_heading',
			[
				'label' => esc_html__( 'Content Padding', 'omsar' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'omsar' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '30',
					'right' => '30',
					'bottom' => '30',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .custom-posts-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get available post types
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array Post types array.
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
	 * Get available taxonomies for selected post type
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array Taxonomies array.
	 */
	private function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$options = [ '' => esc_html__( 'Select Taxonomy', 'omsar' ) ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}

	/**
	 * Get ACF custom fields
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array ACF fields array.
	 */
	private function get_acf_fields() {
		$options = [ '' => esc_html__( 'Select Custom Field', 'omsar' ) ];

		// Check if ACF is active
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return $options;
		}

		// Get all field groups
		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group['ID'] );
			
			if ( $fields ) {
				foreach ( $fields as $field ) {
					$field_label = ! empty( $field['label'] ) ? $field['label'] : $field['name'];
					$field_type = ! empty( $field['type'] ) ? $field['type'] : '';
					
					// Add field type to label for clarity
					$label = $field_label;
					if ( $field_type ) {
						$label .= ' (' . $field_type . ')';
					}
					
					$options[ $field['name'] ] = $label;
				}
			}
		}

		return $options;
	}

	/**
	 * Get ACF field type
	 *
	 * @since 1.0.0
	 * @access private
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
	 * Get badge text based on source
	 *
	 * @since 1.0.0
	 * @access private
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
	 * Render posts widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$post_type = $settings['post_type'];
		$posts_per_page = $settings['posts_per_page'];

		// Query arguments
		$args = [
			'post_type' => $post_type,
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		];

		// Exclude current post if we're on a single post/page
		if ( is_singular() ) {
			$current_post_id = get_queried_object_id();
			if ( $current_post_id ) {
				$args['post__not_in'] = [ $current_post_id ];
			}
		}

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			echo '<p>' . esc_html__( 'No posts found.', 'omsar' ) . '</p>';
			return;
		}

		$badge_source = isset( $settings['badge_source'] ) ? $settings['badge_source'] : 'none';
		$badge_taxonomy = isset( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : '';
		$badge_custom_field = isset( $settings['badge_custom_field'] ) ? $settings['badge_custom_field'] : '';
		$badge_taxonomy_display = isset( $settings['badge_taxonomy_display'] ) ? $settings['badge_taxonomy_display'] : 'name';
		$excerpt_length = isset( $settings['excerpt_length'] ) ? intval( $settings['excerpt_length'] ) : 20;
		$read_more_text = isset( $settings['read_more_text'] ) ? $settings['read_more_text'] : esc_html__( 'Read More', 'omsar' );
		$default_image = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
		$card_shape = isset( $settings['card_shape_style'] ) ? $settings['card_shape_style'] : 'rectangle';

		?>
		<div class="custom-posts-widget">
			<div class="custom-posts-list custom-posts-shape-<?php echo esc_attr( $card_shape ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post(); 
					$post_id = get_the_ID();
					
					// Get featured image or default image
					$image_url = '';
					if ( has_post_thumbnail() ) {
						$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
					} elseif ( ! empty( $default_image ) ) {
						$image_url = $default_image;
					}
					
					// Get badge text
					$badge_text = $this->get_badge_text( $post_id, $badge_source, $badge_taxonomy, $badge_custom_field, $badge_taxonomy_display );
					
					// Get excerpt
					$excerpt = '';
					if ( $excerpt_length > 0 ) {
						if ( has_excerpt() ) {
							$excerpt = wp_trim_words( get_the_excerpt(), $excerpt_length, '...' );
						} elseif ( get_the_content() ) {
							$excerpt = wp_trim_words( get_the_content(), $excerpt_length, '...' );
						}
					}
					
					// Build style attribute for background image
					$item_style = '';
					if ( ! empty( $image_url ) ) {
						$item_style = 'style="background-image: url(' . esc_url( $image_url ) . ');"';
					}
				?>
					<div class="custom-posts-item" <?php echo $item_style; ?>>
						<div class="custom-posts-content">
							<h3 class="custom-posts-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							
							<?php if ( ! empty( $badge_text ) ) : ?>
								<span class="custom-posts-badge"><?php echo esc_html( $badge_text ); ?></span>
							<?php endif; ?>
							
							<?php if ( ! empty( $excerpt ) ) : ?>
								<div class="custom-posts-excerpt">
									<?php echo wp_kses_post( $excerpt ); ?>
								</div>
							<?php endif; ?>
							
							<a href="<?php the_permalink(); ?>" class="custom-posts-read-more">
								<?php echo esc_html( $read_more_text ); ?>
							</a>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
		<?php

		wp_reset_postdata();
	}
	}
	
	$widgets_manager->register( new OMSAR_Related_Posts_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_related_posts_widget' );

/**
 * Enqueue Related Posts widget styles
 */
function omsar_related_posts_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/related-posts-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-related-posts-widget',
			get_template_directory_uri() . '/assets/css/related-posts-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_related_posts_widget_styles' );
