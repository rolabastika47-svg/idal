<?php
/**
 * Elementor Call to Action Widget Class
 * 
 * Displays a call-to-action banner with background (color/gradient/image), text content, and button
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
use Elementor\Group_Control_Text_Shadow;

class OMSAR_Call_To_Action_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_call_to_action';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Call to Action', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
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
		return [ 'cta', 'call to action', 'banner', 'button', 'promotion' ];
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
			'heading',
			[
				'label' => esc_html__( 'Heading', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Ready to Get Started?', 'omsar' ),
				'placeholder' => esc_html__( 'Enter heading text', 'omsar' ),
				'rows' => 2,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'subheading',
			[
				'label' => esc_html__( 'Subheading', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Join thousands of satisfied customers today', 'omsar' ),
				'placeholder' => esc_html__( 'Enter subheading text', 'omsar' ),
				'rows' => 2,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Start Free Trial', 'omsar' ),
				'placeholder' => esc_html__( 'Enter button text', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_url',
			[
				'label' => esc_html__( 'Button URL', 'omsar' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'omsar' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		// Background Section
		$this->start_controls_section(
			'background_section',
			[
				'label' => esc_html__( 'Background', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'background_type',
			[
				'label' => esc_html__( 'Background Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'gradient' => esc_html__( 'Gradient', 'omsar' ),
					'color' => esc_html__( 'Solid Color', 'omsar' ),
					'image' => esc_html__( 'Image', 'omsar' ),
				],
				'default' => 'gradient',
			]
		);

		$this->add_control(
			'gradient_color_from',
			[
				'label' => esc_html__( 'Gradient Color From', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8B4D9',
				'condition' => [
					'background_type' => 'gradient',
				],
			]
		);

		$this->add_control(
			'gradient_color_to',
			[
				'label' => esc_html__( 'Gradient Color To', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FF6B9D',
				'condition' => [
					'background_type' => 'gradient',
				],
			]
		);

		$this->add_control(
			'gradient_angle',
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
					'size' => 90,
				],
				'condition' => [
					'background_type' => 'gradient',
				],
				'description' => esc_html__( 'Gradient angle in degrees (0 = top to bottom, 90 = left to right)', 'omsar' ),
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8B4D9',
				'condition' => [
					'background_type' => 'color',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-banner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
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

		$this->add_control(
			'background_image_overlay',
			[
				'label' => esc_html__( 'Image Overlay Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.3)',
				'condition' => [
					'background_type' => 'image',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Banner
		$this->start_controls_section(
			'style_banner_section',
			[
				'label' => esc_html__( 'Banner Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'banner_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '40',
					'right' => '50',
					'bottom' => '40',
					'left' => '50',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-banner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'banner_border_radius',
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-banner' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'banner_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-cta-banner',
			]
		);

		$this->end_controls_section();

		// Style Section - Heading
		$this->start_controls_section(
			'style_heading_section',
			[
				'label' => esc_html__( 'Heading Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-cta-heading',
				'default' => [
					'font_weight' => '700',
					'font_size' => [
						'size' => '32',
						'unit' => 'px',
					],
					'line_height' => [
						'size' => '1.2',
					],
				],
			]
		);

		$this->add_control(
			'heading_margin',
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Subheading
		$this->start_controls_section(
			'style_subheading_section',
			[
				'label' => esc_html__( 'Subheading Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'subheading_color',
			[
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-subheading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subheading_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-cta-subheading',
				'default' => [
					'font_weight' => '400',
					'font_size' => [
						'size' => '16',
						'unit' => 'px',
					],
					'line_height' => [
						'size' => '1.5',
					],
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Button
		$this->start_controls_section(
			'style_button_section',
			[
				'label' => esc_html__( 'Button Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FF6B9D',
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-cta-button',
				'default' => [
					'font_weight' => '500',
					'font_size' => [
						'size' => '16',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '12',
					'right' => '30',
					'bottom' => '12',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-cta-button',
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .omsar-cta-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_transition',
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
					'{{WRAPPER}} .omsar-cta-button' => 'transition: all {{SIZE}}{{UNIT}};',
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

		$heading = ! empty( $settings['heading'] ) ? $settings['heading'] : '';
		$subheading = ! empty( $settings['subheading'] ) ? $settings['subheading'] : '';
		$button_text = ! empty( $settings['button_text'] ) ? esc_html( $settings['button_text'] ) : '';
		$button_url = ! empty( $settings['button_url']['url'] ) ? esc_url( $settings['button_url']['url'] ) : '';
		$button_target = ! empty( $settings['button_url']['is_external'] ) ? ' target="_blank"' : '';
		$button_nofollow = ! empty( $settings['button_url']['nofollow'] ) ? ' rel="nofollow"' : '';

		$background_type = ! empty( $settings['background_type'] ) ? $settings['background_type'] : 'gradient';
		$gradient_from = ! empty( $settings['gradient_color_from'] ) ? $settings['gradient_color_from'] : '#E8B4D9';
		$gradient_to = ! empty( $settings['gradient_color_to'] ) ? $settings['gradient_color_to'] : '#FF6B9D';
		$gradient_angle = ! empty( $settings['gradient_angle']['size'] ) ? $settings['gradient_angle']['size'] : 90;
		$background_image_url = '';
		$background_overlay = '';

		if ( $background_type === 'image' && ! empty( $settings['background_image']['url'] ) ) {
			$background_image_url = esc_url( $settings['background_image']['url'] );
			$overlay_color = ! empty( $settings['background_image_overlay'] ) ? $settings['background_image_overlay'] : 'rgba(0, 0, 0, 0.3)';
			$background_overlay = 'background-color: ' . esc_attr( $overlay_color ) . ';';
		}

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-cta-' . $this->get_id();

		// Add inline styles for background
		?>
		<style>
			<?php if ( $background_type === 'gradient' ) : ?>
			#<?php echo esc_attr( $widget_id ); ?>.omsar-cta-banner {
				background: linear-gradient(<?php echo esc_attr( $gradient_angle ); ?>deg, <?php echo esc_attr( $gradient_from ); ?> 0%, <?php echo esc_attr( $gradient_to ); ?> 100%);
			}
			<?php elseif ( $background_type === 'image' && ! empty( $background_image_url ) ) : ?>
			#<?php echo esc_attr( $widget_id ); ?>.omsar-cta-banner {
				background-image: url(<?php echo esc_url( $background_image_url ); ?>);
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
			}
			#<?php echo esc_attr( $widget_id ); ?>.omsar-cta-banner::before {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				<?php echo $background_overlay; ?>
				border-radius: inherit;
			}
			<?php endif; ?>
		</style>
		<?php

		?>
		<div id="<?php echo esc_attr( $widget_id ); ?>" class="omsar-cta-banner">
			<div class="omsar-cta-content">
				<?php if ( ! empty( $heading ) ) : ?>
					<h2 class="omsar-cta-heading"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $subheading ) ) : ?>
					<p class="omsar-cta-subheading"><?php echo wp_kses_post( $subheading ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $button_text ) && ! empty( $button_url ) ) : ?>
				<a href="<?php echo esc_url( $button_url ); ?>" class="omsar-cta-button"<?php echo $button_target . $button_nofollow; ?>>
					<?php echo esc_html( $button_text ); ?>
				</a>
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
		var heading = settings.heading || '';
		var subheading = settings.subheading || '';
		var buttonText = settings.button_text || '';
		var buttonUrl = settings.button_url && settings.button_url.url ? settings.button_url.url : '';
		var buttonTarget = settings.button_url && settings.button_url.is_external ? ' target="_blank"' : '';
		var buttonNofollow = settings.button_url && settings.button_url.nofollow ? ' rel="nofollow"' : '';

		var backgroundType = settings.background_type || 'gradient';
		var gradientFrom = settings.gradient_color_from || '#E8B4D9';
		var gradientTo = settings.gradient_color_to || '#FF6B9D';
		var gradientAngle = settings.gradient_angle && settings.gradient_angle.size ? settings.gradient_angle.size : 90;
		var backgroundImageUrl = '';
		var backgroundOverlay = '';

		if (backgroundType === 'image' && settings.background_image && settings.background_image.url) {
			backgroundImageUrl = settings.background_image.url;
			var overlayColor = settings.background_image_overlay || 'rgba(0, 0, 0, 0.3)';
			backgroundOverlay = 'background-color: ' + overlayColor + ';';
		}

		var widgetId = 'omsar-cta-' + view.getIDInt();
		#>
		<style>
			<# if (backgroundType === 'gradient') { #>
			#{{ widgetId }}.omsar-cta-banner {
				background: linear-gradient({{ gradientAngle }}deg, {{ gradientFrom }} 0%, {{ gradientTo }} 100%);
			}
			<# } else if (backgroundType === 'image' && backgroundImageUrl) { #>
			#{{ widgetId }}.omsar-cta-banner {
				background-image: url({{{ backgroundImageUrl }}});
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
			}
			#{{ widgetId }}.omsar-cta-banner::before {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				{{ backgroundOverlay }}
				border-radius: inherit;
			}
			<# } #>
		</style>
		<div id="{{ widgetId }}" class="omsar-cta-banner">
			<div class="omsar-cta-content">
				<# if (heading) { #>
					<h2 class="omsar-cta-heading">{{{ heading }}}</h2>
				<# } #>
				<# if (subheading) { #>
					<p class="omsar-cta-subheading">{{{ subheading }}}</p>
				<# } #>
			</div>
			<# if (buttonText && buttonUrl) { #>
				<a href="{{{ buttonUrl }}}" class="omsar-cta-button"{{{ buttonTarget }}}{{{ buttonNofollow }}}>
					{{{ buttonText }}}
				</a>
			<# } #>
		</div>
		<?php
	}
}
