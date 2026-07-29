<?php
/**
 * Elementor Statistics Card Widget Class
 * 
 * @package OMSAR
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class OMSAR_Stat_Card_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_stat_card';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Statistics Card', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter';
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
		return [ 'statistics', 'card', 'number', 'counter', 'stats' ];
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
			'number',
			[
				'label' => esc_html__( 'Number', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '120',
				'placeholder' => esc_html__( 'Enter number', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'unit',
			[
				'label' => esc_html__( 'Unit', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '+',
				'placeholder' => esc_html__( 'e.g., +, %, K, M', 'omsar' ),
				'description' => esc_html__( 'Optional unit to display after the number (e.g., +, %, K, M)', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Digital Services Launched', 'omsar' ),
				'placeholder' => esc_html__( 'Enter title', 'omsar' ),
				'rows' => 3,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'description' => esc_html__( 'Icon displayed at the bottom right of the card', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Number
		$this->start_controls_section(
			'style_number_section',
			[
				'label' => esc_html__( 'Number Style', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'number_color',
			[
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card .card-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .dt-stat-card .card-number',
				'default' => [
					'font_weight' => '600',
					'font_size' => [
						'size' => '35',
						'unit' => 'px',
					],
					'line_height' => [
						'size' => '1.1',
					],
					'letter_spacing' => [
						'size' => '-0.005',
					],
				],
			]
		);

		$this->add_control(
			'number_margin',
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card .card-number' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Title
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
				'label' => esc_html__( 'Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card .card-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .dt-stat-card .card-text',
				'default' => [
					'font_weight' => '500',
					'font_size' => [
						'size' => '24',
						'unit' => 'px',
					],
					'line_height' => [
						'size' => '1.4',
					],
					'letter_spacing' => [
						'size' => '-0.005',
					],
				],
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_padding',
			[
				'label' => esc_html__( 'Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '25',
					'right' => '25',
					'bottom' => '40',
					'left' => '25',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_min_height',
			[
				'label' => esc_html__( 'Min Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .dt-stat-card',
				'default' => [
					'horizontal' => -5,
					'vertical' => 0,
					'blur' => 15,
					'spread' => 0,
					'color' => 'rgba(0, 0, 0, 0.05)',
				],
			]
		);

		$this->add_control(
			'card_border',
			[
				'label' => esc_html__( 'Border', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'omsar' ),
					'solid' => esc_html__( 'Solid', 'omsar' ),
					'dashed' => esc_html__( 'Dashed', 'omsar' ),
					'dotted' => esc_html__( 'Dotted', 'omsar' ),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_width',
			[
				'label' => esc_html__( 'Border Width', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'condition' => [
					'card_border!' => 'none',
				],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '1',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label' => esc_html__( 'Border Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.05)',
				'condition' => [
					'card_border!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_opacity',
			[
				'label' => esc_html__( 'Icon Opacity', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.15,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card::before' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 300,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card::before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; background-size: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Hover Effect Section
		$this->start_controls_section(
			'hover_effect_section',
			[
				'label' => esc_html__( 'Hover Effect', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'enable_hover_effect',
			[
				'label' => esc_html__( 'Enable Hover Effect', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'hover_overlay_opacity',
			[
				'label' => esc_html__( 'Overlay Opacity', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.77,
				],
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card::after' => 'background: rgb(255 255 255 / {{SIZE}});',
				],
			]
		);

		$this->add_control(
			'hover_scale',
			[
				'label' => esc_html__( 'Hover Scale', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1.2,
						'step' => 0.01,
					],
				],
				'default' => [
					'size' => 1.02,
				],
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card:hover' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_control(
			'hover_gradient_color_from',
			[
				'label' => esc_html__( 'Gradient Color From', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E0F2FF',
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
			]
		);

		$this->add_control(
			'hover_gradient_color_to',
			[
				'label' => esc_html__( 'Gradient Color To', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6EA8FE',
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .dt-stat-card:hover',
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
				'default' => [
					'horizontal' => 0,
					'vertical' => 20,
					'blur' => 50,
					'spread' => 0,
					'color' => 'rgba(25, 45, 80, 0.15)',
				],
			]
		);

		$this->add_control(
			'hover_icon_opacity',
			[
				'label' => esc_html__( 'Icon Opacity on Hover', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.8,
				],
				'condition' => [
					'enable_hover_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-stat-card:hover::before' => 'opacity: {{SIZE}};',
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

		$number = ! empty( $settings['number'] ) ? esc_html( $settings['number'] ) : '';
		$unit = ! empty( $settings['unit'] ) ? esc_html( $settings['unit'] ) : '';
		$title = ! empty( $settings['title'] ) ? esc_html( $settings['title'] ) : '';
		$icon_url = '';
		$enable_hover = ! empty( $settings['enable_hover_effect'] ) && $settings['enable_hover_effect'] === 'yes';
		$gradient_from = ! empty( $settings['hover_gradient_color_from'] ) ? $settings['hover_gradient_color_from'] : '#E0F2FF';
		$gradient_to = ! empty( $settings['hover_gradient_color_to'] ) ? $settings['hover_gradient_color_to'] : '#6EA8FE';

		if ( ! empty( $settings['icon']['url'] ) ) {
			$icon_url = esc_url( $settings['icon']['url'] );
		}

		// Generate unique ID for this widget instance
		$widget_id = 'omsar-stat-card-' . $this->get_id();
		
		// Add inline styles
		?>
		<style>
			<?php if ( ! empty( $icon_url ) ) : ?>
			#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card::before {
				background-image: url(<?php echo esc_url( $icon_url ); ?>);
			}
			<?php endif; ?>
			
			<?php if ( $enable_hover ) : ?>
			#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card:hover {
				background: linear-gradient(180deg, <?php echo esc_attr( $gradient_from ); ?> 0%, <?php echo esc_attr( $gradient_to ); ?> 100%);
			}
			<?php else : ?>
			#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card:hover {
				transform: none !important;
				box-shadow: inherit !important;
			}
			#<?php echo esc_attr( $widget_id ); ?>.dt-stat-card::after {
				display: none;
			}
			<?php endif; ?>
		</style>
		<?php

		?>
		<div id="<?php echo esc_attr( $widget_id ); ?>" class="dt-stat-card elementor-stat-card">
			<div class="card-content">
				<?php if ( ! empty( $number ) ) : ?>
					<h3 class="card-number">
						<?php echo esc_html( $number ); ?><?php echo esc_html( $unit ); ?>
					</h3>
				<?php endif; ?>
				<?php if ( ! empty( $title ) ) : ?>
					<p class="card-text"><?php echo esc_html( $title ); ?></p>
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
		var number = settings.number || '';
		var unit = settings.unit || '';
		var title = settings.title || '';
		var iconUrl = '';
		var widgetId = 'omsar-stat-card-' + view.getIDInt();
		var enableHover = settings.enable_hover_effect === 'yes';
		var gradientFrom = settings.hover_gradient_color_from || '#E0F2FF';
		var gradientTo = settings.hover_gradient_color_to || '#6EA8FE';

		if (settings.icon && settings.icon.url) {
			iconUrl = settings.icon.url;
		}
		#>
		<style>
			<# if (iconUrl) { #>
			#{{ widgetId }}.dt-stat-card::before {
				background-image: url({{{ iconUrl }}});
			}
			<# } #>
			
			<# if (enableHover) { #>
			#{{ widgetId }}.dt-stat-card:hover {
				background: linear-gradient(180deg, {{ gradientFrom }} 0%, {{ gradientTo }} 100%);
			}
			<# } else { #>
			#{{ widgetId }}.dt-stat-card:hover {
				transform: none !important;
				box-shadow: inherit !important;
			}
			#{{ widgetId }}.dt-stat-card::after {
				display: none;
			}
			<# } #>
		</style>
		<div id="{{ widgetId }}" class="dt-stat-card elementor-stat-card">
			<div class="card-content">
				<# if (number) { #>
					<h3 class="card-number">{{{ number }}}{{{ unit }}}</h3>
				<# } #>
				<# if (title) { #>
					<p class="card-text">{{{ title }}}</p>
				<# } #>
			</div>
		</div>
		<?php
	}
}

