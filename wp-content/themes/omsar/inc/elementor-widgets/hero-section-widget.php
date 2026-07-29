<?php
/**
 * Elementor Custom Hero Section Widget Class
 * 
 * Displays a customizable hero section with background, title, description, and buttons
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
use Elementor\Group_Control_Text_Shadow;

class OMSAR_Hero_Section_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_hero_section';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Hero Section', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-banner';
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
		return [ 'hero', 'banner', 'header', 'cta', 'call to action' ];
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
			'hero_title',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Build Amazing Websites', 'omsar' ),
				'placeholder' => esc_html__( 'Enter hero title', 'omsar' ),
				'rows' => 2,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'hero_description',
			[
				'label' => esc_html__( 'Description', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Create stunning, professional websites with our powerful theme builder', 'omsar' ),
				'placeholder' => esc_html__( 'Enter hero description', 'omsar' ),
				'rows' => 3,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		// Buttons Section
		$this->start_controls_section(
			'buttons_section',
			[
				'label' => esc_html__( 'Buttons', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Get Started', 'omsar' ),
				'placeholder' => esc_html__( 'Enter button text', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Button Link', 'omsar' ),
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

		$repeater->add_control(
			'button_style',
			[
				'label' => esc_html__( 'Button Style', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'primary' => esc_html__( 'Primary', 'omsar' ),
					'secondary' => esc_html__( 'Secondary', 'omsar' ),
					'outline' => esc_html__( 'Outline', 'omsar' ),
				],
				'default' => 'primary',
			]
		);

		$this->add_control(
			'hero_buttons',
			[
				'label' => esc_html__( 'Buttons', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'button_text' => esc_html__( 'Get Started', 'omsar' ),
						'button_style' => 'primary',
					],
					[
						'button_text' => esc_html__( 'Learn More', 'omsar' ),
						'button_style' => 'secondary',
					],
				],
				'title_field' => '{{{ button_text }}}',
			]
		);

		$this->add_control(
			'buttons_alignment',
			[
				'label' => esc_html__( 'Buttons Alignment', 'omsar' ),
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
			]
		);

		$this->end_controls_section();

		// Layout Section
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
				'description' => esc_html__( 'Enable to make the hero section span edge-to-edge without gaps. Disable for contained layout with padding.', 'omsar' ),
			]
		);

		$this->add_control(
			'hero_height',
			[
				'label' => esc_html__( 'Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 1000,
						'step' => 10,
					],
					'vh' => [
						'min' => 30,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-section' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label' => esc_html__( 'Content Alignment', 'omsar' ),
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
					'{{WRAPPER}} .omsar-hero-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '80',
					'right' => '20',
					'bottom' => '80',
					'left' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Background
		$this->start_controls_section(
			'style_background_section',
			[
				'label' => esc_html__( 'Background', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hero_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-hero-section',
				'fields_options' => [
					'background' => [
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#a78bfa',
					],
					'color_b' => [
						'default' => '#7c3aed',
					],
				],
			]
		);

		$this->add_control(
			'hero_border_radius',
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
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-section' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Title
		$this->start_controls_section(
			'style_title_section',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
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
					'{{WRAPPER}} .omsar-hero-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-hero-title',
				'default' => [
					'font_size' => [
						'size' => '48',
						'unit' => 'px',
					],
					'font_weight' => '700',
					'line_height' => [
						'size' => '1.2',
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-hero-title',
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Bottom Spacing', 'omsar' ),
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Description
		$this->start_controls_section(
			'style_description_section',
			[
				'label' => esc_html__( 'Description', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Description Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-hero-description',
				'default' => [
					'font_size' => [
						'size' => '18',
						'unit' => 'px',
					],
					'font_weight' => '400',
					'line_height' => [
						'size' => '1.6',
					],
				],
			]
		);

		$this->add_control(
			'description_spacing',
			[
				'label' => esc_html__( 'Bottom Spacing', 'omsar' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Buttons
		$this->start_controls_section(
			'style_buttons_section',
			[
				'label' => esc_html__( 'Buttons', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'buttons_spacing',
			[
				'label' => esc_html__( 'Button Spacing', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-buttons .omsar-hero-button:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Button Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-hero-button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Button Border Radius', 'omsar' ),
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
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => esc_html__( 'Button Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => '14',
					'right' => '32',
					'bottom' => '14',
					'left' => '32',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Primary Button Style
		$this->add_control(
			'primary_button_heading',
			[
				'label' => esc_html__( 'Primary Button', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'primary_button_bg',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-primary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'primary_button_text',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0ea5e9',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-primary' => 'color: {{VALUE}};',
				],
			]
		);

		// Secondary Button Style
		$this->add_control(
			'secondary_button_heading',
			[
				'label' => esc_html__( 'Secondary Button', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'secondary_button_bg',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c084fc',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-secondary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'secondary_button_text',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-secondary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'secondary_button_border',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-secondary' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'secondary_button_border_width',
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
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-secondary' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		// Outline Button Style
		$this->add_control(
			'outline_button_heading',
			[
				'label' => esc_html__( 'Outline Button', 'omsar' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'outline_button_text',
			[
				'label' => esc_html__( 'Text Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-outline' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'outline_button_border',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-outline' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'outline_button_border_width',
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
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-hero-button.button-outline' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid; background-color: transparent;',
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
		$title = ! empty( $settings['hero_title'] ) ? $settings['hero_title'] : '';
		$description = ! empty( $settings['hero_description'] ) ? $settings['hero_description'] : '';
		$buttons = ! empty( $settings['hero_buttons'] ) ? $settings['hero_buttons'] : [];
		$buttons_alignment = ! empty( $settings['buttons_alignment'] ) ? $settings['buttons_alignment'] : 'center';
		$full_width = ! empty( $settings['full_width'] ) && $settings['full_width'] === 'yes';
		$full_width_class = $full_width ? 'omsar-hero-full-width' : 'omsar-hero-contained';
		?>
		<div class="omsar-hero-section <?php echo esc_attr( $full_width_class ); ?>">
			<div class="omsar-hero-content">
				<?php if ( $title ) : ?>
					<h1 class="omsar-hero-title"><?php echo esc_html( $title ); ?></h1>
				<?php endif; ?>
				
				<?php if ( $description ) : ?>
					<p class="omsar-hero-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				
				<?php if ( ! empty( $buttons ) ) : ?>
					<div class="omsar-hero-buttons" style="text-align: <?php echo esc_attr( $buttons_alignment ); ?>;">
						<?php foreach ( $buttons as $button ) : 
							$button_text = ! empty( $button['button_text'] ) ? $button['button_text'] : '';
							$button_link = ! empty( $button['button_link']['url'] ) ? $button['button_link']['url'] : '#';
							$button_style = ! empty( $button['button_style'] ) ? $button['button_style'] : 'primary';
							$target = ! empty( $button['button_link']['is_external'] ) ? 'target="_blank"' : '';
							$nofollow = ! empty( $button['button_link']['nofollow'] ) ? 'rel="nofollow"' : '';
							
							if ( empty( $button_text ) ) {
								continue;
							}
						?>
							<a href="<?php echo esc_url( $button_link ); ?>" 
							   class="omsar-hero-button button-<?php echo esc_attr( $button_style ); ?>" 
							   <?php echo $target; ?> 
							   <?php echo $nofollow; ?>>
								<?php echo esc_html( $button_text ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var title = settings.hero_title || '';
		var description = settings.hero_description || '';
		var buttons = settings.hero_buttons || [];
		var buttonsAlignment = settings.buttons_alignment || 'center';
		var fullWidth = settings.full_width === 'yes';
		var fullWidthClass = fullWidth ? 'omsar-hero-full-width' : 'omsar-hero-contained';
		#>
		<div class="omsar-hero-section {{ fullWidthClass }}">
			<div class="omsar-hero-content">
				<# if (title) { #>
					<h1 class="omsar-hero-title">{{{ title }}}</h1>
				<# } #>
				
				<# if (description) { #>
					<p class="omsar-hero-description">{{{ description }}}</p>
				<# } #>
				
				<# if (buttons.length > 0) { #>
					<div class="omsar-hero-buttons" style="text-align: {{ buttonsAlignment }};">
						<# _.each( buttons, function( button ) {
							var buttonText = button.button_text || '';
							var buttonLink = button.button_link && button.button_link.url ? button.button_link.url : '#';
							var buttonStyle = button.button_style || 'primary';
							var target = button.button_link && button.button_link.is_external ? 'target="_blank"' : '';
							var nofollow = button.button_link && button.button_link.nofollow ? 'rel="nofollow"' : '';
							
							if (!buttonText) return;
						#>
							<a href="{{ buttonLink }}" 
							   class="omsar-hero-button button-{{ buttonStyle }}" 
							   {{ target }} 
							   {{ nofollow }}>
								{{{ buttonText }}}
							</a>
						<# }); #>
					</div>
				<# } #>
			</div>
		</div>
		<?php
	}
}

