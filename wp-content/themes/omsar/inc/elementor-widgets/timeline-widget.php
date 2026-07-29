<?php
/**
 * Elementor Custom Timeline Widget Class
 * 
 * Displays a customizable timeline with configurable items, colors, sizes, and layouts
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

class OMSAR_Timeline_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'omsar_timeline';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Custom Timeline', 'omsar' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-time-line';
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
		return [ 'timeline', 'history', 'events', 'milestones', 'progress' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Timeline Items', 'omsar' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label' => esc_html__( 'Title', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Timeline Item', 'omsar' ),
				'placeholder' => esc_html__( 'Enter item title', 'omsar' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_date',
			[
				'label' => esc_html__( 'Date/Order', 'omsar' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'January 2024', 'omsar' ),
				'placeholder' => esc_html__( 'e.g., January 2024 or Q1 2024', 'omsar' ),
				'description' => esc_html__( 'Display date or order identifier for this item', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_description',
			[
				'label' => esc_html__( 'Description', 'omsar' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Timeline item description goes here.', 'omsar' ),
				'placeholder' => esc_html__( 'Enter item description', 'omsar' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_marker_filled',
			[
				'label' => esc_html__( 'Filled Marker', 'omsar' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'omsar' ),
				'label_off' => esc_html__( 'No', 'omsar' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Make this marker filled/solid instead of outlined', 'omsar' ),
			]
		);

		$this->add_control(
			'timeline_items',
			[
				'label' => esc_html__( 'Timeline Items', 'omsar' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'item_title' => esc_html__( 'Project Kickoff', 'omsar' ),
						'item_date' => esc_html__( 'January 2024', 'omsar' ),
						'item_description' => esc_html__( 'Initial planning and requirement gathering phase completed successfully.', 'omsar' ),
						'item_marker_filled' => 'no',
					],
					[
						'item_title' => esc_html__( 'Development Phase', 'omsar' ),
						'item_date' => esc_html__( 'March 2024', 'omsar' ),
						'item_description' => esc_html__( 'Core features implemented and tested across multiple platforms.', 'omsar' ),
						'item_marker_filled' => 'no',
					],
					[
						'item_title' => esc_html__( 'Beta Launch', 'omsar' ),
						'item_date' => esc_html__( 'June 2024', 'omsar' ),
						'item_description' => esc_html__( 'Limited release to selected users for feedback and improvements.', 'omsar' ),
						'item_marker_filled' => 'yes',
					],
					[
						'item_title' => esc_html__( 'Public Release', 'omsar' ),
						'item_date' => esc_html__( 'Q4 2024', 'omsar' ),
						'item_description' => esc_html__( 'Full public launch with all features and enhancements.', 'omsar' ),
						'item_marker_filled' => 'no',
					],
				],
				'title_field' => '{{{ item_title }}}',
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
			'timeline_layout',
			[
				'label' => esc_html__( 'Timeline Layout', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'alternating' => esc_html__( 'Alternating (Left/Right)', 'omsar' ),
					'alternating_right_left' => esc_html__( 'Alternating (Right/Left)', 'omsar' ),
					'same_side' => esc_html__( 'All Items Same Side', 'omsar' ),
				],
				'default' => 'alternating',
				'description' => esc_html__( 'Choose how timeline items are positioned', 'omsar' ),
			]
		);

		$this->add_control(
			'same_side_alignment',
			[
				'label' => esc_html__( 'Items Alignment', 'omsar' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => esc_html__( 'Left', 'omsar' ),
					'right' => esc_html__( 'Right', 'omsar' ),
				],
				'default' => 'right',
				'condition' => [
					'timeline_layout' => 'same_side',
				],
				'description' => esc_html__( 'Which side to align all items when using same-side layout', 'omsar' ),
			]
		);

		$this->end_controls_section();

		// Style Section - Timeline Line
		$this->start_controls_section(
			'style_line_section',
			[
				'label' => esc_html__( 'Timeline Line', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'line_color',
			[
				'label' => esc_html__( 'Line Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#d1d5db',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_width',
			[
				'label' => esc_html__( 'Line Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-line' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Markers
		$this->start_controls_section(
			'style_markers_section',
			[
				'label' => esc_html__( 'Markers', 'omsar' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'marker_size',
			[
				'label' => esc_html__( 'Marker Size', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 40,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-marker' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'marker_border_width',
			[
				'label' => esc_html__( 'Marker Border Width', 'omsar' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 5,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-marker:not(.filled)' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label' => esc_html__( 'Marker Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#60a5fa',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-marker' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .omsar-timeline-marker.filled' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'marker_background',
			[
				'label' => esc_html__( 'Marker Background', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-marker:not(.filled)' => 'background-color: {{VALUE}};',
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
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1f2937',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Title Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-timeline-item-title',
				'default' => [
					'font_weight' => '600',
					'font_size' => [
						'size' => '18',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => esc_html__( 'Date Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#60a5fa',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-item-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => esc_html__( 'Date Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-timeline-item-date',
				'default' => [
					'font_size' => [
						'size' => '14',
						'unit' => 'px',
					],
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Description Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6b7280',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-item-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => esc_html__( 'Description Typography', 'omsar' ),
				'selector' => '{{WRAPPER}} .omsar-timeline-item-description',
				'default' => [
					'font_size' => [
						'size' => '14',
						'unit' => 'px',
					],
					'line_height' => [
						'size' => '1.6',
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
						'min' => 20,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_spacing',
			[
				'label' => esc_html__( 'Content Spacing', 'omsar' ),
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
					'{{WRAPPER}} .omsar-timeline-item-date' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .omsar-timeline-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'omsar' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wrapper_padding',
			[
				'label' => esc_html__( 'Wrapper Padding', 'omsar' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .omsar-timeline-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$items = $settings['timeline_items'];
		$layout = $settings['timeline_layout'];
		$same_side_alignment = ! empty( $settings['same_side_alignment'] ) ? $settings['same_side_alignment'] : 'right';

		// Detect Arabic language and force right alignment for same_side layout
		$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : ( is_rtl() ? 'ar' : 'en' );
		$is_arabic = ( $current_lang === 'ar' || is_rtl() );
		
		if ( $layout === 'same_side' && $is_arabic ) {
			$same_side_alignment = 'right';
		}

		if ( empty( $items ) ) {
			return;
		}

		$widget_id = 'omsar-timeline-' . $this->get_id();
		$layout_class = 'omsar-timeline-' . $layout;
		if ( $layout === 'same_side' ) {
			$layout_class .= ' omsar-timeline-' . $same_side_alignment;
		}
		if ( $is_arabic ) {
			$layout_class .= ' omsar-timeline-rtl';
		}
		?>
		<div class="omsar-timeline-wrapper <?php echo esc_attr( $layout_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>">
			<div class="omsar-timeline-container">
				<div class="omsar-timeline-line"></div>
				<div class="omsar-timeline-items">
					<?php foreach ( $items as $index => $item ) : 
						$item_title = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
						$item_date = ! empty( $item['item_date'] ) ? $item['item_date'] : '';
						$item_description = ! empty( $item['item_description'] ) ? $item['item_description'] : '';
						$is_filled = ! empty( $item['item_marker_filled'] ) && $item['item_marker_filled'] === 'yes';
						$marker_class = $is_filled ? 'filled' : '';
						$is_last = ( $index === count( $items ) - 1 );
					?>
						<div class="omsar-timeline-item <?php echo $is_last ? 'omsar-timeline-item-last' : ''; ?>">
							<div class="omsar-timeline-marker <?php echo esc_attr( $marker_class ); ?>"></div>
							<div class="omsar-timeline-item-content">
								<?php if ( ! empty( $item_title ) ) : ?>
									<div class="omsar-timeline-item-title"><?php echo esc_html( $item_title ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $item_date ) ) : ?>
									<div class="omsar-timeline-item-date"><?php echo esc_html( $item_date ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $item_description ) ) : ?>
									<div class="omsar-timeline-item-description"><?php echo wp_kses_post( $item_description ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<script>
		(function() {
			var timelineWrapper = document.getElementById('<?php echo esc_js( $widget_id ); ?>');
			if (timelineWrapper) {
				function updateTimelineLine() {
					var lastItem = timelineWrapper.querySelector('.omsar-timeline-item-last');
					var line = timelineWrapper.querySelector('.omsar-timeline-line');
					if (lastItem && line) {
						var lastMarker = lastItem.querySelector('.omsar-timeline-marker');
						if (lastMarker) {
							var container = timelineWrapper.querySelector('.omsar-timeline-container');
							var markerRect = lastMarker.getBoundingClientRect();
							var containerRect = container.getBoundingClientRect();
							var lineRect = line.getBoundingClientRect();
							var lineTop = lineRect.top - containerRect.top;
							var markerTop = markerRect.top - containerRect.top;
							var markerCenter = markerTop + (markerRect.height / 2);
							var lineHeight = markerCenter - lineTop;
							if (lineHeight > 0) {
								line.style.height = lineHeight + 'px';
							}
						}
					}
				}
				
				// Update on load
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', updateTimelineLine);
				} else {
					updateTimelineLine();
				}
				
				// Update on resize
				var resizeTimer;
				window.addEventListener('resize', function() {
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(updateTimelineLine, 100);
				});
				
				// Update after delays to ensure content is loaded
				setTimeout(updateTimelineLine, 100);
				setTimeout(updateTimelineLine, 500);
			}
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
		var items = settings.timeline_items || [];
		var layout = settings.timeline_layout || 'alternating';
		var sameSideAlignment = settings.same_side_alignment || 'right';
		
		// Note: In editor, we can't detect language, so RTL class won't be added
		// This is fine as the frontend render() method will handle it correctly
		var widgetId = 'omsar-timeline-' + view.getIDInt();
		var layoutClass = 'omsar-timeline-' + layout;
		if (layout === 'same_side') {
			layoutClass += ' omsar-timeline-' + sameSideAlignment;
		}
		#>
		<div class="omsar-timeline-wrapper {{ layoutClass }}" id="{{ widgetId }}">
			<div class="omsar-timeline-container">
				<div class="omsar-timeline-line"></div>
				<div class="omsar-timeline-items">
					<# _.each( items, function( item, index ) {
						var itemTitle = item.item_title || '';
						var itemDate = item.item_date || '';
						var itemDescription = item.item_description || '';
						var isFilled = item.item_marker_filled === 'yes';
						var markerClass = isFilled ? 'filled' : '';
						var isLast = (index === items.length - 1);
					#>
						<div class="omsar-timeline-item <# if (isLast) { #>omsar-timeline-item-last<# } #>">
							<div class="omsar-timeline-marker {{ markerClass }}"></div>
							<div class="omsar-timeline-item-content">
								<# if (itemTitle) { #>
									<div class="omsar-timeline-item-title">{{{ itemTitle }}}</div>
								<# } #>
								<# if (itemDate) { #>
									<div class="omsar-timeline-item-date">{{{ itemDate }}}</div>
								<# } #>
								<# if (itemDescription) { #>
									<div class="omsar-timeline-item-description">{{{ itemDescription }}}</div>
								<# } #>
							</div>
						</div>
					<# }); #>
				</div>
			</div>
		</div>
		<script>
		(function() {
			var timelineWrapper = document.getElementById('{{ widgetId }}');
			if (timelineWrapper) {
				function updateTimelineLine() {
					var lastItem = timelineWrapper.querySelector('.omsar-timeline-item-last');
					var line = timelineWrapper.querySelector('.omsar-timeline-line');
					if (lastItem && line) {
						var lastMarker = lastItem.querySelector('.omsar-timeline-marker');
						if (lastMarker) {
							var container = timelineWrapper.querySelector('.omsar-timeline-container');
							var markerRect = lastMarker.getBoundingClientRect();
							var containerRect = container.getBoundingClientRect();
							var lineRect = line.getBoundingClientRect();
							var lineTop = lineRect.top - containerRect.top;
							var markerTop = markerRect.top - containerRect.top;
							var markerCenter = markerTop + (markerRect.height / 2);
							var lineHeight = markerCenter - lineTop;
							if (lineHeight > 0) {
								line.style.height = lineHeight + 'px';
							}
						}
					}
				}
				
				// Update on load
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', updateTimelineLine);
				} else {
					updateTimelineLine();
				}
				
				// Update on resize
				var resizeTimer;
				window.addEventListener('resize', function() {
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(updateTimelineLine, 100);
				});
				
				// Update after delays to ensure content is loaded
				setTimeout(updateTimelineLine, 100);
				setTimeout(updateTimelineLine, 500);
			}
		})();
		</script>
		<?php
	}
}

