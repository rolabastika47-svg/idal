<?php
/**
 * Elementor OMSAR Progress Bar Widget Class
 * 
 * Displays customizable progress bars in horizontal or circular format
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

class OMSAR_Progress_Bar_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_progress_bar';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'OMSAR Progress Bar', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-skill-bar';
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
		return [ 'progress', 'bar', 'circular', 'percentage', 'skill', 'indicator' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Progress Bars', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'global_unit',
			[
				'label' => esc_html__( 'Global Unit', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => '%',
				'placeholder' => esc_html__( 'e.g., %, +, K', 'omsar' ),
				'description' => esc_html__( 'Default unit for all progress bars. Can be overridden per item.', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'progress_type',
			[
				'label' => esc_html__( 'Progress Type', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'omsar' ),
					'circular' => esc_html__( 'Circular', 'omsar' ),
				],
				'default' => 'horizontal',
				'description' => esc_html__( 'Choose horizontal (bar) or circular (donut chart) style for all items', 'omsar' ),
			]
		);

		$this->add_control(
			'circular_layout',
			[
				'label' => esc_html__( 'Circular Items Layout', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'vertical' => esc_html__( 'Vertical (Stacked)', 'omsar' ),
					'horizontal' => esc_html__( 'Horizontal (Side by Side)', 'omsar' ),
				],
				'default' => 'vertical',
				'condition' => [
					'progress_type' => 'circular',
				],
				'description' => esc_html__( 'How to arrange circular progress bars', 'omsar' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'progress_value',
			[
				'label' => esc_html__( 'Progress Value', 'omsar' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 75,
				'description' => esc_html__( 'Enter progress value (0-100)', 'omsar' ),
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
				'default' => '',
				'placeholder' => esc_html__( 'Leave empty to use global unit', 'omsar' ),
				'description' => esc_html__( 'Override global unit for this item (optional)', 'omsar' ),
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
				'default' => esc_html__( 'Progress', 'omsar' ),
				'placeholder' => esc_html__( 'Enter label text', 'omsar' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'show_percentage',
			[
				'label' => esc_html__( 'Show Percentage', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$repeater->add_control(
			'use_gradient',
			[
				'label' => esc_html__( 'Use Gradient', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Enable linear gradient for progress fill', 'omsar' ),
			]
		);

		$repeater->add_control(
			'gradient_color_from',
			[
				'label' => esc_html__( 'Gradient Color From', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7c3aed',
				'condition' => [
					'use_gradient' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'gradient_color_to',
			[
				'label' => esc_html__( 'Gradient Color To', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#a855f7',
				'condition' => [
					'use_gradient' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'gradient_direction',
			[
				'label' => esc_html__( 'Gradient Direction', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'to right' => esc_html__( 'Left to Right', 'omsar' ),
					'to bottom' => esc_html__( 'Top to Bottom', 'omsar' ),
					'to left' => esc_html__( 'Right to Left', 'omsar' ),
					'to top' => esc_html__( 'Bottom to Top', 'omsar' ),
					'45deg' => esc_html__( 'Diagonal (45°)', 'omsar' ),
					'135deg' => esc_html__( 'Diagonal (135°)', 'omsar' ),
				],
				'default' => 'to right',
				'condition' => [
					'use_gradient' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'progress_color',
			[
				'label' => esc_html__( 'Progress Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7c3aed',
				'condition' => [
					'use_gradient!' => 'yes',
				],
			]
		);

		$this->add_control(
			'progress_items',
			[
				'label' => esc_html__( 'Progress Items', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'progress_value' => 85,
						'label_text' => esc_html__( 'Web Design', 'omsar' ),
						'show_percentage' => 'yes',
					],
					[
						'progress_value' => 70,
						'label_text' => esc_html__( 'Development', 'omsar' ),
						'show_percentage' => 'yes',
					],
					[
						'progress_value' => 90,
						'label_text' => esc_html__( 'Marketing', 'omsar' ),
						'show_percentage' => 'yes',
					],
				],
				'title_field' => '{{{ label_text }}} ({{{ progress_value }}}%)',
			]
		);

		$this->end_controls_section();

		// Style Section - Progress Bar
		$this->start_controls_section(
			'style_progress_section',
			[
				'label' => esc_html__( 'Progress Bar', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__( 'Default Progress Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7c3aed',
				'description' => esc_html__( 'Default color for progress bars. Can be overridden per item in the repeater.', 'omsar' ),
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-bar-fill' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-progress-circular-fill' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e5e7eb',
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-bar-track' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-progress-circular-track' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bar_height',
			[
				'label' => esc_html__( 'Bar Height', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-bar-track' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bar_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'omsar' ),
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
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-bar-track' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-progress-bar-fill' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'circular_size',
			[
				'label' => esc_html__( 'Circle Size', 'omsar' ),
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
					'size' => 120,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-circular-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-progress-circular-svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'stroke_width',
			[
				'label' => esc_html__( 'Stroke Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Text
		$this->start_controls_section(
			'style_text_section',
			[
				'label' => esc_html__( 'Text', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1f2937',
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => esc_html__( 'Label Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-progress-label',
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
			'percentage_color',
			[
				'label' => esc_html__( 'Percentage Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1f2937',
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-percentage' => 'color: {{VALUE}};',
					'{{WRAPPER}} .omsar-progress-circular-percentage' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'percentage_typography',
				'label' => esc_html__( 'Percentage Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-progress-percentage, {{WRAPPER}} .omsar-progress-circular-percentage',
				'default' => [
					'font_weight' => '600',
					'font_size' => [
						'size' => '18',
						'unit' => 'px',
					],
				],
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
			'item_spacing',
			[
				'label' => esc_html__( 'Item Spacing', 'omsar' ),
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
					'{{WRAPPER}} .omsar-progress-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Label Spacing (Horizontal)', 'omsar' ),
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
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'circular_label_spacing',
			[
				'label' => esc_html__( 'Label Spacing (Circular)', 'omsar' ),
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-progress-circular-label' => 'margin-top: {{SIZE}}{{UNIT}};',
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
		$items = $settings['progress_items'];
		$global_unit = ! empty( $settings['global_unit'] ) ? esc_html( $settings['global_unit'] ) : '%';
		$stroke_width = ! empty( $settings['stroke_width']['size'] ) ? $settings['stroke_width']['size'] : 10;
		$circular_size = ! empty( $settings['circular_size']['size'] ) ? $settings['circular_size']['size'] : 120;
		$progress_type = ! empty( $settings['progress_type'] ) ? $settings['progress_type'] : 'horizontal';
		$circular_layout = ! empty( $settings['circular_layout'] ) ? $settings['circular_layout'] : 'vertical';

		if ( empty( $items ) ) {
			return;
		}

		$widget_id = 'omsar-progress-' . $this->get_id();
		$layout_class = 'omsar-circular-layout-' . $circular_layout;
		?>
		<div class="omsar-progress-bars-container <?php echo esc_attr( $layout_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>">
			<?php foreach ( $items as $index => $item ) :
				$progress_value = ! empty( $item['progress_value'] ) ? floatval( $item['progress_value'] ) : 0;
				$unit = ! empty( $item['unit'] ) ? esc_html( $item['unit'] ) : $global_unit;
				$label_text = ! empty( $item['label_text'] ) ? esc_html( $item['label_text'] ) : '';
				$show_percentage = ! empty( $item['show_percentage'] ) && $item['show_percentage'] === 'yes';
				$use_gradient = ! empty( $item['use_gradient'] ) && $item['use_gradient'] === 'yes';
				$gradient_from = ! empty( $item['gradient_color_from'] ) ? $item['gradient_color_from'] : '#7c3aed';
				$gradient_to = ! empty( $item['gradient_color_to'] ) ? $item['gradient_color_to'] : '#a855f7';
				$gradient_direction = ! empty( $item['gradient_direction'] ) ? $item['gradient_direction'] : 'to right';
				$progress_color = ! empty( $item['progress_color'] ) ? $item['progress_color'] : '#7c3aed';
				
				// Ensure progress value is between 0 and 100
				$progress_value = max( 0, min( 100, $progress_value ) );
				
				$item_id = $widget_id . '-item-' . $index;
				$gradient_id = 'gradient-' . $widget_id . '-' . $index;
				$item_type_class = 'omsar-progress-type-' . $progress_type;
				
				// Build gradient style
				$fill_style = '';
				if ( $use_gradient ) {
					$fill_style = 'background: linear-gradient(' . esc_attr( $gradient_direction ) . ', ' . esc_attr( $gradient_from ) . ', ' . esc_attr( $gradient_to ) . ');';
				} else {
					$fill_style = 'background-color: ' . esc_attr( $progress_color ) . ';';
				}
			?>
				<div class="omsar-progress-item <?php echo esc_attr( $item_type_class ); ?>">
					<?php if ( $progress_type === 'circular' ) : 
						// Calculate circular progress
						$radius = ( $circular_size - $stroke_width ) / 2;
						$circumference = 2 * M_PI * $radius;
						$offset = $circumference - ( $progress_value / 100 ) * $circumference;
						
						// Convert gradient direction to SVG coordinates
						$svg_gradient = '';
						if ( $use_gradient ) {
							$x1 = 0; $y1 = 0; $x2 = 1; $y2 = 0;
							switch ( $gradient_direction ) {
								case 'to bottom':
									$x1 = 0; $y1 = 0; $x2 = 0; $y2 = 1;
									break;
								case 'to left':
									$x1 = 1; $y1 = 0; $x2 = 0; $y2 = 0;
									break;
								case 'to top':
									$x1 = 0; $y1 = 1; $x2 = 0; $y2 = 0;
									break;
								case '45deg':
									$x1 = 0; $y1 = 1; $x2 = 1; $y2 = 0;
									break;
								case '135deg':
									$x1 = 0; $y1 = 0; $x2 = 1; $y2 = 1;
									break;
							}
							$svg_gradient = '<defs><linearGradient id="' . esc_attr( $gradient_id ) . '" x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '"><stop offset="0%" stop-color="' . esc_attr( $gradient_from ) . '" /><stop offset="100%" stop-color="' . esc_attr( $gradient_to ) . '" /></linearGradient></defs>';
							$stroke_color = 'url(#' . esc_attr( $gradient_id ) . ')';
						} else {
							$stroke_color = $progress_color;
						}
					?>
						<div class="omsar-progress-wrapper omsar-progress-circular-wrapper" id="<?php echo esc_attr( $item_id ); ?>">
							<div class="omsar-progress-circular-container">
								<svg class="omsar-progress-circular-svg" width="<?php echo esc_attr( $circular_size ); ?>" height="<?php echo esc_attr( $circular_size ); ?>" viewBox="0 0 <?php echo esc_attr( $circular_size ); ?> <?php echo esc_attr( $circular_size ); ?>">
									<?php echo $svg_gradient; ?>
									<circle
										class="omsar-progress-circular-track"
										cx="<?php echo esc_attr( $circular_size / 2 ); ?>"
										cy="<?php echo esc_attr( $circular_size / 2 ); ?>"
										r="<?php echo esc_attr( $radius ); ?>"
										fill="none"
										stroke-width="<?php echo esc_attr( $stroke_width ); ?>"
									/>
									<circle
										class="omsar-progress-circular-fill"
										cx="<?php echo esc_attr( $circular_size / 2 ); ?>"
										cy="<?php echo esc_attr( $circular_size / 2 ); ?>"
										r="<?php echo esc_attr( $radius ); ?>"
										fill="none"
										stroke-width="<?php echo esc_attr( $stroke_width ); ?>"
										stroke="<?php echo esc_attr( $stroke_color ); ?>"
										stroke-dasharray="<?php echo esc_attr( $circumference ); ?>"
										stroke-dashoffset="<?php echo esc_attr( $circumference ); ?>"
										stroke-linecap="round"
										transform="rotate(-90 <?php echo esc_attr( $circular_size / 2 ); ?> <?php echo esc_attr( $circular_size / 2 ); ?>)"
										style="stroke-dashoffset: <?php echo esc_attr( $offset ); ?>; transition: stroke-dashoffset 1s ease-in-out;"
									/>
								</svg>
								<?php if ( $show_percentage ) : ?>
									<div class="omsar-progress-circular-percentage">
										<?php echo esc_html( $progress_value ); ?><?php echo esc_html( $unit ); ?>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $label_text ) ) : ?>
								<div class="omsar-progress-circular-label"><?php echo esc_html( $label_text ); ?></div>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="omsar-progress-wrapper omsar-progress-horizontal-wrapper" id="<?php echo esc_attr( $item_id ); ?>">
							<?php if ( ! empty( $label_text ) || $show_percentage ) : ?>
								<div class="omsar-progress-header">
									<?php if ( ! empty( $label_text ) ) : ?>
										<span class="omsar-progress-label"><?php echo esc_html( $label_text ); ?></span>
									<?php endif; ?>
									<?php if ( $show_percentage ) : ?>
										<span class="omsar-progress-percentage"><?php echo esc_html( $progress_value ); ?><?php echo esc_html( $unit ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<div class="omsar-progress-bar-track">
								<div class="omsar-progress-bar-fill" style="width: <?php echo esc_attr( $progress_value ); ?>%; transition: width 1s ease-in-out; <?php echo $fill_style; ?>"></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<#
		var items = settings.progress_items || [];
		var globalUnit = settings.global_unit || '%';
		var strokeWidth = settings.stroke_width && settings.stroke_width.size ? settings.stroke_width.size : 10;
		var circularSize = settings.circular_size && settings.circular_size.size ? settings.circular_size.size : 120;
		var progressType = settings.progress_type || 'horizontal';
		var circularLayout = settings.circular_layout || 'vertical';
		var widgetId = 'omsar-progress-' + view.getIDInt();
		var layoutClass = 'omsar-circular-layout-' + circularLayout;
		#>
		<div class="omsar-progress-bars-container {{ layoutClass }}" id="{{ widgetId }}">
			<# _.each( items, function( item, index ) {
				var progressValue = item.progress_value || 0;
				var unit = item.unit || globalUnit;
				var labelText = item.label_text || '';
				var showPercentage = item.show_percentage === 'yes';
				var useGradient = item.use_gradient === 'yes';
				var gradientFrom = item.gradient_color_from || '#7c3aed';
				var gradientTo = item.gradient_color_to || '#a855f7';
				var gradientDirection = item.gradient_direction || 'to right';
				var progressColor = item.progress_color || '#7c3aed';
				
				// Ensure progress value is between 0 and 100
				progressValue = Math.max(0, Math.min(100, parseFloat(progressValue)));
				
				var itemId = widgetId + '-item-' + index;
				var gradientId = 'gradient-' + widgetId + '-' + index;
				var itemTypeClass = 'omsar-progress-type-' + progressType;
				
				// Build gradient style
				var fillStyle = '';
				if (useGradient) {
					fillStyle = 'background: linear-gradient(' + gradientDirection + ', ' + gradientFrom + ', ' + gradientTo + ');';
				} else {
					fillStyle = 'background-color: ' + progressColor + ';';
				}
			#>
				<div class="omsar-progress-item {{ itemTypeClass }}">
					<# if (progressType === 'circular') {
						var radius = (circularSize - strokeWidth) / 2;
						var circumference = 2 * Math.PI * radius;
						var offset = circumference - (progressValue / 100) * circumference;
						
						// Convert gradient direction to SVG coordinates
						var svgGradient = '';
						var strokeColor = progressColor;
						if (useGradient) {
							var x1 = 0, y1 = 0, x2 = 1, y2 = 0;
							switch (gradientDirection) {
								case 'to bottom':
									x1 = 0; y1 = 0; x2 = 0; y2 = 1;
									break;
								case 'to left':
									x1 = 1; y1 = 0; x2 = 0; y2 = 0;
									break;
								case 'to top':
									x1 = 0; y1 = 1; x2 = 0; y2 = 0;
									break;
								case '45deg':
									x1 = 0; y1 = 1; x2 = 1; y2 = 0;
									break;
								case '135deg':
									x1 = 0; y1 = 0; x2 = 1; y2 = 1;
									break;
							}
							svgGradient = '<defs><linearGradient id="' + gradientId + '" x1="' + x1 + '" y1="' + y1 + '" x2="' + x2 + '" y2="' + y2 + '"><stop offset="0%" stop-color="' + gradientFrom + '" /><stop offset="100%" stop-color="' + gradientTo + '" /></linearGradient></defs>';
							strokeColor = 'url(#' + gradientId + ')';
						}
					#>
						<div class="omsar-progress-wrapper omsar-progress-circular-wrapper" id="{{ itemId }}">
							<div class="omsar-progress-circular-container">
								<svg class="omsar-progress-circular-svg" width="{{ circularSize }}" height="{{ circularSize }}" viewBox="0 0 {{ circularSize }} {{ circularSize }}">
									{{{ svgGradient }}}
									<circle
										class="omsar-progress-circular-track"
										cx="{{ circularSize / 2 }}"
										cy="{{ circularSize / 2 }}"
										r="{{ radius }}"
										fill="none"
										stroke-width="{{ strokeWidth }}"
									/>
									<circle
										class="omsar-progress-circular-fill"
										cx="{{ circularSize / 2 }}"
										cy="{{ circularSize / 2 }}"
										r="{{ radius }}"
										fill="none"
										stroke-width="{{ strokeWidth }}"
										stroke="{{ strokeColor }}"
										stroke-dasharray="{{ circumference }}"
										stroke-dashoffset="{{ circumference }}"
										stroke-linecap="round"
										transform="rotate(-90 {{ circularSize / 2 }} {{ circularSize / 2 }})"
										style="stroke-dashoffset: {{ offset }};"
									/>
								</svg>
								<# if (showPercentage) { #>
									<div class="omsar-progress-circular-percentage">
										{{{ progressValue }}}{{{ unit }}}
									</div>
								<# } #>
							</div>
							<# if (labelText) { #>
								<div class="omsar-progress-circular-label">{{{ labelText }}}</div>
							<# } #>
						</div>
					<# } else { #>
						<div class="omsar-progress-wrapper omsar-progress-horizontal-wrapper" id="{{ itemId }}">
							<# if (labelText || showPercentage) { #>
								<div class="omsar-progress-header">
									<# if (labelText) { #>
										<span class="omsar-progress-label">{{{ labelText }}}</span>
									<# } #>
									<# if (showPercentage) { #>
										<span class="omsar-progress-percentage">{{{ progressValue }}}{{{ unit }}}</span>
									<# } #>
								</div>
							<# } #>
							<div class="omsar-progress-bar-track">
								<div class="omsar-progress-bar-fill" style="width: {{ progressValue }}%; {{ fillStyle }}"></div>
							</div>
						</div>
					<# } #>
				</div>
			<# }); #>
		</div>
		<?php
	}
}

