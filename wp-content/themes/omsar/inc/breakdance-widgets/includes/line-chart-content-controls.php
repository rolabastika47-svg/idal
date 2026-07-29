<?php
/**
 * Breakdance content controls for Line Chart element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\c;

/**
 * @return array<string, mixed>
 */
function omsar_bd_line_chart_default_content_properties() {
	$defaults = omsar_bd_line_chart_default_settings();

	return array(
		'chart'  => array(
			'chart_title'      => $defaults['chart_title'],
			'show_export_icon' => false,
			'show_zoom_icon'   => false,
			'y_axis_label'     => $defaults['y_axis_label'],
			'reverse_chart'    => false,
			'line_label'       => $defaults['line_label'],
		),
		'y_axis' => array(
			'y_axis_values' => $defaults['y_axis_values'],
		),
		'data'   => array(
			'chart_data' => $defaults['chart_data'],
		),
		'legend' => array(
			'show_legend'      => true,
			'legend_position'  => $defaults['legend_position'],
			'legend_alignment' => $defaults['legend_alignment'],
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_line_chart_content_controls() {
	$legend_position_items = array(
		array( 'text' => 'Top', 'value' => 'top' ),
		array( 'text' => 'Bottom', 'value' => 'bottom' ),
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Right', 'value' => 'right' ),
	);

	$legend_alignment_items = array(
		array( 'text' => 'Start', 'value' => 'start' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'End', 'value' => 'end' ),
	);

	return array(
		c(
			'chart',
			'Chart Content',
			array(
				c( 'chart_title', 'Chart Title', array(), array( 'type' => 'text', 'layout' => 'vertical' ), false, false, array() ),
				c( 'show_export_icon', 'Show Export Image Icon', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'show_zoom_icon', 'Show Zoom/Expand Icon', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'y_axis_label', 'Y-Axis Label', array(), array( 'type' => 'text', 'layout' => 'vertical' ), false, false, array() ),
				c( 'reverse_chart', 'Reverse Chart Display', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'line_label', 'Line Label', array(), array( 'type' => 'text', 'layout' => 'vertical' ), false, false, array() ),
			),
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
		c(
			'y_axis',
			'Y-Axis Values',
			array(
				c(
					'y_axis_values',
					'Y-Axis Scale',
					array(
						c( 'y_axis_value', 'Y-Axis Value', array(), array( 'type' => 'text', 'layout' => 'vertical' ), false, false, array() ),
					),
					array(
						'type'            => 'repeater',
						'layout'          => 'vertical',
						'repeaterOptions' => array(
							'titleTemplate'   => '{y_axis_value}',
							'defaultTitle'    => 'Value',
							'buttonName'      => 'Add Y-Axis Value',
							'defaultNewValue' => array( 'y_axis_value' => '0' ),
						),
					),
					false,
					false,
					array()
				),
			),
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
		c(
			'data',
			'Chart Data Points',
			array(
				c(
					'chart_data',
					'Data Points',
					array(
						c( 'x_axis_label', 'X-Axis Label', array(), array( 'type' => 'text', 'layout' => 'vertical' ), false, false, array() ),
						c(
							'data_value',
							'Data Value',
							array(),
							array(
								'type'         => 'number',
								'layout'       => 'inline',
								'rangeOptions' => array( 'min' => 0, 'step' => 0.1 ),
							),
							false,
							false,
							array()
						),
					),
					array(
						'type'            => 'repeater',
						'layout'          => 'vertical',
						'repeaterOptions' => array(
							'titleTemplate'   => '{x_axis_label}',
							'defaultTitle'    => 'Point',
							'buttonName'      => 'Add Data Point',
							'defaultNewValue' => array(
								'x_axis_label' => 'Jan',
								'data_value'   => 0,
							),
						),
					),
					false,
					false,
					array()
				),
			),
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
		c(
			'legend',
			'Legend',
			array(
				c( 'show_legend', 'Show Legend', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c(
					'legend_position',
					'Legend Position',
					array(),
					array(
						'type'      => 'dropdown',
						'layout'    => 'inline',
						'items'     => $legend_position_items,
						'condition' => array( 'path' => 'content.legend.show_legend', 'operand' => 'equals', 'value' => true ),
					),
					false,
					false,
					array()
				),
				c(
					'legend_alignment',
					'Legend Alignment',
					array(),
					array(
						'type'      => 'dropdown',
						'layout'    => 'inline',
						'items'     => $legend_alignment_items,
						'condition' => array( 'path' => 'content.legend.show_legend', 'operand' => 'equals', 'value' => true ),
					),
					false,
					false,
					array()
				),
			),
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
	);
}
