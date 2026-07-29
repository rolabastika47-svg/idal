<?php
/**
 * Breakdance content controls for Procurement Notices element.
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
function omsar_bd_procurement_notices_content_controls() {
	$column_items = array(
		array( 'text' => '1', 'value' => '1' ),
		array( 'text' => '2', 'value' => '2' ),
		array( 'text' => '3', 'value' => '3' ),
		array( 'text' => '4', 'value' => '4' ),
	);

	return array(
		c(
			'content',
			'Content',
			array(
				c(
					'posts_per_page',
					'Posts Per Page',
					array(),
					array(
						'type'    => 'number',
						'layout'  => 'inline',
						'rangeOptions' => array(
							'min'  => 1,
							'max'  => 100,
							'step' => 1,
						),
					),
					false,
					false,
					array()
				),
				c(
					'orderby',
					'Order By',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Date', 'value' => 'date' ),
							array( 'text' => 'Title', 'value' => 'title' ),
							array( 'text' => 'Opening Date', 'value' => 'meta_value' ),
						),
					),
					false,
					false,
					array()
				),
				c(
					'order',
					'Order',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Descending', 'value' => 'DESC' ),
							array( 'text' => 'Ascending', 'value' => 'ASC' ),
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
					'default_image',
					'Default Image',
					array(),
					array(
						'type'   => 'wpmedia',
						'layout' => 'vertical',
					),
					false,
					false,
					array()
				),
				c(
					'filter_label',
					'Filter Label',
					array(),
					array(
						'type'        => 'text',
						'layout'      => 'vertical',
						'placeholder' => function_exists( 'pll__' ) ? pll__( 'Filter by:' ) : 'Filter by:',
					),
					false,
					false,
					array()
				),
				c(
					'gradient_enabled',
					'Enable Gradient Overlay',
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
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
	);
}
