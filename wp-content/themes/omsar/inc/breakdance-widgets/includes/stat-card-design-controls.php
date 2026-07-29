<?php
/**
 * Breakdance design controls for Statistics Card element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return array<string, mixed>
 */
function omsar_bd_stat_card_default_design_properties() {
	return array(
		'number' => array(
			'color'         => '#000000',
			'margin_bottom' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
		),
		'title'  => array(
			'color' => '#000000',
		),
		'card'   => array(
			'border_radius' => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
			'min_height'    => array( 'number' => 200, 'unit' => 'px', 'style' => '200px' ),
			'border_style'  => 'none',
			'border_color'  => 'rgba(0, 0, 0, 0.05)',
		),
		'icon'   => array(
			'opacity' => 0.15,
			'size'    => array( 'number' => 150, 'unit' => 'px', 'style' => '150px' ),
		),
		'hover'  => array(
			'enable_hover_effect' => true,
			'overlay_opacity'     => 0.77,
			'hover_scale'         => 1.02,
			'gradient_from'       => '#E0F2FF',
			'gradient_to'         => '#6EA8FE',
			'hover_icon_opacity'  => 0.8,
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_stat_card_design_controls() {
	$border_condition = array(
		'condition' => array(
			'path'    => 'design.card.border_style',
			'operand' => 'not equals',
			'value'   => 'none',
		),
	);

	$hover_condition = array(
		'condition' => array(
			'path'    => 'design.hover.enable_hover_effect',
			'operand' => 'equals',
			'value'   => true,
		),
	);

	$border_style_items = array(
		array( 'text' => 'None', 'value' => 'none' ),
		array( 'text' => 'Solid', 'value' => 'solid' ),
		array( 'text' => 'Dashed', 'value' => 'dashed' ),
		array( 'text' => 'Dotted', 'value' => 'dotted' ),
	);

	return array(
		omsar_bd_design_section(
			'number',
			'Number Style',
			array(
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'margin_bottom',
					'Margin Bottom',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title Style',
			array(
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'card',
			'Card Style',
			array(
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
					)
				),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'min_height',
					'Min Height',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 100, 'max' => 500, 'step' => 5 ),
					)
				),
				getPresetSection(
					'EssentialElements\\borders',
					'Box Shadow',
					'borders',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'border_style',
					'Border',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $border_style_items,
					)
				),
				getPresetSection(
					'EssentialElements\\borders',
					'Border Width',
					'border_width',
					array( 'type' => 'popout' ) + $border_condition
				),
				omsar_bd_design_control(
					'border_color',
					'Border Color',
					array( 'type' => 'color', 'layout' => 'inline' ) + $border_condition
				),
			)
		),
		omsar_bd_design_section(
			'icon',
			'Icon',
			array(
				omsar_bd_design_control(
					'opacity',
					'Icon Opacity',
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array( 'min' => 0, 'max' => 1, 'step' => 0.1 ),
					)
				),
				omsar_bd_design_control(
					'size',
					'Icon Size',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 50, 'max' => 300, 'step' => 10 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'hover',
			'Hover Effect',
			array(
				omsar_bd_design_control(
					'enable_hover_effect',
					'Enable Hover Effect',
					array(
						'type'   => 'toggle',
						'layout' => 'inline',
					)
				),
				omsar_bd_design_control(
					'overlay_opacity',
					'Overlay Opacity',
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array( 'min' => 0, 'max' => 1, 'step' => 0.1 ),
					) + $hover_condition
				),
				omsar_bd_design_control(
					'hover_scale',
					'Hover Scale',
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array( 'min' => 1, 'max' => 1.2, 'step' => 0.01 ),
					) + $hover_condition
				),
				omsar_bd_design_control(
					'gradient_from',
					'Gradient Color From',
					array( 'type' => 'color', 'layout' => 'inline' ) + $hover_condition
				),
				omsar_bd_design_control(
					'gradient_to',
					'Gradient Color To',
					array( 'type' => 'color', 'layout' => 'inline' ) + $hover_condition
				),
				getPresetSection(
					'EssentialElements\\borders',
					'Hover Box Shadow',
					'hover_shadow',
					array( 'type' => 'popout' ) + $hover_condition
				),
				omsar_bd_design_control(
					'hover_icon_opacity',
					'Icon Opacity on Hover',
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array( 'min' => 0, 'max' => 1, 'step' => 0.1 ),
					) + $hover_condition
				),
			)
		),
	);
}
