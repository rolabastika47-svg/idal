<?php
/**
 * Breakdance design controls for Custom Timeline element.
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
function omsar_bd_timeline_default_design_properties() {
	return array(
		'line'        => array(
			'color' => '#d1d5db',
			'width' => array( 'number' => 2, 'unit' => 'px', 'style' => '2px' ),
		),
		'markers'     => array(
			'size'          => array( 'number' => 16, 'unit' => 'px', 'style' => '16px' ),
			'border_width'  => array( 'number' => 2, 'unit' => 'px', 'style' => '2px' ),
			'color'         => '#60a5fa',
			'background'    => '#ffffff',
		),
		'title'       => array(
			'color' => '#1f2937',
		),
		'date'        => array(
			'color' => '#60a5fa',
		),
		'description' => array(
			'color' => '#6b7280',
		),
		'spacing'     => array(
			'item_spacing'    => array( 'number' => 40, 'unit' => 'px', 'style' => '40px' ),
			'content_spacing' => array( 'number' => 8, 'unit' => 'px', 'style' => '8px' ),
		),
		'background'  => array(
			'background_color' => '#ffffff',
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_timeline_design_controls() {
	return array(
		omsar_bd_design_section(
			'line',
			'Timeline Line',
			array(
				omsar_bd_design_control( 'color', 'Line Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'width',
					'Line Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 1, 'max' => 10, 'step' => 1 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'markers',
			'Markers',
			array(
				omsar_bd_design_control(
					'size',
					'Marker Size',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 8, 'max' => 40, 'step' => 1 ),
					)
				),
				omsar_bd_design_control(
					'border_width',
					'Marker Border Width',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 1, 'max' => 5, 'step' => 1 ),
					)
				),
				omsar_bd_design_control( 'color', 'Marker Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background', 'Marker Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title',
			array(
				omsar_bd_design_control( 'color', 'Title Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'date',
			'Date',
			array(
				omsar_bd_design_control( 'color', 'Date Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'description',
			'Description',
			array(
				omsar_bd_design_control( 'color', 'Description Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'spacing',
			'Spacing',
			array(
				omsar_bd_design_control(
					'item_spacing',
					'Item Spacing',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 20, 'max' => 100, 'step' => 5 ),
					)
				),
				omsar_bd_design_control(
					'content_spacing',
					'Content Spacing',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 50, 'step' => 2 ),
					)
				),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Content Padding',
					'content_padding',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'background',
			'Background',
			array(
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Wrapper Padding',
					'wrapper_padding',
					array( 'type' => 'popout' )
				),
			)
		),
	);
}
