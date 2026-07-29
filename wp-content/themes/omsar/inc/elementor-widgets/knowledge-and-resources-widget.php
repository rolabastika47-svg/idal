<?php
/**
 * Elementor Knowledge and Resources Widget Class
 *
 * Displays knowledge_resources CPT posts: featured image, title, date, and
 * downloadable documents from ACF repeater resources_repeater → document_resource.
 * Layout switches by RTL/LTR (Polylang).
 *
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! did_action( 'elementor/loaded' ) ) {
	return;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class OMSAR_Knowledge_And_Resources_Widget extends Widget_Base {

	public function get_name() {
		return 'omsar_knowledge_and_resources';
	}

	public function get_title() {
		return esc_html__( 'Knowledge and Resources', 'omsar' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'omsar-elements' ];
	}

	public function get_keywords() {
		return [ 'knowledge', 'resources', 'documents', 'downloads' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'omsar' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => esc_html__( 'Posts Per Page', 'omsar' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 100,
				'description' => esc_html__( 'Number of items per page (initial load and each Load More).', 'omsar' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'omsar' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order By', 'omsar' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'date'  => esc_html__( 'Date', 'omsar' ),
					'title' => esc_html__( 'Title', 'omsar' ),
					'menu_order' => esc_html__( 'Menu Order', 'omsar' ),
				],
				'default' => 'date',
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'omsar' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'DESC' => esc_html__( 'Descending', 'omsar' ),
					'ASC'  => esc_html__( 'Ascending', 'omsar' ),
				],
				'default' => 'DESC',
			]
		);

		$this->add_control(
			'default_image',
			[
				'label'       => esc_html__( 'Default Image', 'omsar' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [ 'url' => '' ],
				'description' => esc_html__( 'Used when a post has no featured image.', 'omsar' ),
			]
		);

		$this->add_control(
			'search_placeholder',
			[
				'label'       => esc_html__( 'Search Placeholder', 'omsar' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Search posts...', 'omsar' ),
				'placeholder' => __( 'Search posts...', 'omsar' ),
				'description' => esc_html__( 'Placeholder text for the search field.', 'omsar' ),
			]
		);

		$this->add_control(
			'search_align',
			[
				'label'   => esc_html__( 'Search Alignment', 'omsar' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [ 'title' => esc_html__( 'Left', 'omsar' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'omsar' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'omsar' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'cards_clickable',
			[
				'label'        => esc_html__( 'Cards clickable', 'omsar' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'omsar' ),
				'label_off'    => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'When enabled, clicking a card opens the resource single page. Download links still work independently.', 'omsar' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .omsar-kr-card-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-card-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'title_align',
			[
				'label'     => esc_html__( 'Alignment', 'omsar' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'default'   => 'left',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-card-title' => 'text-align: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'card_style_section',
			[
				'label' => esc_html__( 'Card', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Background', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-card' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .omsar-kr-card',
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 8 ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'load_more_style_section',
			[
				'label' => esc_html__( 'Load More Button', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'load_more_btn_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5568',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5568',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 10, 'step' => 1 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 1 ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			]
		);

		$this->add_control(
			'load_more_btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
					'%'  => [ 'min' => 0, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 50 ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => '10', 'right' => '24', 'bottom' => '10', 'left' => '24',
					'unit' => 'px',
				],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'load_more_btn_typography',
				'label'    => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn',
			]
		);

		$this->add_control(
			'load_more_btn_hover_heading',
			[
				'label'     => esc_html__( 'Hover State', 'omsar' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'load_more_btn_hover_bg_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_hover_text_color',
			[
				'label'     => esc_html__( 'Hover Text Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_btn_hover_border_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper .omsar-load-more-btn:hover' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'load_more_wrapper_align',
			[
				'label'     => esc_html__( 'Alignment', 'omsar' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'omsar' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'omsar' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'omsar' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper' => 'text-align: {{VALUE}};' ],
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
					'top' => '32', 'right' => '0', 'bottom' => '0', 'left' => '0',
					'unit' => 'px',
				],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-load-more-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'search_style_section',
			[
				'label' => esc_html__( 'Search Field', 'omsar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'search_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .omsar-kr-search-inner' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-kr-search-input' => 'background-color: transparent;',
				],
			]
		);

		$this->add_control(
			'search_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4a5568',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-search-input' => 'color: {{VALUE}};', '{{WRAPPER}} .omsar-kr-search-input::placeholder' => 'color: {{VALUE}}; opacity: 0.8;' ],
			]
		);

		$this->add_control(
			'search_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-search-inner' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'search_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 10, 'step' => 1 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 1 ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-search-inner' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ],
			]
		);

		$this->add_control(
			'search_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
					'%'  => [ 'min' => 0, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 50 ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-search-inner' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'search_padding',
			[
				'label'      => esc_html__( 'Padding', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => '12', 'right' => '44', 'bottom' => '12', 'left' => '20',
					'unit' => 'px',
				],
				'description' => esc_html__( 'Right padding should accommodate the search icon.', 'omsar' ),
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-search-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'search_typography',
				'label'    => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-kr-search-input',
			]
		);

		$this->add_control(
			'search_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'omsar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#718096',
				'selectors' => [ '{{WRAPPER}} .omsar-kr-search-icon' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'search_wrapper_margin',
			[
				'label'      => esc_html__( 'Wrapper Margin', 'omsar' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => '0', 'right' => '0', 'bottom' => '24', 'left' => '0',
					'unit' => 'px',
				],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-search-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'search_max_width',
			[
				'label'      => esc_html__( 'Max Width', 'omsar' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ], 'em' => [ 'min' => 15, 'max' => 80 ] ],
				'selectors'  => [ '{{WRAPPER}} .omsar-kr-search-inner' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%;' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( function_exists( 'omsar_bd_render_knowledge_resources_widget' ) ) {
			$settings   = omsar_bd_knowledge_resources_settings_from_elementor( $this->get_settings_for_display() );
			$element_id = (string) $this->get_id();
			omsar_bd_render_knowledge_resources_widget(
				$settings,
				$element_id,
				'elementor-element-' . $element_id
			);
			return;
		}

		$this->render_legacy();
	}

	/**
	 * Legacy render when Breakdance shared render is unavailable.
	 */
	private function render_legacy() {
		$settings = $this->get_settings_for_display();
		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6;
		$columns        = isset( $settings['columns'] ) ? $settings['columns'] : '2';
		$orderby        = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order          = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$default_image  = isset( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : '';
		$search_placeholder = ! empty( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : ( function_exists( 'pll__' ) ? pll__( 'Search posts...' ) : __( 'Search posts...', 'omsar' ) );
		$search_align   = isset( $settings['search_align'] ) ? $settings['search_align'] : 'center';
		$cards_clickable = ! empty( $settings['cards_clickable'] ) && $settings['cards_clickable'] === 'yes';

		$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
		$is_rtl       = ( $current_lang === 'ar' || is_rtl() );

		$query_args = [
			'post_type'      => 'knowledge_resources',
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order,
			'post_status'    => 'publish',
			'paged'          => 1,
			'no_found_rows'  => false,
		];

		if ( function_exists( 'pll_current_language' ) ) {
			$query_args['lang'] = pll_current_language();
		}

		$query = new WP_Query( $query_args );
		$widget_id = $this->get_id();
		$total_found = $query->found_posts;
		$has_more    = $total_found > $posts_per_page;

		$downloads_label = function_exists( 'pll__' ) ? pll__( 'Downloads' ) : __( 'Downloads', 'omsar' );
		$load_more_label = function_exists( 'pll__' ) ? pll__( 'Load More' ) : __( 'Load More', 'omsar' );
		$no_results      = function_exists( 'pll__' ) ? pll__( 'No knowledge and resources are available at this time.' ) : __( 'No knowledge and resources are available at this time.', 'omsar' );

		$rtl_class = $is_rtl ? ' omsar-kr-rtl' : '';
		?>
		<div class="omsar-knowledge-resources-widget elementor-element-<?php echo esc_attr( $widget_id ); ?><?php echo esc_attr( $rtl_class ); ?>"
			data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
			data-columns="<?php echo esc_attr( $columns ); ?>"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-order="<?php echo esc_attr( $order ); ?>"
			data-default-image="<?php echo esc_attr( $default_image ); ?>"
			data-search-placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
			data-cards-clickable="<?php echo $cards_clickable ? '1' : '0'; ?>">
			<div class="omsar-kr-search-wrapper" style="text-align: <?php echo esc_attr( $search_align ); ?>;">
				<div class="omsar-kr-search-inner">
					<input type="search" class="omsar-kr-search-input" placeholder="<?php echo esc_attr( $search_placeholder ); ?>" autocomplete="off" aria-label="<?php echo esc_attr( $search_placeholder ); ?>" />
					<i class="bi bi-search omsar-kr-search-icon" aria-hidden="true"></i>
				</div>
			</div>
			<div class="omsar-kr-list omsar-kr-grid" style="grid-template-columns: repeat(<?php echo esc_attr( (int) $columns ); ?>, 1fr);" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
				<?php
				$has_posts = $query->have_posts();
				if ( $has_posts ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$this->render_card( $default_image, $downloads_label, $is_rtl, $cards_clickable );
					}
					wp_reset_postdata();
				}
				?>
			</div>
			<div class="omsar-kr-no-results"<?php echo $has_posts ? ' style="display: none;"' : ''; ?>>
				<p class="omsar-kr-no-results-message"><?php echo esc_html( $no_results ); ?></p>
			</div>
			<div class="omsar-kr-load-more-wrapper"<?php echo $has_more ? '' : ' style="display: none;"'; ?>>
				<button type="button" class="omsar-load-more-btn omsar-kr-load-more-btn"
					data-page="1"
					data-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
					data-orderby="<?php echo esc_attr( $orderby ); ?>"
					data-order="<?php echo esc_attr( $order ); ?>"
					data-columns="<?php echo esc_attr( $columns ); ?>"
					data-default-image="<?php echo esc_attr( $default_image ); ?>"
					data-cards-clickable="<?php echo $cards_clickable ? '1' : '0'; ?>"
					data-total="<?php echo esc_attr( $total_found ); ?>">
					<?php echo esc_html( $load_more_label ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Output a single Knowledge and Resources card.
	 *
	 * @param string $default_image   Default image URL.
	 * @param string $downloads_label "Downloads" label.
	 * @param bool   $is_rtl          Whether current language is RTL.
	 * @param bool   $cards_clickable Whether the card should link to the single post.
	 */
	private function render_card( $default_image, $downloads_label, $is_rtl, $cards_clickable = false ) {
		$post_id = get_the_ID();

		$image_url = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
		}
		if ( ! $image_url && $default_image ) {
			$image_url = $default_image;
		}

		$title = get_the_title();
		$date  = get_the_date( 'F j, Y' );

		$documents = [];
		if ( function_exists( 'have_rows' ) && have_rows( 'resources_repeater', $post_id ) ) {
			while ( have_rows( 'resources_repeater', $post_id ) ) {
				the_row();
				$file = get_sub_field( 'document_resource' );
				if ( ! $file ) {
					continue;
				}
				$documents[] = $this->normalize_file( $file );
			}
		}
		$documents = array_filter( $documents );

		$card_class = 'omsar-kr-card';
		if ( ! $image_url ) {
			$card_class .= ' omsar-kr-card--no-image';
		}
		if ( $cards_clickable ) {
			$card_class .= ' omsar-kr-card--clickable';
		}
		$card_attrs = $cards_clickable ? ' data-href="' . esc_url( get_permalink() ) . '"' : '';
		?>
		<article class="<?php echo esc_attr( $card_class ); ?>"<?php echo $card_attrs; ?>>
			<?php if ( $image_url ) : ?>
				<div class="omsar-kr-card-image">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
				</div>
			<?php endif; ?>
			<div class="omsar-kr-card-content">
				<h3 class="omsar-kr-card-title"><?php echo esc_html( $title ); ?></h3>
				
				<?php if ( ! empty( $documents ) ) : ?>
					<div class="omsar-kr-downloads">
						<span class="omsar-kr-downloads-label"><?php echo esc_html( $downloads_label ); ?>:</span>
						<ul class="omsar-kr-download-list">
							<?php foreach ( $documents as $doc ) : ?>
								<li>
									<a href="<?php echo esc_url( $doc['url'] ); ?>" class="omsar-kr-download-link" target="_blank" rel="noopener noreferrer" download>
										<i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
										<?php echo esc_html( $doc['label'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

	/**
	 * Return HTML for a single Knowledge and Resources card (for AJAX load more).
	 *
	 * @param int    $post_id         Post ID.
	 * @param string $default_image   Default image URL.
	 * @param string $downloads_label "Downloads" label.
	 * @param bool   $is_rtl          Whether current language is RTL.
	 * @param bool   $cards_clickable Whether the card should link to the single post.
	 * @return string
	 */
	public static function get_card_html( $post_id, $default_image, $downloads_label, $is_rtl, $cards_clickable = false ) {
		if ( function_exists( 'omsar_bd_get_knowledge_resource_card_html' ) ) {
			$settings = omsar_bd_normalize_knowledge_resources_settings(
				array(
					'default_image'   => $default_image,
					'cards_clickable' => $cards_clickable,
				)
			);
			return omsar_bd_get_knowledge_resource_card_html( $post_id, $settings, $downloads_label );
		}

		$image_url = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
		}
		if ( ! $image_url && $default_image ) {
			$image_url = $default_image;
		}

		$title = get_the_title( $post_id );
		$date  = get_the_date( 'F j, Y', $post_id );

		$documents = [];
		if ( function_exists( 'have_rows' ) && have_rows( 'resources_repeater', $post_id ) ) {
			while ( have_rows( 'resources_repeater', $post_id ) ) {
				the_row();
				$file = get_sub_field( 'document_resource' );
				if ( ! $file ) {
					continue;
				}
				$doc = self::normalize_file_static( $file );
				if ( $doc ) {
					$documents[] = $doc;
				}
			}
		}

		$card_class = 'omsar-kr-card';
		if ( ! $image_url ) {
			$card_class .= ' omsar-kr-card--no-image';
		}
		if ( $cards_clickable ) {
			$card_class .= ' omsar-kr-card--clickable';
		}
		$card_attrs = $cards_clickable ? ' data-href="' . esc_url( get_permalink( $post_id ) ) . '"' : '';

		ob_start();
		?>
		<article class="<?php echo esc_attr( $card_class ); ?>"<?php echo $card_attrs; ?>>
			<?php if ( $image_url ) : ?>
				<div class="omsar-kr-card-image">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
				</div>
			<?php endif; ?>
			<div class="omsar-kr-card-content">
				<h3 class="omsar-kr-card-title"><?php echo esc_html( $title ); ?></h3>
				
				<?php if ( ! empty( $documents ) ) : ?>
					<div class="omsar-kr-downloads">
						<span class="omsar-kr-downloads-label"><?php echo esc_html( $downloads_label ); ?>:</span>
						<ul class="omsar-kr-download-list">
							<?php foreach ( $documents as $doc ) : ?>
								<li>
									<a href="<?php echo esc_url( $doc['url'] ); ?>" class="omsar-kr-download-link" target="_blank" rel="noopener noreferrer" download>
										<i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
										<?php echo esc_html( $doc['label'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</article>
		<?php
		return ob_get_clean();
	}

	/**
	 * Normalize ACF file field (ID, array, or object) to url + label. Static version for AJAX.
	 *
	 * @param mixed $file Raw value from document_resource.
	 * @return array{url: string, label: string}|null
	 */
	public static function normalize_file_static( $file ) {
		$id = null;

		if ( is_numeric( $file ) && $file > 0 ) {
			$id = (int) $file;
		} elseif ( is_array( $file ) ) {
			if ( ! empty( $file['ID'] ) ) {
				$id = (int) $file['ID'];
			} elseif ( ! empty( $file['id'] ) ) {
				$id = (int) $file['id'];
			}
			if ( $id && ! empty( $file['url'] ) ) {
				$url   = $file['url'];
				$label = ! empty( $file['title'] ) ? $file['title'] : ( ! empty( $file['filename'] ) ? $file['filename'] : basename( $url ) );
				return [ 'url' => $url, 'label' => $label ?: __( 'Download', 'omsar' ) ];
			}
		} elseif ( is_object( $file ) && isset( $file->ID ) ) {
			$id = (int) $file->ID;
		}

		if ( ! $id ) {
			return null;
		}

		$url = wp_get_attachment_url( $id );
		if ( ! $url ) {
			return null;
		}

		$title    = get_the_title( $id );
		$filename = basename( get_attached_file( $id ) );
		$label    = ( $title && $title !== 'Attachment' ) ? $title : ( $filename ?: __( 'Download', 'omsar' ) );

		return [ 'url' => $url, 'label' => $label ];
	}

	/**
	 * Normalize ACF file field (ID, array, or object) to url + label.
	 *
	 * @param mixed $file Raw value from document_resource.
	 * @return array{url: string, label: string}|null
	 */
	private function normalize_file( $file ) {
		return self::normalize_file_static( $file );
	}
}

