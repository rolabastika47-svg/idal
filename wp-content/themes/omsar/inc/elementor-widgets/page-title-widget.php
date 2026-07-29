<?php
/**
 * Elementor Page Title Widget Class
 * 
 * Displays the current page/post title dynamically
 * 
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

class OMSAR_Page_Title_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_page_title';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Page Title', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-title';
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
		return [ 'title', 'page title', 'post title', 'heading', 'h1' ];
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
			'title_info',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<div style="background: #f0f0f1; padding: 10px; border-radius: 4px; margin-bottom: 10px;"><strong>' . esc_html__( 'Note:', 'omsar' ) . '</strong> ' . esc_html__( 'This widget automatically displays the current page or post title. The title will change based on the page you are viewing.', 'omsar' ) . '</div>',
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'DIV',
					'span' => 'SPAN',
					'p' => 'P',
				],
				'description' => esc_html__( 'Choose the HTML tag for the title', 'omsar' ),
			]
		);

		$this->add_control(
			'fallback_text',
			[
				'label' => esc_html__( 'Fallback Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Text to show if no title is found', 'omsar' ),
				'description' => esc_html__( 'Optional text to display if the page has no title (e.g., on archive pages)', 'omsar' ),
			]
		);

		$this->add_control(
			'link_title',
			[
				'label' => esc_html__( 'Link to Page', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Make the title clickable and link to the current page', 'omsar' ),
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

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-page-title',
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
					'justify' => [
						'title' => esc_html__( 'Justified', 'omsar' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-page-title',
			]
		);

		$this->end_controls_section();

		// Link Style Section
		$this->start_controls_section(
			'link_style_section',
			[
				'label' => esc_html__( 'Link Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Link Hover Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_text_decoration',
			[
				'label' => esc_html__( 'Text Decoration', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'omsar' ),
					'underline' => esc_html__( 'Underline', 'omsar' ),
					'overline' => esc_html__( 'Overline', 'omsar' ),
					'line-through' => esc_html__( 'Line Through', 'omsar' ),
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-page-title a' => 'text-decoration: {{VALUE}};',
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
		
		// Get the current page/post title
		$title = '';
		
		if ( is_singular() ) {
			// Single post, page, or custom post type
			$title = get_the_title();
		} elseif ( is_home() && ! is_front_page() ) {
			// Blog page
			$title = get_the_title( get_option( 'page_for_posts' ) );
		} elseif ( is_front_page() && is_home() ) {
			// Default homepage (blog)
			$title = get_bloginfo( 'name' );
		} elseif ( is_front_page() ) {
			// Static homepage
			$title = get_the_title();
		} elseif ( is_archive() ) {
			// Archive pages
			$title = get_the_archive_title();
			// Clean up archive title (remove "Archives: ", "Category: ", etc.)
			$title = str_replace( array( 'Archives: ', 'Category: ', 'Tag: ', 'Author: ' ), '', $title );
			$title = wp_strip_all_tags( $title );
		} elseif ( is_search() ) {
			// Search results
			$title = sprintf( pll__('Search Results for: %s'), get_search_query() );
		} elseif ( is_404() ) {
			// 404 page
			$title = esc_html__( 'Page Not Found', 'omsar' );
		} else {
			// Fallback
			$title = get_bloginfo( 'name' );
		}
		
		// Use fallback text if title is empty
		if ( empty( $title ) && ! empty( $settings['fallback_text'] ) ) {
			$title = $settings['fallback_text'];
		}
		
		// If still empty, use site name
		if ( empty( $title ) ) {
			$title = get_bloginfo( 'name' );
		}
		
		// Get HTML tag
		$html_tag = ! empty( $settings['html_tag'] ) ? $settings['html_tag'] : 'h1';
		
		// Check if title should be linked
		$link_title = ! empty( $settings['link_title'] ) && $settings['link_title'] === 'yes';
		
		// Build the title output
		$title_output = esc_html( $title );
		
		if ( $link_title && is_singular() ) {
			$title_url = get_permalink();
			$title_output = '<a href="' . esc_url( $title_url ) . '">' . $title_output . '</a>';
		}
		
		// Output the title
		?>
		<<?php echo esc_attr( $html_tag ); ?> class="omsar-page-title">
			<?php echo $title_output; ?>
		</<?php echo esc_attr( $html_tag ); ?>>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var htmlTag = settings.html_tag || 'h1';
		var title = '';
		
		// In editor, show a preview title
		if ( elementor.config.post_title ) {
			title = elementor.config.post_title;
		} else {
			title = '<?php esc_html_e( 'Page Title', 'omsar' ); ?>';
		}
		
		var linkTitle = settings.link_title === 'yes';
		var titleOutput = title;
		
		if ( linkTitle ) {
			titleOutput = '<a href="#">' + title + '</a>';
		}
		#>
		<{{{ htmlTag }}} class="omsar-page-title">
			{{{ titleOutput }}}
		</{{{ htmlTag }}}>
		<?php
	}
}
