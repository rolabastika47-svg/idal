<?php
/**
 * Breakdance design controls for Static Card List element.
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
function omsar_bd_static_card_list_default_design_properties() {
	return array(
		'card'        => array(
			'border_radius' => array( 'number' => 12, 'unit' => 'px', 'style' => '12px' ),
			'gap'           => array( 'number' => 30, 'unit' => 'px', 'style' => '30px' ),
			'min_height'    => array( 'number' => 400, 'unit' => 'px', 'style' => '400px' ),
		),
		'title'       => array(
			'color'         => '#1f2937',
			'margin_bottom' => array( 'number' => 15, 'unit' => 'px', 'style' => '15px' ),
		),
		'description' => array(
			'color'         => '#6b7280',
			'margin_bottom' => array( 'number' => 0, 'unit' => 'px', 'style' => '0px' ),
		),
		'category'    => array(
			'margin_bottom' => array( 'number' => 10, 'unit' => 'px', 'style' => '10px' ),
		),
		'button'      => array(
			'text_color'       => '#ffffff',
			'background_color' => 'transparent',
			'border_radius'    => array( 'number' => 0, 'unit' => 'px', 'style' => '0px' ),
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_static_card_list_design_controls() {
	$style2_condition = array(
		'condition' => array(
			'path'    => 'content.content.card_style',
			'operand' => 'equals',
			'value'   => 'style2',
		),
	);

	return array(
		omsar_bd_design_section(
			'card',
			'Card Style',
			array(
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
				getPresetSection(
					'EssentialElements\\spacing_padding_all',
					'Padding',
					'padding',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'gap',
					'Gap Between Cards',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					)
				),
				omsar_bd_design_control(
					'min_height',
					'Card Min Height',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px', 'vh' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 1000, 'step' => 10 ),
					)
				),
				getPresetSection(
					'EssentialElements\\borders',
					'Box Shadow',
					'borders',
					array( 'type' => 'popout' )
				),
			)
		),
		omsar_bd_design_section(
			'title',
			'Title Style',
			array(
				omsar_bd_design_control( 'color', 'Title Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'margin_bottom',
					'Margin Bottom',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'description',
			'Description Style',
			array(
				omsar_bd_design_control( 'color', 'Description Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'margin_bottom',
					'Margin Bottom',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
					)
				),
			)
		),
		omsar_bd_design_section(
			'category',
			'Category Style',
			array(
				getPresetSection(
					'EssentialElements\\typography',
					'Typography',
					'typography',
					array( 'type' => 'popout' )
				),
				omsar_bd_design_control(
					'margin_bottom',
					'Margin Bottom',
					array(
						'type'        => 'unit',
						'layout'      => 'inline',
						'unitOptions' => array( 'types' => array( 'px' ) ),
						'rangeOptions' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
					)
				),
			),
			$style2_condition
		),
		omsar_bd_design_section(
			'button',
			'Button Style',
			array(
				omsar_bd_design_control( 'text_color', 'Text Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
				omsar_bd_design_control( 'background_color', 'Background Color', array( 'type' => 'color', 'layout' => 'inline' ) ),
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
			),
			$style2_condition
		),
	);
}
