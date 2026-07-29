<?php
/**
 * Breakdance design controls for Animated Stats Counter element.
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
function omsar_bd_animated_stats_counter_default_design_properties() {
	return array(
		'icon'     => array(
			'size'              => array( 'number' => 60, 'unit' => 'px', 'style' => '60px' ),
			'background_type'   => 'gradient',
			'background_color'  => '#7c3aed',
			'gradient_from'     => '#7c3aed',
			'gradient_to'       => '#6366f1',
			'color'             => '#4c1d95',
			'border_radius'     => array( 'number' => 50, 'unit' => '%', 'style' => '50%' ),
			'spacing'           => array( 'number' => 20, 'unit' => 'px', 'style' => '20px' ),
		),
		'number'   => array(
			'color'   => '#1f2937',
			'spacing' => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
		),
		'label'    => array(
			'color' => '#6b7280',
		),
		'card'     => array(
			'background_color' => '#ffffff',
			'border_radius'    => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
		),
		'spacing'  => array(
			'items_gap'      => array( 'number' => 30, 'unit' => 'px', 'style' => '30px' ),
			'columns'        => 'auto',
			'card_max_width' => array( 'number' => 240, 'unit' => 'px', 'style' => '240px' ),
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_animated_stats_counter_design_controls() {
	$color_bg_condition = array(
		'condition' => array(
			'path'    => 'design.icon.background_type',
			'operand' => 'equals',
			'value'   => 'color',
		),
	);

	$gradient_bg_condition = array(
		'condition' => array(
			'path'    => 'design.icon.background_type',
			'operand' => 'equals',
			'value'   => 'gradient',
		),
	);

	$column_items = array(
		array( 'text' => 'Auto', 'value' => 'auto' ),
		array( 'text' => '1', 'value' => '1' ),
		array( 'text' => '2', 'value' => '2' ),
		array( 'text' => '3', 'value' => '3' ),
		array( 'text' => '4', 'value' => '4' ),
	);

	return array(
		omsar_bd_design_section(
			'icon',
			'Icon',
			array(
				omsar_bd_design_control(
					'size',
					'Icon Size',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 20, 'max' => 150, 'step' => 5 ),
					)
				),
				omsar_bd_design_control(
					'background_type',
					'Icon Background Type',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Color', 'value' => 'color' ),
							array( 'text' => 'Gradient', 'value' => 'gradient' ),
							array( 'text' => 'None', 'value' => 'none' ),
						),
					)
				),
				omsar_bd_design_control(
					'background_color',
					'Background Color',
					array( 'type' => 'color', 'layout' => 'inline' ) + $color_bg_condition
				),
				omsar_bd_design_control(
					'gradient_from',
					'Gradient Color From',
					array( 'type' => 'color', 'layout' => 'inline' ) + $gradient_bg_condition
				),
				omsar_bd_design_control(
					'gradient_to',
					'Gradient Color To',
					array( 'type' => 'color', 'layout' => 'inline' ) + $gradient_bg_condition
				),
				omsar_bd_design_control( 'color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					)
				),
				omsar_bd_design_control(
					'spacing',
					'Icon Spacing',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'number',
			'Number',
			array(
				omsar_bd_design_control( 'color', 'Number Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'spacing',
					'Number Spacing',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 50, 'step' => 2 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'label',
			'Label',
			array(
				omsar_bd_design_control( 'color', 'Label Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
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
			'Card',
			array(
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
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
				getPresetSection(
					'EssentialElements\\borders',
					'Box Shadow',
					'borders',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'spacing',
			'Spacing',
			array(
				omsar_bd_design_control(
					'items_gap',
					'Items Gap',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
					)
				),
				omsar_bd_design_control(
					'columns',
					'Columns',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $column_items,
					)
				),
				omsar_bd_design_control(
					'card_max_width',
					'Card Max Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
						'rangeOptions' => array( 'min' => 200, 'max' => 600, 'step' => 10 ),
					)
				),
			)
		),
	);
}
