<?php
/**
 * Elementor Static Card List Widget Class
 * 
 * Displays a list of static cards with two style options
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

class OMSAR_Static_Card_List_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_static_card_list';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Static Card List', 'omsar' );
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
		return [ 'cards', 'card list', 'static cards', 'grid', 'services' ];
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
			'card_style',
			[
				'label' => esc_html__( 'Card Style', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' => esc_html__( 'Style 1: Image/Color Above Text', 'omsar' ),
					'style2' => esc_html__( 'Style 2: Text Overlay on Background', 'omsar' ),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
			]
		);

		// Cards Repeater
		$repeater = new Repeater();

		// Background Type
		$repeater->add_control(
			'background_type',
			[
				'label' => esc_html__( 'Background Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'color',
				'options' => [
					'color' => esc_html__( 'Solid Color', 'omsar' ),
					'image' => esc_html__( 'Image', 'omsar' ),
				],
			]
		);

		$repeater->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6366f1',
				'condition' => [
					'background_type' => 'color',
				],
			]
		);

		$repeater->add_control(
			'background_image',
			[
				'label' => esc_html__( 'Background Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'condition' => [
					'background_type' => 'image',
				],
			]
		);

		// For Style 2: Gradient colors (only used when widget style is style2)
		$repeater->add_control(
			'gradient_color_from',
			[
				'label' => esc_html__( 'Gradient Color From (Style 2 only)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6366f1',
				'description' => esc_html__( 'Only used when Card Style is set to Style 2', 'omsar' ),
			]
		);

		$repeater->add_control(
			'gradient_color_to',
			[
				'label' => esc_html__( 'Gradient Color To (Style 2 only)', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#8b5cf6',
				'description' => esc_html__( 'Only used when Card Style is set to Style 2', 'omsar' ),
			]
		);

		// Title
		$repeater->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Card Title', 'omsar' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		// Description
		$repeater->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Card description text goes here.', 'omsar' ),
				'rows' => 3,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		// For Style 2: Categories Repeater
		$categories_repeater = new Repeater();
		$categories_repeater->add_control(
			'category_text',
			[
				'label' => esc_html__( 'Category Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'CATEGORY', 'omsar' ),
				'label_block' => true,
			]
		);

		$categories_repeater->add_control(
			'category_color',
			[
				'label' => esc_html__( 'Category Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
			]
		);

		$repeater->add_control(
			'categories',
			[
				'label' => esc_html__( 'Categories (Style 2 only)', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $categories_repeater->get_controls(),
				'default' => [
					[
						'category_text' => esc_html__( 'CATEGORY', 'omsar' ),
						'category_color' => '#ffffff',
					],
				],
				'title_field' => '{{{ category_text }}}',
				'description' => esc_html__( 'Only used when Card Style is set to Style 2', 'omsar' ),
			]
		);

		// For Style 2: Button
		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text (Style 2 only)', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Learn More', 'omsar' ),
				'description' => esc_html__( 'Only used when Card Style is set to Style 2', 'omsar' ),
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Button Link (Style 2 only)', 'omsar' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'omsar' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
				'description' => esc_html__( 'Only used when Card Style is set to Style 2', 'omsar' ),
			]
		);

		$this->add_control(
			'cards',
			[
				'label' => esc_html__( 'Cards', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__( 'Service One', 'omsar' ),
						'description' => esc_html__( 'Brief description of the service offered.', 'omsar' ),
						'background_color' => '#6366f1',
					],
					[
						'title' => esc_html__( 'Service Two', 'omsar' ),
						'description' => esc_html__( 'Another service we provide to clients.', 'omsar' ),
						'background_color' => '#6366f1',
					],
					[
						'title' => esc_html__( 'Service Three', 'omsar' ),
						'description' => esc_html__( 'Additional offering for customers.', 'omsar' ),
						'background_color' => '#6366f1',
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// Style Section - Card
		$this->start_controls_section(
			'style_card_section',
			[
				'label' => esc_html__( 'Card Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .omsar-static-card-list-item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_gap',
			[
				'label' => esc_html__( 'Gap Between Cards', 'omsar' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_min_height',
			[
				'label' => esc_html__( 'Card Min Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 10,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'desktop_default' => [
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
					'{{WRAPPER}} .omsar-static-card-list-item' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-static-card-list-item',
			]
		);

		$this->end_controls_section();

		// Style Section - Title (Style 1)
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
				'label' => esc_html__( 'Title Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1f2937',
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-static-card-list-item-title',
			]
		);

		$this->add_responsive_control(
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
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Description
		$this->start_controls_section(
			'style_description_section',
			[
				'label' => esc_html__( 'Description Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Description Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6b7280',
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-static-card-list-item-description',
			]
		);

		$this->add_responsive_control(
			'description_margin',
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Category (Style 2)
		$this->start_controls_section(
			'style_category_section',
			[
				'label' => esc_html__( 'Category Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'card_style' => 'style2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'category_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-static-card-list-item-category',
			]
		);

		$this->add_responsive_control(
			'category_margin',
			[
				'label' => esc_html__( 'Margin Bottom', 'omsar' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Button (Style 2)
		$this->start_controls_section(
			'style_button_section',
			[
				'label' => esc_html__( 'Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'card_style' => 'style2',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-static-card-list-item-button',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
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
				'selectors' => [
					'{{WRAPPER}} .omsar-static-card-list-item-button' => 'border-radius: {{SIZE}}{{UNIT}};',
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
		$cards = ! empty( $settings['cards'] ) ? $settings['cards'] : [];
		$card_style = ! empty( $settings['card_style'] ) ? $settings['card_style'] : 'style1';
		$columns = ! empty( $settings['columns'] ) ? $settings['columns'] : '3';
		$columns_tablet = ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : '2';
		$columns_mobile = ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : '1';

		$widget_id = 'omsar-static-card-list-' . $this->get_id();

		// Generate responsive CSS
		?>
		<style>
			#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
				display: grid;
				grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);
			}

			@media (max-width: 1024px) {
				#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
					grid-template-columns: repeat(<?php echo esc_attr( $columns_tablet ); ?>, 1fr);
				}
			}

			@media (max-width: 767px) {
				#<?php echo esc_attr( $widget_id ); ?> .omsar-static-card-list {
					grid-template-columns: repeat(<?php echo esc_attr( $columns_mobile ); ?>, 1fr);
				}
			}
		</style>

		<div id="<?php echo esc_attr( $widget_id ); ?>" class="omsar-static-card-list-wrapper">
			<div class="omsar-static-card-list omsar-static-card-list-<?php echo esc_attr( $card_style ); ?>">
				<?php foreach ( $cards as $index => $card ) : 
					$card_item_id = $widget_id . '-card-' . $index;
					$background_type = ! empty( $card['background_type'] ) ? $card['background_type'] : 'color';
					$background_color = ! empty( $card['background_color'] ) ? $card['background_color'] : '#6366f1';
					$background_image = ! empty( $card['background_image']['url'] ) ? $card['background_image']['url'] : '';
					$gradient_from = ! empty( $card['gradient_color_from'] ) ? $card['gradient_color_from'] : $background_color;
					$gradient_to = ! empty( $card['gradient_color_to'] ) ? $card['gradient_color_to'] : $background_color;
					$title = ! empty( $card['title'] ) ? $card['title'] : '';
					$description = ! empty( $card['description'] ) ? $card['description'] : '';
					$categories = ! empty( $card['categories'] ) ? $card['categories'] : [];
					$button_text = ! empty( $card['button_text'] ) ? $card['button_text'] : '';
					$button_link = ! empty( $card['button_link']['url'] ) ? $card['button_link']['url'] : '';
					$button_target = ! empty( $card['button_link']['is_external'] ) ? ' target="_blank"' : '';
					$button_nofollow = ! empty( $card['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';

					// Build background style for Style 2
					$background_style = '';
					if ( $card_style === 'style2' ) {
						if ( $background_type === 'image' && $background_image ) {
							$background_style = 'background-image: url(' . esc_url( $background_image ) . '); background-size: cover; background-position: center;';
						} elseif ( $background_type === 'color' ) {
							$background_style = 'background: linear-gradient(180deg, ' . esc_attr( $gradient_from ) . ' 0%, ' . esc_attr( $gradient_to ) . ' 100%);';
						}
					}
				?>
					<?php if ( $card_style === 'style1' && ( $background_image || $background_color ) ) : ?>
						<style>
							#<?php echo esc_attr( $card_item_id ); ?>::before {
								<?php if ( $background_type === 'image' && $background_image ) : ?>
									background-image: url(<?php echo esc_url( $background_image ); ?>);
									background-size: cover;
									background-position: center;
								<?php else : ?>
									background-color: <?php echo esc_attr( $background_color ); ?>;
								<?php endif; ?>
							}
						</style>
					<?php endif; ?>
					<div id="<?php echo esc_attr( $card_item_id ); ?>" class="omsar-static-card-list-item" style="<?php echo esc_attr( $background_style ); ?>">
						<?php if ( $card_style === 'style1' ) : ?>
							<div class="omsar-static-card-list-item-content">
								<?php if ( $title ) : ?>
									<h3 class="omsar-static-card-list-item-title"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( $description ) : ?>
									<p class="omsar-static-card-list-item-description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
							</div>
						<?php else : // style2 ?>
							<div class="omsar-static-card-list-item-overlay">
								<?php if ( ! empty( $categories ) ) : ?>
									<div class="omsar-static-card-list-item-categories">
										<?php foreach ( $categories as $category ) : 
											$category_text = ! empty( $category['category_text'] ) ? $category['category_text'] : '';
											$category_color = ! empty( $category['category_color'] ) ? $category['category_color'] : '#ffffff';
										?>
											<span class="omsar-static-card-list-item-category" style="color: <?php echo esc_attr( $category_color ); ?>;">
												<?php echo esc_html( $category_text ); ?>
											</span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<?php if ( $title ) : ?>
									<h3 class="omsar-static-card-list-item-title"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( $description ) : ?>
									<p class="omsar-static-card-list-item-description"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
								<?php if ( $button_text && $button_link ) : ?>
									<a href="<?php echo esc_url( $button_link ); ?>" class="omsar-static-card-list-item-button"<?php echo $button_target . $button_nofollow; ?>>
										<?php echo esc_html( $button_text ); ?> →
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
