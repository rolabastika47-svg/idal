<?php
/**
 * Breakdance design controls for Recruitments element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @param string               $slug
 * @param string               $label
 * @param array<int, mixed>    $children
 * @param array<string, mixed> $options
 * @return array<string, mixed>
 */
function omsar_bd_design_section( $slug, $label, $children, $options = array() ) {
	$options['type'] = 'section';
	return c( $slug, $label, $children, $options, false, false, array() );
}

/**
 * @param string               $slug
 * @param string               $label
 * @param array<string, mixed> $options
 * @param array<int, mixed>    $children
 * @return array<string, mixed>
 */
function omsar_bd_design_control( $slug, $label, $options, $children = array() ) {
	return c( $slug, $label, $children, $options, false, false, array() );
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_recruitments_design_controls() {
	$image_condition = array(
		'condition' => array(
			'path'    => 'design.card.card_background_type',
			'operand' => 'equals',
			'value'   => 'image',
		),
	);

	$bg_position_items = array(
		array( 'text' => 'Center Center', 'value' => 'center center' ),
		array( 'text' => 'Center Top', 'value' => 'center top' ),
		array( 'text' => 'Center Bottom', 'value' => 'center bottom' ),
		array( 'text' => 'Left Center', 'value' => 'left center' ),
		array( 'text' => 'Right Center', 'value' => 'right center' ),
	);

	return array(
		omsar_bd_design_section(
			'card',
			'Card Style',
			array(
				omsar_bd_design_control(
					'card_background_type',
					'Background Type',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Color', 'value' => 'color' ),
							array( 'text' => 'Image', 'value' => 'image' ),
						),
					)
				),
				omsar_bd_design_control(
					'card_background_color',
					'Card Background Color',
					array(
						'type'      => 'color',
						'layout'    => 'inline',
						'condition' => array(
							'path'    => 'design.card.card_background_type',
							'operand' => 'equals',
							'value'   => 'color',
						),
					)
				),
				omsar_bd_design_control(
					'card_background_image',
					'Card Background Image',
					array(
						'type'   => 'wpmedia',
						'layout' => 'vertical',
					) + $image_condition
				),
				omsar_bd_design_control(
					'card_background_image_size',
					'Background Size',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Cover', 'value' => 'cover' ),
							array( 'text' => 'Contain', 'value' => 'contain' ),
							array( 'text' => 'Auto', 'value' => 'auto' ),
						),
					) + $image_condition
				),
				omsar_bd_design_control(
					'card_background_image_position',
					'Background Position',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $bg_position_items,
					) + $image_condition
				),
				omsar_bd_design_control(
					'card_background_image_repeat',
					'Background Repeat',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'No Repeat', 'value' => 'no-repeat' ),
							array( 'text' => 'Repeat', 'value' => 'repeat' ),
						),
					) + $image_condition
				),
				omsar_bd_design_control(
					'card_background_overlay',
					'Background Overlay',
					array(
						'type'   => 'toggle',
						'layout' => 'inline',
					) + $image_condition
				),
				omsar_bd_design_control(
					'card_background_overlay_color',
					'Overlay Color',
					array(
						'type'      => 'color',
						'layout'    => 'inline',
						'condition' => array(
							'path'    => 'design.card.card_background_overlay',
							'operand' => 'is set',
							'value'   => '',
						),
					)
				),
				omsar_bd_design_control(
					'card_gradient_color_from',
					'Gradient Color From',
					array( 'type' => 'color', 'layout' => 'inline' )
				),
				omsar_bd_design_control(
					'card_gradient_color_to',
					'Gradient Color To',
					array( 'type' => 'color', 'layout' => 'inline' )
				),
				omsar_bd_design_control(
					'card_gradient_angle',
					'Gradient Angle',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'deg' ) ),
					)
				),
				omsar_bd_design_control(
					'card_gradient_overlay_opacity',
					'Gradient Overlay Opacity',
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array( 'min' => 0, 'max' => 1, 'step' => 0.1 ),
					)
				),
				omsar_bd_design_control(
					'card_overlay_border_radius',
					'Overlay Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control(
					'card_border_radius',
					'Card Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Card Padding', 'padding', array( 'type' => 'popout' ) ),
				getPresetSection( 'EssentialElements\\borders', 'Card Border', 'borders', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'grid_gap',
					'Grid Gap',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'filters',
			'Filter Tabs',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_width',
					'Border Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Tab Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'gap',
					'Gap Between Tabs',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'active_text_color', 'Active Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'active_background_color', 'Active Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'active_border_color', 'Active Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title',
			array(
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'details',
			'Details',
			array(
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'icon_color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'badges',
			'Status Badges',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control( 'open_text_color', 'Open Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'open_border_color', 'Open Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'open_background_color', 'Open Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'closed_text_color', 'Closed Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'closed_border_color', 'Closed Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'closed_background_color', 'Closed Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'upcoming_text_color', 'Upcoming Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'upcoming_border_color', 'Upcoming Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'upcoming_background_color', 'Upcoming Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'apply_button',
			'Apply Button',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_width',
					'Border Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'load_more',
			'Load More Button',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_width',
					'Border Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'wrapper_alignment',
					'Alignment',
					array(
						'type'   => 'button_bar',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Left', 'value' => 'left' ),
							array( 'text' => 'Center', 'value' => 'center' ),
							array( 'text' => 'Right', 'value' => 'right' ),
						),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_margin_y', 'Wrapper Margin', 'wrapper_margin', array( 'type' => 'popout' ) ),
			)
		),
	);
}

