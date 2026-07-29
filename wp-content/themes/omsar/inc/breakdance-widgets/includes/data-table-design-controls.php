<?php
/**
 * Breakdance design controls for Data Table element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return array<int, mixed>
 */
function omsar_bd_data_table_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'right' ),
	);

	$border_style_items = array(
		array( 'text' => 'None', 'value' => 'none' ),
		array( 'text' => 'Solid', 'value' => 'solid' ),
		array( 'text' => 'Dashed', 'value' => 'dashed' ),
		array( 'text' => 'Dotted', 'value' => 'dotted' ),
		array( 'text' => 'Double', 'value' => 'double' ),
		array( 'text' => 'Groove', 'value' => 'groove' ),
		array( 'text' => 'Ridge', 'value' => 'ridge' ),
		array( 'text' => 'Inset', 'value' => 'inset' ),
		array( 'text' => 'Outset', 'value' => 'outset' ),
	);

	$border_condition = array(
		'condition' => array(
			'path'    => 'design.container.border_style',
			'operand' => 'not equals',
			'value'   => 'none',
		),
	);

	return array(
		omsar_bd_design_section(
			'title',
			'Title Style',
			array(
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'alignment',
					'Alignment',
					array(
						'type'   => 'button_bar',
						'layout' => 'inline',
						'items'  => $align_items,
					)
				),
				getPresetSection(
					'EssentialElements\\spacing_margin_all',
					'Margin Bottom',
					'margin',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'header',
			'Table Header',
			array(
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'body',
			'Table Body',
			array(
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Cell Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'row_border_color', 'Row Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'row_border_width',
					'Row Border Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array(
							'min'  => 0,
							'max'  => 5,
							'step' => 0.1,
						),
					)
				),
			)
		),
		omsar_bd_design_section(
			'container',
			'Table Container',
			array(
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'border_style',
					'Border Style',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $border_style_items,
					)
				),
				omsar_bd_design_control(
					'border_color',
					'Border Color',
					array(
						'type'   => 'color',
						'layout' => 'inline',
					) + $border_condition
				),
				getPresetSection(
					'EssentialElements\\borders',
					'Border Width',
					'borders',
					array( 'type' => 'popout' ) + $border_condition
				),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					)
				),
			)
		),
		omsar_bd_design_section(
			'advanced',
			'Advanced',
			array(
				omsar_bd_design_control(
					'table_width',
					'Table Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
						'rangeOptions' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 1,
						),
					)
				),
				getPresetSection(
					'EssentialElements\\spacing_margin_all',
					'Margin',
					'margin',
					array( 'type' => 'popout' )
				),
			)
		),
	);
}
