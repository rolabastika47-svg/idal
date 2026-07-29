<?php
/**
 * Breakdance design controls for Bar Chart element.
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
function omsar_bd_bar_chart_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'right' ),
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
			'legend',
			'Legend Style',
			array(
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			),
			array(
				'condition' => array(
					'path'    => 'content.legend.show_legend',
					'operand' => 'equals',
					'value'   => true,
				),
			)
		),
		omsar_bd_design_section(
			'container',
			'Chart Container',
			array(
				omsar_bd_design_control(
					'chart_height',
					'Chart Height',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array(
							'min'  => 200,
							'max'  => 800,
							'step' => 10,
						),
					)
				),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\borders',
					'Border',
					'borders',
					array( 'type' => 'popout' )
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
					'chart_alignment',
					'Chart Alignment',
					array(
						'type'   => 'button_bar',
						'layout' => 'inline',
						'items'  => $align_items,
					)
				),
				omsar_bd_design_control(
					'chart_width',
					'Chart Width',
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
