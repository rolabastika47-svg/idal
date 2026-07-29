<?php
/**
 * Breakdance content controls for Circular Chart element.
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
function omsar_bd_circular_chart_default_content_properties() {
	$defaults = omsar_bd_circular_chart_default_settings();

	return array(
		'chart'  => array(
			'chart_title'      => $defaults['chart_title'],
			'show_export_icon' => false,
			'show_zoom_icon'   => false,
			'chart_type'       => $defaults['chart_type'],
			'reverse_chart'    => false,
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
function omsar_bd_circular_chart_content_controls() {
	$chart_type_items = array(
		array( 'text' => 'Pie Chart', 'value' => 'pie' ),
		array( 'text' => 'Donut Chart', 'value' => 'doughnut' ),
	);

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
				c(
					'chart_title',
					'Chart Title',
					array(),
					array( 'type' => 'text', 'layout' => 'vertical' ),
					false,
					false,
					array()
				),
				c(
					'show_export_icon',
					'Show Export Image Icon',
					array(),
					array( 'type' => 'toggle', 'layout' => 'inline' ),
					false,
					false,
					array()
				),
				c(
					'show_zoom_icon',
					'Show Zoom/Expand Icon',
					array(),
					array( 'type' => 'toggle', 'layout' => 'inline' ),
					false,
					false,
					array()
				),
				c(
					'chart_type',
					'Chart Type',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $chart_type_items,
					),
					false,
					false,
					array()
				),
				c(
					'reverse_chart',
					'Reverse Chart Display',
					array(),
					array( 'type' => 'toggle', 'layout' => 'inline' ),
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
			'Chart Segments',
			array(
				c(
					'chart_data',
					'Segments',
					array(
						c(
							'segment_label',
							'Segment Label',
							array(),
							array( 'type' => 'text', 'layout' => 'vertical' ),
							false,
							false,
							array()
						),
						c(
							'segment_value',
							'Segment Value',
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
						c(
							'segment_color',
							'Segment Color',
							array(),
							array( 'type' => 'color', 'layout' => 'inline' ),
							false,
							false,
							array()
						),
					),
					array(
						'type'            => 'repeater',
						'layout'          => 'vertical',
						'repeaterOptions' => array(
							'titleTemplate'   => '{segment_label}',
							'defaultTitle'    => 'Segment',
							'buttonName'      => 'Add Segment',
							'defaultNewValue' => array(
								'segment_label' => 'Segment',
								'segment_value' => 25,
								'segment_color' => '#2563eb',
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
				c(
					'show_legend',
					'Show Legend',
					array(),
					array( 'type' => 'toggle', 'layout' => 'inline' ),
					false,
					false,
					array()
				),
				c(
					'legend_position',
					'Legend Position',
					array(),
					array(
						'type'      => 'dropdown',
						'layout'    => 'inline',
						'items'     => $legend_position_items,
						'condition' => array(
							'path'    => 'content.legend.show_legend',
							'operand' => 'equals',
							'value'   => true,
						),
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
						'condition' => array(
							'path'    => 'content.legend.show_legend',
							'operand' => 'equals',
							'value'   => true,
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
	);
}
