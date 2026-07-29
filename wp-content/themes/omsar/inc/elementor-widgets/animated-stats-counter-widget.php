<?php
/**
 * Elementor OMSAR Animated Stats Counter Widget Class
 * 
 * Displays animated statistics counters with icons, numbers, and labels
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

class OMSAR_Animated_Stats_Counter_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_animated_stats_counter';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Animated Stats Counter', 'omsar' );
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
		return [ 'stats', 'counter', 'animated', 'statistics', 'number', 'metric' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Counter Items', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Icon Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => esc_html__( 'Image', 'omsar' ),
					'icon' => esc_html__( 'Icon', 'omsar' ),
				],
				'default' => 'image',
			]
		);

		$repeater->add_control(
			'icon_image',
			[
				'label' => esc_html__( 'Icon Image', 'omsar' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'omsar' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
				'condition' => [
					'icon_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'counter_value',
			[
				'label' => esc_html__( 'Counter Value', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 10000,
				'min' => 0,
				'step' => 1,
				'description' => esc_html__( 'The number to count up to', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'unit',
			[
				'label' => esc_html__( 'Unit', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '+',
				'placeholder' => esc_html__( 'e.g., +, %, K, M', 'omsar' ),
				'description' => esc_html__( 'Optional unit to display after the number', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'label_text',
			[
				'label' => esc_html__( 'Label Text', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Happy Customers', 'omsar' ),
				'placeholder' => esc_html__( 'Enter label text', 'omsar' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'counter_items',
			[
				'label' => esc_html__( 'Counter Items', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'counter_value' => 10000,
						'unit' => '+',
						'label_text' => esc_html__( 'Happy Customers', 'omsar' ),
					],
				],
				'title_field' => '{{{ label_text }}}',
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label' => esc_html__( 'Animation Duration (seconds)', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => 0.5,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 2,
				],
				'description' => esc_html__( 'How long the counting animation takes', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Icon
		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => esc_html__( 'Icon', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
						'min' => 20,
						'max' => 150,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-stats-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-stats-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_background_type',
			[
				'label' => esc_html__( 'Icon Background Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'color' => esc_html__( 'Color', 'omsar' ),
					'gradient' => esc_html__( 'Gradient', 'omsar' ),
					'none' => esc_html__( 'None', 'omsar' ),
				],
				'default' => 'gradient',
			]
		);

		$this->add_control(
			'icon_background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7c3aed',
				'condition' => [
					'icon_background_type' => 'color',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_gradient_from',
			[
				'label' => esc_html__( 'Gradient Color From', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7c3aed',
				'condition' => [
					'icon_background_type' => 'gradient',
				],
			]
		);

		$this->add_control(
			'icon_gradient_to',
			[
				'label' => esc_html__( 'Gradient Color To', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6366f1',
				'condition' => [
					'icon_background_type' => 'gradient',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4c1d95',
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-icon i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
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
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'omsar' ),
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
					'{{WRAPPER}} .omsar-stats-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Number
		$this->start_controls_section(
			'style_number_section',
			[
				'label' => esc_html__( 'Number', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'number_color',
			[
				'label' => esc_html__( 'Number Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1f2937',
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography',
				'label' => esc_html__( 'Number Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-stats-number',
				'default' => [
					'font_weight' => '700',
					'font_size' => [
						'size' => '40',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'number_spacing',
			[
				'label' => esc_html__( 'Number Spacing', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-number' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Label
		$this->start_controls_section(
			'style_label_section',
			[
				'label' => esc_html__( 'Label', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6b7280',
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => esc_html__( 'Label Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-stats-label',
				'default' => [
					'font_size' => [
						'size' => '15',
						'unit' => 'px',
					],
					'font_weight' => '400',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Card
		$this->start_controls_section(
			'style_card_section',
			[
				'label' => esc_html__( 'Card', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'card_background',
				'label' => esc_html__( 'Background', 'omsar' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .omsar-stats-counter-item',
				'default' => '#ffffff',
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
					'{{WRAPPER}} .omsar-stats-counter-item' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'top' => '30',
					'right' => '24',
					'bottom' => '30',
					'left' => '24',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-counter-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-stats-counter-item',
			]
		);

		$this->end_controls_section();

		// Style Section - Spacing
		$this->start_controls_section(
			'style_spacing_section',
			[
				'label' => esc_html__( 'Spacing', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'items_gap',
			[
				'label' => esc_html__( 'Items Gap', 'omsar' ),
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
					'{{WRAPPER}} .omsar-stats-counter-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => esc_html__( 'Auto', 'omsar' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'default' => 'auto',
				'description' => esc_html__( 'Auto will fit cards based on available space', 'omsar' ),
			]
		);

		$this->add_control(
			'card_max_width',
			[
				'label' => esc_html__( 'Card Max Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 600,
						'step' => 10,
					],
					'%' => [
						'min' => 20,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 240,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-stats-counter-item' => 'max-width: {{SIZE}}{{UNIT}};',
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
		$items = $settings['counter_items'];
		$animation_duration = ! empty( $settings['animation_duration']['size'] ) ? $settings['animation_duration']['size'] : 2;
		$icon_background_type = ! empty( $settings['icon_background_type'] ) ? $settings['icon_background_type'] : 'gradient';
		$icon_gradient_from = ! empty( $settings['icon_gradient_from'] ) ? $settings['icon_gradient_from'] : '#7c3aed';
		$icon_gradient_to = ! empty( $settings['icon_gradient_to'] ) ? $settings['icon_gradient_to'] : '#6366f1';
		$columns = ! empty( $settings['columns'] ) ? $settings['columns'] : 'auto';

		if ( empty( $items ) ) {
			return;
		}

		$widget_id = 'omsar-stats-counter-' . $this->get_id();
		
		// Build grid template columns
		$grid_columns = 'repeat(auto-fit, minmax(250px, 1fr))';
		if ( $columns !== 'auto' && is_numeric( $columns ) ) {
			$grid_columns = 'repeat(' . intval( $columns ) . ', 1fr)';
		}
		?>
		<div class="omsar-stats-counter-wrapper" id="<?php echo esc_attr( $widget_id ); ?>">
			<div class="omsar-stats-counter-grid" style="grid-template-columns: <?php echo esc_attr( $grid_columns ); ?>;">
				<?php foreach ( $items as $index => $item ) : 
					$counter_value = ! empty( $item['counter_value'] ) ? floatval( $item['counter_value'] ) : 0;
					$unit = ! empty( $item['unit'] ) ? esc_html( $item['unit'] ) : '';
					$label_text = ! empty( $item['label_text'] ) ? esc_html( $item['label_text'] ) : '';
					$icon_type = ! empty( $item['icon_type'] ) ? $item['icon_type'] : 'image';
					$item_id = $widget_id . '-item-' . $index;
					
					// Build icon background style
					$icon_bg_style = '';
					if ( $icon_background_type === 'gradient' ) {
						$icon_bg_style = 'background: linear-gradient(135deg, ' . esc_attr( $icon_gradient_from ) . ', ' . esc_attr( $icon_gradient_to ) . ');';
					} elseif ( $icon_background_type === 'color' && ! empty( $settings['icon_background_color'] ) ) {
						$icon_bg_style = 'background-color: ' . esc_attr( $settings['icon_background_color'] ) . ';';
					}
				?>
					<div class="omsar-stats-counter-item" data-counter-value="<?php echo esc_attr( $counter_value ); ?>" data-counter-duration="<?php echo esc_attr( $animation_duration ); ?>" id="<?php echo esc_attr( $item_id ); ?>">
						<?php if ( $icon_type === 'image' && ! empty( $item['icon_image']['url'] ) ) : ?>
							<div class="omsar-stats-icon" style="<?php echo $icon_bg_style; ?>">
								<img src="<?php echo esc_url( $item['icon_image']['url'] ); ?>" alt="<?php echo esc_attr( $label_text ); ?>" />
							</div>
						<?php elseif ( $icon_type === 'icon' && ! empty( $item['icon']['value'] ) ) : ?>
							<div class="omsar-stats-icon" style="<?php echo $icon_bg_style; ?>">
								<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
							</div>
						<?php endif; ?>
						
						<div class="omsar-stats-number" data-unit="<?php echo esc_attr( $unit ); ?>">
							<span class="omsar-counter-value">0</span><?php echo esc_html( $unit ); ?>
						</div>
						
						<?php if ( ! empty( $label_text ) ) : ?>
							<div class="omsar-stats-label"><?php echo esc_html( $label_text ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
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
		var items = settings.counter_items || [];
		var animationDuration = settings.animation_duration && settings.animation_duration.size ? settings.animation_duration.size : 2;
		var iconBackgroundType = settings.icon_background_type || 'gradient';
		var iconGradientFrom = settings.icon_gradient_from || '#7c3aed';
		var iconGradientTo = settings.icon_gradient_to || '#6366f1';
		var iconBackgroundColor = settings.icon_background_color || '#7c3aed';
		var columns = settings.columns || 'auto';
		var widgetId = 'omsar-stats-counter-' + view.getIDInt();
		
		// Build grid template columns
		var gridColumns = 'repeat(auto-fit, minmax(250px, 1fr))';
		if (columns !== 'auto' && !isNaN(parseInt(columns))) {
			gridColumns = 'repeat(' + parseInt(columns) + ', 1fr)';
		}
		#>
		<div class="omsar-stats-counter-wrapper" id="{{ widgetId }}">
			<div class="omsar-stats-counter-grid" style="grid-template-columns: {{ gridColumns }};">
				<# _.each( items, function( item, index ) {
					var counterValue = item.counter_value || 0;
					var unit = item.unit || '';
					var labelText = item.label_text || '';
					var iconType = item.icon_type || 'image';
					var itemId = widgetId + '-item-' + index;
					
					// Build icon background style
					var iconBgStyle = '';
					if (iconBackgroundType === 'gradient') {
						iconBgStyle = 'background: linear-gradient(135deg, ' + iconGradientFrom + ', ' + iconGradientTo + ');';
					} else if (iconBackgroundType === 'color') {
						iconBgStyle = 'background-color: ' + iconBackgroundColor + ';';
					}
				#>
					<div class="omsar-stats-counter-item" data-counter-value="{{ counterValue }}" data-counter-duration="{{ animationDuration }}" id="{{ itemId }}">
						<# if (iconType === 'image' && item.icon_image && item.icon_image.url) { #>
							<div class="omsar-stats-icon" style="{{ iconBgStyle }}">
								<img src="{{ item.icon_image.url }}" alt="{{ labelText }}" />
							</div>
						<# } else if (iconType === 'icon' && item.icon && item.icon.value) { #>
							<div class="omsar-stats-icon" style="{{ iconBgStyle }}">
								<#
								var iconHTML = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i', 'object' );
								if (iconHTML && iconHTML.value) {
									print(iconHTML.value);
								}
								#>
							</div>
						<# } #>
						
						<div class="omsar-stats-number" data-unit="{{ unit }}">
							<span class="omsar-counter-value">{{ counterValue }}</span>{{{ unit }}}
						</div>
						
						<# if (labelText) { #>
							<div class="omsar-stats-label">{{{ labelText }}}</div>
						<# } #>
					</div>
				<# }); #>
			</div>
		</div>
		<?php
	}
}

