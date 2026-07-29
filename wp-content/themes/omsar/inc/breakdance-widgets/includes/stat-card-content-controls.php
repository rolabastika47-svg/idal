<?php
/**
 * Breakdance content controls for Statistics Card element.
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
function omsar_bd_stat_card_content_controls() {
	return array(
		c(
			'content',
			'Content',
			array(
				c(
					'number',
					'Number',
					array(),
					array(
						'type'   => 'text',
						'layout' => 'inline',
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
					'title',
					'Title',
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
					'background_color',
					'Background Color',
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
					'icon',
					'Icon',
					array(),
					array(
						'type'   => 'wpmedia',
						'layout' => 'vertical',
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
