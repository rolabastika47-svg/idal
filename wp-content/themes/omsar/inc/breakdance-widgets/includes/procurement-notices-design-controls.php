<?php
/**
 * Breakdance design controls for Procurement Notices element.
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
function omsar_bd_procurement_notices_default_design_properties() {
	return array(
		'filters'    => array(
			'text_color'              => '#7db3e8',
			'background_color'        => 'transparent',
			'border_color'            => '#7db3e8',
			'border_width'            => array( 'number' => 2, 'unit' => 'px' ),
			'border_radius'           => array( 'number' => 24, 'unit' => 'px' ),
			'active_text_color'       => '#ffffff',
			'active_background_color' => '#7db3e8',
			'active_border_color'     => '#7db3e8',
			'hover_text_color'        => '#ffffff',
			'hover_background_color'  => '#7db3e8',
			'hover_border_color'      => '#7db3e8',
		),
		'title'      => array(
			'color' => '#000000',
		),
		'badge_open' => array(
			'text_color'       => '#28a745',
			'background_color' => 'transparent',
			'border_color'     => '#28a745',
			'border_radius'    => array( 'number' => 20, 'unit' => 'px' ),
		),
		'badge_closed' => array(
			'text_color'       => '#dc3545',
			'background_color' => 'transparent',
			'border_color'     => '#dc3545',
			'border_radius'    => array( 'number' => 20, 'unit' => 'px' ),
		),
		'badge_cancelled' => array(
			'text_color'       => '#6c757d',
			'background_color' => 'transparent',
			'border_color'     => '#6c757d',
			'border_radius'    => array( 'number' => 20, 'unit' => 'px' ),
		),
		'card'       => array(
			'border_radius' => array( 'number' => 8, 'unit' => 'px' ),
			'grid_gap'      => array( 'number' => 20, 'unit' => 'px' ),
			'min_height'    => array( 'number' => 150, 'unit' => 'px' ),
		),
		'gradient'   => array(
			'color_from' => 'rgba(227, 242, 253, 0.8)',
			'color_to'   => 'rgba(255, 255, 255, 0.6)',
			'angle'      => array( 'number' => 135, 'unit' => 'deg' ),
			'opacity'    => array( 'number' => 0.3, 'unit' => '' ),
		),
		'description' => array(
			'color'      => '#666666',
			'icon_color' => '#999999',
		),
		'dates'      => array(
			'color'      => '#666666',
			'icon_color' => '#999999',
		),
		'apply_button' => array(
			'text_color'       => '#ffffff',
			'background_color' => '#1a3e7b',
			'border_radius'    => array( 'number' => 25, 'unit' => 'px' ),
		),
		'load_more'  => array(
			'background_color'       => 'transparent',
			'text_color'             => '#7db3e8',
			'border_color'           => '#7db3e8',
			'border_width'           => array( 'number' => 2, 'unit' => 'px' ),
			'border_radius'          => array( 'number' => 24, 'unit' => 'px' ),
			'hover_background_color' => '#7db3e8',
			'hover_text_color'       => '#ffffff',
			'hover_border_color'     => '#7db3e8',
			'wrapper_alignment'      => 'center',
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_procurement_notices_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'left' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'right' ),
	);

	return array(
		omsar_bd_design_section(
			'filters',
			'Filter Tabs Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_width', 'Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'active_text_color', 'Active Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'active_background_color', 'Active Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'active_border_color', 'Active Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\spacing_margin_all', 'Margin', 'margin', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'badge_open',
			'Badge Style - Open',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'badge_closed',
			'Badge Style - Closed',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'badge_cancelled',
			'Badge Style - Cancelled',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'card',
			'Card Style',
			array(
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'grid_gap', 'Gap Between Cards', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'min_height', 'Card Minimum Height', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Card Content Padding', 'padding', array( 'type' => 'popout' ) ),
				getPresetSection( 'EssentialElements\\borders', 'Border & Shadow', 'borders', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'gradient',
			'Gradient Overlay',
			array(
				omsar_bd_design_control( 'color_from', 'Color From', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'color_to', 'Color To', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'angle', 'Angle', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'deg' ) ) ) ),
				omsar_bd_design_control( 'opacity', 'Opacity', array( 'type' => 'number', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'description',
			'Description Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'icon_color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'dates',
			'Date Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'icon_color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'apply_button',
			'Apply Button Style',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				getPresetSection( 'EssentialElements\\spacing_margin_all', 'Margin', 'margin', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'load_more',
			'Load More Button',
			array(
				getPresetSection( 'EssentialElements\\typography', 'Typography', 'typography', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'border_width', 'Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'hover_background_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control(
					'wrapper_alignment',
					'Alignment',
					array(
						'type'   => 'button_bar',
						'layout' => 'inline',
						'items'  => $align_items,
					)
				),
				getPresetSection( 'EssentialElements\\spacing_margin_y', 'Wrapper Margin', 'wrapper_margin', array( 'type' => 'popout' ) ),
			)
		),
	);
}