/**
 * Default design properties aligned with Elementor Recruitments widget.
 *
 * @return array<string, mixed>
 */
function omsar_bd_recruitments_default_design_properties() {
	return array(
		'card'          => array(
			'card_background_type'            => 'image',
			'card_background_color'           => 'transparent',
			'card_background_overlay'         => false,
			'card_background_overlay_color'   => 'rgba(0, 0, 0, 0.3)',
			'card_background_image_size'      => 'cover',
			'card_background_image_position'  => 'center center',
			'card_background_image_repeat'    => 'no-repeat',
			'card_gradient_color_from'      => 'rgba(173, 216, 230, 0.25)',
			'card_gradient_color_to'        => 'rgba(255, 255, 255, 0.95)',
			'card_gradient_angle'           => array( 'number' => 135, 'unit' => 'deg' ),
			'card_gradient_overlay_opacity'   => array( 'number' => 0.8 ),
			'card_overlay_border_radius'      => array( 'number' => 12, 'unit' => 'px' ),
			'card_border_radius'              => array( 'number' => 12, 'unit' => 'px' ),
			'grid_gap'                        => array( 'number' => 24, 'unit' => 'px' ),
		),
		'filters'       => array(
			'text_color'              => '#192D50',
			'background_color'        => 'transparent',
			'border_width'            => array( 'number' => 2, 'unit' => 'px' ),
			'border_color'            => '#192D50',
			'border_radius'           => array( 'number' => 24, 'unit' => 'px' ),
			'gap'                     => array( 'number' => 12, 'unit' => 'px' ),
			'hover_text_color'        => '#ffffff',
			'hover_background_color'  => '#7db3e8',
			'active_text_color'       => '#ffffff',
			'active_background_color' => '#192D50',
			'active_border_color'     => '#192D50',
		),
		'title'         => array(
			'color' => '#192D50',
		),
		'details'       => array(
			'text_color' => '#7db3e8',
			'icon_color' => '#010101',
		),
		'badges'        => array(
			'border_radius'           => array( 'number' => 30, 'unit' => 'px' ),
			'open_text_color'         => '#059669',
			'open_border_color'       => '#059669',
			'open_background_color'   => 'transparent',
			'closed_text_color'       => '#dc3545',
			'closed_border_color'     => '#dc3545',
			'closed_background_color' => 'transparent',
			'upcoming_text_color'     => '#d97706',
			'upcoming_border_color'   => '#d97706',
			'upcoming_background_color' => 'transparent',
		),
		'apply_button'  => array(
			'background_color' => '#ffffff',
			'text_color'       => '#000000',
			'border_color'     => 'transparent',
			'border_width'     => array( 'number' => 0, 'unit' => 'px' ),
			'border_radius'    => array( 'number' => 24, 'unit' => 'px' ),
		),
		'load_more'     => array(
			'background_color'       => 'transparent',
			'text_color'             => '#7db3e8',
			'border_color'           => '#7db3e8',
			'border_width'           => array( 'number' => 2, 'unit' => 'px' ),
			'border_radius'          => array( 'number' => 24, 'unit' => 'px' ),
			'hover_background_color' => '#7db3e8',
			'hover_text_color'       => '#ffffff',
			'hover_border_color'     => '#7db3e8',
			'wrapper_alignment'      => 'center',
		),
	);
}
