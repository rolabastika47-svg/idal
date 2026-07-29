<?php
/**
 * Breakdance design controls for Knowledge and Resources element.
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
function omsar_bd_knowledge_resources_default_design_properties() {
	return array(
		'title'     => array(
			'color'     => '#000000',
			'alignment' => 'left',
		),
		'card'          => array(
			'background_color' => '#ffffff',
			'border_radius'    => array( 'number' => 8, 'unit' => 'px' ),
			'grid_gap'         => array( 'number' => 24, 'unit' => 'px' ),
		),
		'download_link' => array(
			'text_color'       => '#1a3e7b',
			'hover_text_color' => '#7db3e8',
			'icon_color'       => '#dc3545',
		),
		'load_more' => array(
			'background_color'       => '#ffffff',
			'text_color'             => '#4a5568',
			'border_color'           => '#4a5568',
			'border_width'           => array( 'number' => 1, 'unit' => 'px' ),
			'border_radius'          => array( 'number' => 50, 'unit' => 'px' ),
			'hover_background_color' => '#f7fafc',
			'hover_text_color'       => '#2d3748',
			'hover_border_color'     => '#2d3748',
			'wrapper_alignment'      => 'center',
		),
		'search'    => array(
			'background_color' => '#f5f5f5',
			'text_color'       => '#4a5568',
			'border_color'     => '#e2e8f0',
			'border_width'       => array( 'number' => 1, 'unit' => 'px' ),
			'border_radius'      => array( 'number' => 50, 'unit' => 'px' ),
			'icon_color'         => '#718096',
			'max_width'          => array( 'number' => 400, 'unit' => 'px' ),
			'wrapper_margin'     => array(
				'margin' => array(
					'bottom' => array(
						'number' => 24,
						'unit'   => 'px',
						'style'  => '24px',
					),
				),
			),
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_knowledge_resources_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'right' ),
	);

	return array(
		omsar_bd_design_section(
			'title',
			'Title',
			array(
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'color', 'Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'alignment', 'Alignment', array( 'type' => 'button_bar', 'layout' => 'inline', 'items' => $align_items ) ),
				getPresetSection(
					'EssentialElements\\spacing_margin_all',
					'Spacing',
					'margin',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'card',
			'Card',
			array(
				omsar_bd_design_control( 'background_color', 'Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\borders',
					'Border & Shadow',
					'borders',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'grid_gap', 'Grid Gap', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
			)
		),
		omsar_bd_design_section(
			'download_link',
			'PDF / Download Link',
			array(
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'text_color', 'Title Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Title Hover Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'icon_color', 'PDF Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'load_more',
			'Load More Button',
			array(
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_width', 'Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'wrapper_alignment', 'Alignment', array( 'type' => 'button_bar', 'layout' => 'inline', 'items' => $align_items ) ),
				getPresetSection(
					'EssentialElements\\spacing_margin_all',
					'Wrapper Margin',
					'wrapper_margin',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'search',
			'Search Field',
			array(
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_width', 'Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'icon_color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\spacing_margin_all',
					'Wrapper Margin',
					'wrapper_margin',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control( 'max_width', 'Max Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%', 'em' ) ) ) ),
			)
		),
	);
}
