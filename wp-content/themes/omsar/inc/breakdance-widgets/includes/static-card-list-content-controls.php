<?php
/**
 * Breakdance content controls for Static Card List element.
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
function omsar_bd_static_card_list_content_controls() {
	$column_items = array(
		array( 'text' => '1', 'value' => '1' ),
		array( 'text' => '2', 'value' => '2' ),
		array( 'text' => '3', 'value' => '3' ),
		array( 'text' => '4', 'value' => '4' ),
	);

	$color_bg_condition = array(
		'condition' => array(
			'path'    => '%%CURRENTPATH%%.background_type',
			'operand' => 'equals',
			'value'   => 'color',
		),
	);

	$image_bg_condition = array(
		'condition' => array(
			'path'    => '%%CURRENTPATH%%.background_type',
			'operand' => 'equals',
			'value'   => 'image',
		),
	);

	return array(
		c(
			'content',
			'Content',
			array(
				c(
					'card_style',
					'Card Style',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Style 1: Image/Color Above Text', 'value' => 'style1' ),
							array( 'text' => 'Style 2: Text Overlay on Background', 'value' => 'style2' ),
						),
					),
					false,
					false,
					array()
				),
				c(
					'columns',
					'Columns',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $column_items,
					),
					false,
					false,
					array()
				),
				c(
					'columns_tablet',
					'Columns (Tablet)',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $column_items,
					),
					false,
					false,
					array()
				),
				c(
					'columns_mobile',
					'Columns (Mobile)',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => $column_items,
					),
					false,
					false,
					array()
				),
				c(
					'cards',
					'Cards',
					array(
						c(
							'background_type',
							'Background Type',
							array(),
							array(
								'type'   => 'dropdown',
								'layout' => 'inline',
								'items'  => array(
									array( 'text' => 'Solid Color', 'value' => 'color' ),
									array( 'text' => 'Image', 'value' => 'image' ),
								),
							),
							false,
							false,
							array()
						),
						c(
							'background_color',
							'Background Color',
							array(),
							array(
								'type'   => 'color',
								'layout' => 'inline',
							) + $color_bg_condition,
							false,
							false,
							array()
						),
						c(
							'background_image',
							'Background Image',
							array(),
							array(
								'type'   => 'wpmedia',
								'layout' => 'vertical',
							) + $image_bg_condition,
							false,
							false,
							array()
						),
						c(
							'gradient_color_from',
							'Gradient Color From (Style 2 only)',
							array(),
							array(
								'type'   => 'color',
								'layout' => 'inline',
							),
							false,
							false,
							array()
						),
						c(
							'gradient_color_to',
							'Gradient Color To (Style 2 only)',
							array(),
							array(
								'type'   => 'color',
								'layout' => 'inline',
							),
							false,
							false,
							array()
						),
						c(
							'title',
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
							'description',
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
							'categories',
							'Categories (Style 2 only)',
							array(
								c(
									'category_text',
									'Category Text',
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
									'category_color',
									'Category Color',
									array(),
									array(
										'type'   => 'color',
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
									'titleTemplate'   => '{category_text}',
									'defaultTitle'    => 'Category',
									'buttonName'      => 'Add Category',
									'defaultNewValue' => array(
										'category_text'  => 'CATEGORY',
										'category_color' => '#ffffff',
									),
								),
							),
							false,
							false,
							array()
						),
						c(
							'button_text',
							'Button Text (Style 2 only)',
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
							'button_link',
							'Button Link (Style 2 only)',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'vertical',
								'placeholder' => 'https://your-link.com',
							),
							false,
							false,
							array()
						),
						c(
							'button_new_tab',
							'Open in New Tab',
							array(),
							array(
								'type'   => 'toggle',
								'layout' => 'inline',
							),
							false,
							false,
							array()
						),
						c(
							'button_nofollow',
							'Add nofollow',
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
							'titleTemplate'   => '{title}',
							'defaultTitle'    => 'Card',
							'buttonName'      => 'Add Card',
							'defaultNewValue' => array(
								'title'              => 'Card Title',
								'description'        => 'Card description text goes here.',
								'background_type'    => 'color',
								'background_color'   => '#6366f1',
								'gradient_color_from' => '#6366f1',
								'gradient_color_to'   => '#8b5cf6',
								'categories'         => array(
									array(
										'category_text'  => 'CATEGORY',
										'category_color' => '#ffffff',
									),
								),
								'button_text'        => 'Learn More',
								'button_link'        => '',
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
	);
}
