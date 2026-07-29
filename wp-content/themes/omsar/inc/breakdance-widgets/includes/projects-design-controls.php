<?php
/**
 * Breakdance design controls for Projects element (mirrors Elementor style tabs).
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return array<string, mixed>
 */
function omsar_bd_projects_default_design_properties() {
	return array(
		'style'      => array(
			'grid_columns' => '3',
		),
		'read_more'  => array(
			'background_color'       => '#192D50',
			'text_color'             => '#ffffff',
			'hover_background_color' => '#1a3d6b',
			'hover_text_color'       => '#ffffff',
			'border_radius'          => array( 'number' => 50, 'unit' => 'px' ),
		),
		'load_more'  => array(
			'background_color'       => '#5693ff',
			'text_color'             => '#ffffff',
			'border_color'           => 'transparent',
			'border_width'           => array( 'number' => 0, 'unit' => 'px' ),
			'hover_background_color' => '#4a7cff',
			'hover_text_color'       => '#ffffff',
			'hover_border_color'     => 'transparent',
			'border_radius'          => array( 'number' => 50, 'unit' => 'px' ),
			'wrapper_alignment'      => 'center',
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_projects_design_controls() {
	$column_items = array(
		array( 'text' => '1', 'value' => '1' ),
		array( 'text' => '2', 'value' => '2' ),
		array( 'text' => '3', 'value' => '3' ),
		array( 'text' => '4', 'value' => '4' ),
	);

	return array(
		omsar_bd_design_section(
			'style',
			'Style',
			array(
				omsar_bd_design_control(
					'grid_columns',
					'Grid Columns',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $column_items,
					)
				),
			)
		),
		omsar_bd_design_section(
			'read_more',
			'Read More Button Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
					)
				),
				getPresetSection( 'EssentialElements\\borders', 'Border', 'borders', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'transition_duration',
					'Transition Duration',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 's', 'ms' ) ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'load_more',
			'Load More Button Style',
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
				omsar_bd_design_control( 'hover_background_color', 'Hover Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				getPresetSection( 'EssentialElements\\spacing_margin_y', 'Wrapper Margin', 'wrapper_margin', array( 'type' => 'popout' ) ),
				omsar_bd_design_control(
					'border_radius',
					'Border Radius',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', '%' ) ),
					)
				),
				omsar_bd_design_control(
					'transition_duration',
					'Transition Duration',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 's', 'ms' ) ),
					)
				),
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
			)
		),
	);
}
