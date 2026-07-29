<?php
/**
 * Breakdance design controls for List Posts by Taxonomy element.
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
function omsar_bd_posts_by_taxonomy_default_design_properties() {
	return array(
		'tabs'      => array(
			'tabs_alignment'            => 'flex-start',
			'tab_inactive_text_color'   => '#21759b',
			'tab_inactive_bg_color'     => '#e0e2e6',
			'tab_inactive_border_color' => 'transparent',
			'tab_inactive_border_width' => array( 'number' => 0, 'unit' => 'px' ),
			'tab_inactive_border_radius'=> array( 'number' => 50, 'unit' => 'px' ),
			'tab_active_text_color'     => '#ffffff',
			'tab_active_bg_color'       => '#5693ff',
			'tab_active_border_color'   => 'transparent',
			'tab_active_border_width'   => array( 'number' => 0, 'unit' => 'px' ),
			'tab_active_border_radius'  => array( 'number' => 50, 'unit' => 'px' ),
			'tab_active_shadow_color'   => 'rgba(86, 147, 255, 0.3)',
			'tab_hover_text_color'      => '#ffffff',
			'tab_hover_bg_color'        => '#4a7cff',
			'tab_hover_border_color'    => 'transparent',
		),
		'search'    => array(
			'search_alignment'       => 'left',
			'search_width'             => array( 'number' => 400, 'unit' => 'px' ),
			'search_border_color'      => '#e0e2e6',
			'search_border_color_focus'=> '#5693ff',
			'search_icon_color'        => '#999999',
		),
		'posts'     => array(
			'image_fit' => 'cover',
		),
		'load_more' => array(
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
		'style5'    => array(
			'style5_title_color'              => '#ffffff',
			'style5_excerpt_color'            => 'rgba(255, 255, 255, 0.95)',
			'style5_category_bg_color'        => 'rgba(255, 255, 255, 0.2)',
			'style5_category_text_color'      => '#ffffff',
			'style5_normal_title_color'       => '',
			'style5_normal_excerpt_color'     => '',
			'style5_normal_category_bg_color' => '',
			'style5_normal_category_text_color' => '',
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_posts_by_taxonomy_design_controls() {
	$align_items = array(
		array( 'text' => 'Left', 'value' => 'flex-start' ),
		array( 'text' => 'Center', 'value' => 'center' ),
		array( 'text' => 'Right', 'value' => 'flex-end' ),
	);

	$column_items = array(
		array( 'text' => '1', 'value' => '1' ),
		array( 'text' => '2', 'value' => '2' ),
		array( 'text' => '3', 'value' => '3' ),
		array( 'text' => '4', 'value' => '4' ),
		array( 'text' => '5', 'value' => '5' ),
		array( 'text' => '6', 'value' => '6' ),
	);

	return array(
		omsar_bd_design_section(
			'tabs',
			'Tabs Style',
			array(
				omsar_bd_design_control( 'tabs_alignment', 'Tabs Alignment', array( 'type' => 'button_bar', 'layout' => 'inline', 'items' => $align_items ) ),
				omsar_bd_design_control( 'tab_inactive_text_color', 'Inactive Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_inactive_bg_color', 'Inactive Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_inactive_border_color', 'Inactive Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_inactive_border_width', 'Inactive Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'tab_inactive_border_radius', 'Inactive Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'tab_active_text_color', 'Active Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_active_bg_color', 'Active Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_active_border_color', 'Active Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_active_border_width', 'Active Border Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'tab_active_border_radius', 'Active Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px' ) ) ) ),
				omsar_bd_design_control( 'tab_active_shadow_color', 'Active Shadow Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_hover_bg_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'tab_hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'search',
			'Search Style',
			array(
				omsar_bd_design_control(
					'search_alignment',
					'Search Bar Alignment',
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
				omsar_bd_design_control( 'search_width', 'Search Box Width', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
				omsar_bd_design_control( 'search_border_color', 'Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'search_border_color_focus', 'Border Color (Focus)', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'search_icon_color', 'Icon Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
		omsar_bd_design_section(
			'posts',
			'Posts Style',
			array(
				omsar_bd_design_control( 'posts_per_row', 'Posts Per Row', array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $column_items ) ),
				omsar_bd_design_control( 'posts_per_row_style2', 'Posts Per Row (Style 2)', array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => array_slice( $column_items, 0, 4 ) ) ),
				omsar_bd_design_control(
					'image_fit',
					'Image Fit',
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Cover', 'value' => 'cover' ),
							array( 'text' => 'Contain', 'value' => 'contain' ),
						),
					)
				),
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
				omsar_bd_design_control( 'hover_background_color', 'Hover Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_text_color', 'Hover Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'hover_border_color', 'Hover Border Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection( 'EssentialElements\\spacing_padding_all', 'Padding', 'padding', array( 'type' => 'popout' ) ),
				omsar_bd_design_control( 'border_radius', 'Border Radius', array( 'type' => 'unit', 'layout' => 'inline', 'unitOptions' => array( 'types' => array( 'px', '%' ) ) ) ),
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
				getPresetSection( 'EssentialElements\\spacing_margin_y', 'Wrapper Margin', 'wrapper_margin', array( 'type' => 'popout' ) ),
			)
		),
		omsar_bd_design_section(
			'style5',
			'Style 5 Colors',
			array(
				omsar_bd_design_control( 'style5_title_color', 'Overlay Title Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_excerpt_color', 'Overlay Excerpt Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_category_bg_color', 'Overlay Category Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_category_text_color', 'Overlay Category Text', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_normal_title_color', 'Normal Title Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_normal_excerpt_color', 'Normal Excerpt Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_normal_category_bg_color', 'Normal Category Background', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'style5_normal_category_text_color', 'Normal Category Text', array( 'type' => 'color', 'layout' => 'inline' ) ),
			)
		),
	);
}
