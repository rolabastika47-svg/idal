<?php
/**
 * Breakdance content controls for Data Table element.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Breakdance\Elements\c;

/**
 * @return array<string, mixed>
 */
function omsar_bd_data_table_default_content_properties() {
	$defaults = omsar_bd_data_table_default_settings();

	return array(
		'table'   => array(
			'table_title'      => $defaults['table_title'],
			'show_export_icon' => false,
			'show_zoom_icon'   => false,
		),
		'columns' => array(
			'table_columns' => $defaults['table_columns'],
		),
		'rows'    => array(
			'table_rows' => $defaults['table_rows'],
		),
	);
}

/**
 * @return array<int, mixed>
 */
function omsar_bd_data_table_content_controls() {
	return array(
		c(
			'table',
			'Table Content',
			array(
				c(
					'table_title',
					'Table Title',
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
					'show_export_icon',
					'Show Export Icon',
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
					'show_zoom_icon',
					'Show Zoom/Expand Icon',
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
		c(
			'columns',
			'Table Columns',
			array(
				c(
					'table_columns',
					'Columns',
					array(
						c(
							'column_label',
							'Column Label',
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
							'titleTemplate'   => '{column_label}',
							'defaultTitle'    => 'Column',
							'buttonName'      => 'Add Column',
							'defaultNewValue' => array( 'column_label' => 'Column' ),
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
			'rows',
			'Table Rows',
			array(
				c(
					'table_rows',
					'Rows',
					array(
						c(
							'row_values',
							'Row Values',
							array(),
							array(
								'type'        => 'text',
								'layout'      => 'vertical',
								'textOptions' => array( 'multiline' => true ),
								'placeholder' => 'Value 1 | Value 2 | Value 3',
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
							'titleTemplate'   => '{row_values}',
							'defaultTitle'    => 'Row',
							'buttonName'      => 'Add Row',
							'defaultNewValue' => array( 'row_values' => '' ),
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
