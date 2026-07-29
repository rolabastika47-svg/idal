<?php
/**
 * Breakdance content controls for Animated Stats Counter element.
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
function omsar_bd_animated_stats_counter_content_controls() {
	$image_icon_condition = array(
		'condition' => array(
			'path'    => '%%CURRENTPATH%%.icon_type',
			'operand' => 'equals',
			'value'   => 'image',
		),
	);

	$class_icon_condition = array(
		'condition' => array(
			'path'    => '%%CURRENTPATH%%.icon_type',
			'operand' => 'equals',
			'value'   => 'icon',
		),
	);

	return array(
		c(
			'items',
			'Counter Items',
			array(
				c(
					'counter_items',
					'Counter Items',
					array(
						c(
							'icon_type',
							'Icon Type',
							array(),
							array(
								'type'   => 'dropdown',
								'layout' => 'inline',
								'items'  => array(
									array( 'text' => 'Image', 'value' => 'image' ),
									array( 'text' => 'Icon', 'value' => 'icon' ),
								),
							),
							false,
							false,
							array()
						),
						c(
							'icon_image',
							'Icon Image',
							array(),
							array(
								'type'   => 'wpmedia',
								'layout' => 'vertical',
							) + $image_icon_condition,
							false,
							false,
							array()
						),
						c(
							'icon_class',
							'Icon Class',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'vertical',
								'placeholder' => 'bi bi-star',
							) + $class_icon_condition,
							false,
							false,
							array()
						),
						c(
							'counter_value',
							'Counter Value',
							array(),
							array(
								'type'         => 'number',
								'layout'       => 'inline',
								'rangeOptions' => array(
									'min'  => 0,
									'step' => 1,
								),
							),
							false,
							false,
							array()
						),
						c(
							'unit',
							'Unit',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'inline',
								'placeholder' => 'e.g., +, %, K, M',
							),
							false,
							false,
							array()
						),
						c(
							'label_text',
							'Label Text',
							array(),
							array(
								'type'   => 'text',
								'layout' => 'vertical',
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
							'titleTemplate'   => '{label_text}',
							'defaultTitle'    => 'Counter',
							'buttonName'      => 'Add Counter',
							'defaultNewValue' => array(
								'icon_type'     => 'image',
								'icon_class'    => 'bi bi-star',
								'counter_value' => 10000,
								'unit'          => '+',
								'label_text'    => 'Happy Customers',
							),
						),
					),
					false,
					false,
					array()
				),
				c(
					'animation_duration',
					'Animation Duration (seconds)',
					array(),
					array(
						'type'         => 'number',
						'layout'       => 'inline',
						'rangeOptions' => array(
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.1,
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
