<?php
/**
 * Breakdance content controls for Custom Timeline element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\c;

/**
 * @return array<int, mixed>
 */
function omsar_bd_timeline_content_controls() {
	$same_side_condition = array(
		'condition' => array(
			'path'    => 'content.layout.timeline_layout',
			'operand' => 'equals',
			'value'   => 'same_side',
		),
	);

	return array(
		c(
			'items',
			'Timeline Items',
			array(
				c(
					'timeline_items',
					'Timeline Items',
					array(
						c(
							'item_title',
							'Title',
							array(),
							array(
								'type'   => 'text',
								'layout' => 'vertical',
							),
							false,
							false,
							array()
						),
						c(
							'item_date',
							'Date/Order',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'vertical',
								'placeholder' => 'e.g., January 2024 or Q1 2024',
							),
							false,
							false,
							array()
						),
						c(
							'item_description',
							'Description',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'vertical',
								'textOptions' => array( 'multiline' => true ),
							),
							false,
							false,
							array()
						),
						c(
							'item_marker_filled',
							'Filled Marker',
							array(),
							array(
								'type'   => 'toggle',
								'layout' => 'inline',
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
							'titleTemplate'   => '{item_title}',
							'defaultTitle'    => 'Timeline Item',
							'buttonName'      => 'Add Item',
							'defaultNewValue' => array(
								'item_title'         => 'Timeline Item',
								'item_date'          => 'January 2024',
								'item_description'   => 'Timeline item description goes here.',
								'item_marker_filled' => false,
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
			'layout',
			'Layout',
			array(
				c(
					'timeline_layout',
					'Timeline Layout',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Alternating (Left/Right)', 'value' => 'alternating' ),
							array( 'text' => 'Alternating (Right/Left)', 'value' => 'alternating_right_left' ),
							array( 'text' => 'All Items Same Side', 'value' => 'same_side' ),
						),
					),
					false,
					false,
					array()
				),
				c(
					'same_side_alignment',
					'Items Alignment',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Left', 'value' => 'left' ),
							array( 'text' => 'Right', 'value' => 'right' ),
						),
					) + $same_side_condition,
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
