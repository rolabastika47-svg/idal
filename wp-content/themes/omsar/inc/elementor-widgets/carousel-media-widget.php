<?php
/**
 * Elementor Custom Carousel Media Widget Class
 * 
 * Displays a customizable carousel with images, text content, navigation arrows, and pagination bullets
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
use Elementor\Group_Control_Background;

class OMSAR_Carousel_Media_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_carousel_media';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Carousel Media', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-slider-push';
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
		return [ 'carousel', 'slider', 'media', 'slides', 'gallery' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section - Slides
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Slides', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_image',
			[
				'label' => esc_html__( 'Background Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Optional background image for the slide', 'omsar' ),
			]
		);

		$repeater->add_control(
			'slide_text',
			[
				'label' => esc_html__( 'Slide Text', 'omsar' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Slide Content', 'omsar' ),
				'placeholder' => esc_html__( 'Enter slide text content', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'slide_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-carousel-media-item.elementor-repeater-item-{{_id}}',
				'fields_options' => [
					'background' => [
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#60a5fa',
					],
					'color_b' => [
						'default' => '#a78bfa',
					],
				],
			]
		);

		$this->add_control(
			'carousel_slides',
			[
				'label' => esc_html__( 'Carousel Slides', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'slide_text' => esc_html__( 'Slide Content', 'omsar' ),
						'slide_background_background' => 'gradient',
						'slide_background_color' => '#60a5fa',
						'slide_background_color_b' => '#a78bfa',
					],
					[
						'slide_text' => esc_html__( 'Slide Content', 'omsar' ),
						'slide_background_background' => 'gradient',
						'slide_background_color' => '#60a5fa',
						'slide_background_color_b' => '#a78bfa',
					],
					[
						'slide_text' => esc_html__( 'Slide Content', 'omsar' ),
						'slide_background_background' => 'gradient',
						'slide_background_color' => '#60a5fa',
						'slide_background_color_b' => '#a78bfa',
					],
				],
				'title_field' => '{{{ slide_text }}}',
			]
		);

		$this->end_controls_section();

		// Settings Section
		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'Carousel Settings', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_timeout',
			[
				'label' => esc_html__( 'Autoplay Timeout (ms)', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'min' => 1000,
				'max' => 10000,
				'step' => 500,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'full_width',
			[
				'label' => esc_html__( 'Full Width', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Enable to make the carousel span edge-to-edge without gaps. Disable for contained layout with padding.', 'omsar' ),
			]
		);

		$this->add_control(
			'slide_height',
			[
				'label' => esc_html__( 'Slide Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 800,
						'step' => 10,
					],
					'vh' => [
						'min' => 20,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label' => esc_html__( 'Arrows Position', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'on_image' => esc_html__( 'On Image', 'omsar' ),
					'outside' => esc_html__( 'Outside', 'omsar' ),
				],
				'default' => 'on_image',
				'description' => esc_html__( 'Choose whether arrows appear on the image or outside the carousel', 'omsar' ),
			]
		);

		$this->add_control(
			'bullets_position',
			[
				'label' => esc_html__( 'Bullets Position', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'on_image' => esc_html__( 'On Image', 'omsar' ),
					'outside' => esc_html__( 'Outside', 'omsar' ),
				],
				'default' => 'on_image',
				'description' => esc_html__( 'Choose whether bullets appear on the image or outside the carousel', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Slide Content
		$this->start_controls_section(
			'style_content_section',
			[
				'label' => esc_html__( 'Slide Content', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-carousel-media-text',
				'default' => [
					'font_size' => [
						'size' => '24',
						'unit' => 'px',
					],
					'font_weight' => '500',
				],
			]
		);

		$this->add_control(
			'text_alignment',
			[
				'label' => esc_html__( 'Text Alignment', 'omsar' ),
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
					'{{WRAPPER}} .omsar-carousel-media-text' => 'text-align: {{VALUE}};',
				],
				'prefix_class' => 'omsar-text-align-',
			]
		);

		$this->end_controls_section();

		// Style Section - Navigation Arrows
		$this->start_controls_section(
			'style_arrows_section',
			[
				'label' => esc_html__( 'Navigation Arrows', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'arrow_background',
			[
				'label' => esc_html__( 'Arrow Background', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0f2fe',
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-nav-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0ea5e9',
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-nav-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_size',
			[
				'label' => esc_html__( 'Arrow Size', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 80,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 48,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-nav-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'label' => esc_html__( 'Border', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-carousel-nav-button',
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_border_radius',
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
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-nav-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_outside_spacing',
			[
				'label' => esc_html__( 'Arrow Outside Spacing', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}}.omsar-carousel-arrows-outside .omsar-carousel-nav-prev' => 'left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.omsar-carousel-arrows-outside .omsar-carousel-nav-next' => 'right: -{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_arrows' => 'yes',
					'arrows_position' => 'outside',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Pagination Bullets
		$this->start_controls_section(
			'style_bullets_section',
			[
				'label' => esc_html__( 'Pagination Bullets', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_bullets',
			[
				'label' => esc_html__( 'Show Bullets', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'omsar' ),
				'label_off' => esc_html__( 'Hide', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'bullet_active_color',
			[
				'label' => esc_html__( 'Active Bullet Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#60a5fa',
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media .owl-dots .owl-dot.active span' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
				],
			]
		);

		$this->add_control(
			'bullet_inactive_color',
			[
				'label' => esc_html__( 'Inactive Bullet Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#d1d5db',
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media .owl-dots .owl-dot:not(.active) span' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
				],
			]
		);

		$this->add_control(
			'bullet_size',
			[
				'label' => esc_html__( 'Bullet Size', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 4,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media .owl-dots .owl-dot:not(.active) span' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
				],
			]
		);

		$this->add_control(
			'bullet_active_width',
			[
				'label' => esc_html__( 'Active Bullet Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 40,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media .owl-dots .owl-dot.active span' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
				],
			]
		);

		$this->add_control(
			'bullet_spacing',
			[
				'label' => esc_html__( 'Bullet Spacing', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-carousel-media .owl-dots .owl-dot' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
				],
			]
		);

		$this->add_control(
			'bullet_outside_spacing',
			[
				'label' => esc_html__( 'Bullets Top Spacing (Outside)', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}.omsar-carousel-bullets-outside .omsar-carousel-media .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_bullets' => 'yes',
					'bullets_position' => 'outside',
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
		$slides = $settings['carousel_slides'];
		$widget_id = 'omsar-carousel-media-' . $this->get_id();

		if ( empty( $slides ) ) {
			return;
		}

		$autoplay = ! empty( $settings['autoplay'] ) && $settings['autoplay'] === 'yes' ? 'true' : 'false';
		$autoplay_timeout = ! empty( $settings['autoplay_timeout'] ) ? intval( $settings['autoplay_timeout'] ) : 5000;
		$loop = ! empty( $settings['loop'] ) && $settings['loop'] === 'yes' ? 'true' : 'false';
		$show_arrows = ! empty( $settings['show_arrows'] ) && $settings['show_arrows'] === 'yes';
		$show_bullets = ! empty( $settings['show_bullets'] ) && $settings['show_bullets'] === 'yes';
		$arrows_position = ! empty( $settings['arrows_position'] ) ? $settings['arrows_position'] : 'on_image';
		$bullets_position = ! empty( $settings['bullets_position'] ) ? $settings['bullets_position'] : 'on_image';
		$full_width = ! empty( $settings['full_width'] ) && $settings['full_width'] === 'yes';
		$arrows_position_class = 'omsar-carousel-arrows-' . esc_attr( $arrows_position );
		$bullets_position_class = 'omsar-carousel-bullets-' . esc_attr( $bullets_position );
		$full_width_class = $full_width ? 'omsar-carousel-full-width' : 'omsar-carousel-contained';
		?>
		<div class="omsar-carousel-media-wrapper <?php echo esc_attr( $arrows_position_class ); ?> <?php echo esc_attr( $bullets_position_class ); ?> <?php echo esc_attr( $full_width_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>">
			<div class="omsar-carousel-media owl-carousel owl-theme">
				<?php foreach ( $slides as $index => $slide ) : 
					$slide_text = ! empty( $slide['slide_text'] ) ? $slide['slide_text'] : '';
					$slide_image = ! empty( $slide['slide_image']['url'] ) ? $slide['slide_image']['url'] : '';
					$item_class = 'omsar-carousel-media-item elementor-repeater-item-' . $slide['_id'];
				?>
					<div class="<?php echo esc_attr( $item_class ); ?>">
						<?php if ( $slide_image ) : ?>
							<div class="omsar-carousel-media-bg-image" style="background-image: url('<?php echo esc_url( $slide_image ); ?>');"></div>
						<?php endif; ?>
						<div class="omsar-carousel-media-content">
							<?php if ( $slide_text ) : ?>
								<div class="omsar-carousel-media-text"><?php echo wp_kses_post( $slide_text ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $show_arrows ) : ?>
				<button class="omsar-carousel-nav-button omsar-carousel-nav-prev" aria-label="<?php echo esc_attr__( 'Previous', 'omsar' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<button class="omsar-carousel-nav-button omsar-carousel-nav-next" aria-label="<?php echo esc_attr__( 'Next', 'omsar' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			<?php endif; ?>
		</div>
		<script>
		(function() {
			var widgetId = '<?php echo esc_js( $widget_id ); ?>';
			var initAttempts = 0;
			var maxAttempts = 50;
			
			function initCarousel() {
				initAttempts++;
				if (initAttempts > maxAttempts) {
					console.warn('Carousel initialization failed after ' + maxAttempts + ' attempts');
					return;
				}
				
				if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') {
					setTimeout(initCarousel, 100);
					return;
				}
				
				var $ = jQuery;
				var $carousel = $('#' + widgetId + ' .omsar-carousel-media');
				if ($carousel.length && !$carousel.hasClass('owl-loaded')) {
					var carouselOptions = {
						items: 1,
						loop: <?php echo esc_js( $loop ); ?>,
						autoplay: <?php echo esc_js( $autoplay ); ?>,
						autoplayTimeout: <?php echo esc_js( $autoplay_timeout ); ?>,
						autoplayHoverPause: true,
						nav: false,
						dots: <?php echo $show_bullets ? 'true' : 'false'; ?>,
						smartSpeed: 500,
						margin: 0
					};
					
					$carousel.owlCarousel(carouselOptions);
					
					<?php if ( $show_arrows ) : ?>
					// Custom navigation
					$('#' + widgetId + ' .omsar-carousel-nav-prev').off('click').on('click', function(e) {
						e.preventDefault();
						$carousel.trigger('prev.owl.carousel');
					});
					
					$('#' + widgetId + ' .omsar-carousel-nav-next').off('click').on('click', function(e) {
						e.preventDefault();
						$carousel.trigger('next.owl.carousel');
					});
					<?php endif; ?>
				}
			}
			
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initCarousel);
			} else {
				initCarousel();
			}
			
			// Also try on window load as fallback
			window.addEventListener('load', function() {
				setTimeout(initCarousel, 100);
			});
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
		var slides = settings.carousel_slides || [];
		var widgetId = 'omsar-carousel-media-' + view.getIDInt();
		var autoplay = settings.autoplay === 'yes' ? 'true' : 'false';
		var autoplayTimeout = settings.autoplay_timeout || 5000;
		var loop = settings.loop === 'yes' ? 'true' : 'false';
		var showArrows = settings.show_arrows === 'yes';
		var showBullets = settings.show_bullets === 'yes';
		var arrowsPosition = settings.arrows_position || 'on_image';
		var bulletsPosition = settings.bullets_position || 'on_image';
		var fullWidth = settings.full_width === 'yes';
		var arrowsPositionClass = 'omsar-carousel-arrows-' + arrowsPosition;
		var bulletsPositionClass = 'omsar-carousel-bullets-' + bulletsPosition;
		var fullWidthClass = fullWidth ? 'omsar-carousel-full-width' : 'omsar-carousel-contained';
		#>
		<div class="omsar-carousel-media-wrapper {{ arrowsPositionClass }} {{ bulletsPositionClass }} {{ fullWidthClass }}" id="{{ widgetId }}">
			<div class="omsar-carousel-media owl-carousel owl-theme">
				<# _.each( slides, function( slide, index ) {
					var slideText = slide.slide_text || '';
					var slideImage = slide.slide_image && slide.slide_image.url ? slide.slide_image.url : '';
					var itemClass = 'omsar-carousel-media-item elementor-repeater-item-' + slide._id;
				#>
					<div class="{{ itemClass }}">
						<# if (slideImage) { #>
							<div class="omsar-carousel-media-bg-image" style="background-image: url('{{ slideImage }}');"></div>
						<# } #>
						<div class="omsar-carousel-media-content">
							<# if (slideText) { #>
								<div class="omsar-carousel-media-text">{{{ slideText }}}</div>
							<# } #>
						</div>
					</div>
				<# }); #>
			</div>
			<# if (showArrows) { #>
				<button class="omsar-carousel-nav-button omsar-carousel-nav-prev" aria-label="<?php echo esc_attr__( 'Previous', 'omsar' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<button class="omsar-carousel-nav-button omsar-carousel-nav-next" aria-label="<?php echo esc_attr__( 'Next', 'omsar' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			<# } #>
		</div>
		<script>
		(function() {
			var widgetId = '{{ widgetId }}';
			var initAttempts = 0;
			var maxAttempts = 50;
			
			function initCarousel() {
				initAttempts++;
				if (initAttempts > maxAttempts) {
					console.warn('Carousel initialization failed after ' + maxAttempts + ' attempts');
					return;
				}
				
				if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') {
					setTimeout(initCarousel, 100);
					return;
				}
				
				var $ = jQuery;
				var $carousel = $('#' + widgetId + ' .omsar-carousel-media');
				if ($carousel.length && !$carousel.hasClass('owl-loaded')) {
					var carouselOptions = {
						items: 1,
						loop: {{ loop }},
						autoplay: {{ autoplay }},
						autoplayTimeout: {{ autoplayTimeout }},
						autoplayHoverPause: true,
						nav: false,
						dots: {{ showBullets ? 'true' : 'false' }},
						smartSpeed: 500,
						margin: 0
					};
					
					$carousel.owlCarousel(carouselOptions);
					
					<# if (showArrows) { #>
					$('#' + widgetId + ' .omsar-carousel-nav-prev').off('click').on('click', function(e) {
						e.preventDefault();
						$carousel.trigger('prev.owl.carousel');
					});
					
					$('#' + widgetId + ' .omsar-carousel-nav-next').off('click').on('click', function(e) {
						e.preventDefault();
						$carousel.trigger('next.owl.carousel');
					});
					<# } #>
				}
			}
			
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initCarousel);
			} else {
				initCarousel();
			}
			
			window.addEventListener('load', function() {
				setTimeout(initCarousel, 100);
			});
		})();
		</script>
		<?php
	}
}

