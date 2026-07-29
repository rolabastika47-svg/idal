<?php
/**
 * Breakdance design controls for Line Chart element.
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
function omsar_bd_line_chart_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'right' ),
	);

	$points_condition = array(
		'condition' => array(
			'path'    => 'design.line.show_points',
			'operand' => 'equals',
			'value'   => true,
		),
	);

	$fill_condition = array(
		'condition' => array(
			'path'    => 'design.line.fill_area',
			'operand' => 'equals',
			'value'   => true,
		),
	);

	$grid_condition = array(
		'condition' => array(
			'path'    => 'design.grid.show_grid',
			'operand' => 'equals',
			'value'   => true,
		),
	);

	return array(
		omsar_bd_design_section(
			'line',
			'Line Style',
			array(
				omsar_bd_design_control( 'line_color', 'Line Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'line_width',
					'Line Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 1, 'max' => 10, 'step' => 1 ),
					)
				),
				omsar_bd_design_control( 'show_points', 'Show Data Points', array( 'type' => 'toggle', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'point_radius',
					'Point Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 1, 'max' => 10, 'step' => 1 ),
					) + $points_condition
				),
				omsar_bd_design_control( 'point_color', 'Point Color', array( 'type' => 'color', 'layout' => 'inline' ) + $points_condition ),
				omsar_bd_design_control( 'fill_area', 'Fill Area Under Line', array( 'type' => 'toggle', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'fill_color', 'Fill Color', array( 'type' => 'color', 'layout' => 'inline' ) + $fill_condition ),
			)
		),
		omsar_bd_design_section(
			'grid',
			'Grid Style',
			array(
				omsar_bd_design_control( 'show_grid', 'Show Grid Lines', array( 'type' => 'toggle', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'grid_color', 'Grid Color', array( 'type' => 'color', 'layout' => 'inline' ) + $grid_condition ),
				omsar_bd_design_control(
					'grid_line_width',
					'Grid Line Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
					) + $grid_condition
				),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'alignment',
					'Alignment',
					array( 'type' => 'button_bar', 'layout' => 'inline', 'items' => $align_items )
				),
				getPresetSection( 'EssentialElements\\spacing_margin_all', 'Margin Bottom', 'margin', array( 'type' => 'popout' ) ),
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
						'rangeOptions' => array( 'min' => 200, 'max' => 800, 'step' => 10 ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\borders', 'Border', 'borders', array( 'type' => 'popout' ) ),
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
			)
		),
		omsar_bd_design_section(
			'advanced',
			'Advanced',
			array(
				omsar_bd_design_control(
					'chart_width',
					'Container Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
						'rangeOptions' => array( 'min' => 10, 'max' => 100, 'step' => 1 ),
					)
				),
				getPresetSection( 'EssentialElements\\spacing_margin_all', 'Margin', 'margin', array( 'type' => 'popout' ) ),
			)
		),
	);
}
