<?php
/**
 * Breakdance content controls for Projects element.
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
function omsar_bd_projects_content_controls() {
	$post_type_items = array();
	foreach ( omsar_bd_projects_get_post_type_options() as $value => $label ) {
		$post_type_items[] = array( 'text' => $label, 'value' => $value );
	}

	$taxonomy_items = array();
	foreach ( omsar_bd_projects_get_taxonomy_options() as $value => $label ) {
		$taxonomy_items[] = array( 'text' => $label, 'value' => $value );
	}

	$acf_items = array();
	foreach ( omsar_bd_projects_get_acf_field_options() as $value => $label ) {
		$acf_items[] = array( 'text' => $label, 'value' => $value );
	}

	$dropdown_tax_condition = array(
		'condition' => array(
			'path'    => 'content.content.enable_dropdown_filter',
			'operand' => 'is set',
			'value'   => '',
		),
	);

	$badge_tax_condition = array(
		'condition' => array(
			'path'    => 'content.content.badge_source',
			'operand' => 'equals',
			'value'   => 'taxonomy',
		),
	);

	$badge_cf_condition = array(
		'condition' => array(
			'path'    => 'content.content.badge_source',
			'operand' => 'equals',
			'value'   => 'custom_field',
		),
	);

	return array(
		c(
			'content',
			'Content',
			array(
				c( 'post_type', 'Post Type', array(), array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $post_type_items ), false, false, array() ),
				c( 'posts_per_page', 'Posts Per Page', array(), array( 'type' => 'number', 'layout' => 'inline', 'rangeOptions' => array( 'min' => 1, 'max' => 100, 'step' => 1 ) ), false, false, array() ),
				c( 'enable_load_more', 'Enable Load More Button', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c(
					'load_more_text',
					'Load More Button Text',
					array(),
					array(
						'type'      => 'text',
						'layout'    => 'inline',
						'condition' => array( 'path' => 'content.content.enable_load_more', 'operand' => 'is set', 'value' => '' ),
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
							array( 'text' => 'Menu Order', 'value' => 'menu_order' ),
							array( 'text' => 'Random', 'value' => 'rand' ),
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
				c( 'enable_search', 'Enable Search', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c(
					'search_placeholder',
					'Search Placeholder',
					array(),
					array( 'type' => 'text', 'layout' => 'inline', 'condition' => array( 'path' => 'content.content.enable_search', 'operand' => 'is set', 'value' => '' ) ),
					false,
					false,
					array()
				),
				c(
					'search_label',
					'Search Label',
					array(),
					array( 'type' => 'text', 'layout' => 'inline', 'condition' => array( 'path' => 'content.content.enable_search', 'operand' => 'is set', 'value' => '' ) ),
					false,
					false,
					array()
				),
				c( 'enable_dropdown_filter', 'Enable Dropdown Filter', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c(
					'dropdown_filter_label',
					'Dropdown Label',
					array(),
					array( 'type' => 'text', 'layout' => 'inline' ) + $dropdown_tax_condition,
					false,
					false,
					array()
				),
				c(
					'dropdown_filter_taxonomy',
					'Dropdown Taxonomy',
					array(),
					array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $taxonomy_items ) + $dropdown_tax_condition,
					false,
					false,
					array()
				),
				c(
					'dropdown_filter_custom_field',
					'Filter by Custom Field',
					array(),
					array(
						'type'        => 'text',
						'layout'      => 'inline',
						'placeholder' => 'e.g., pillar_option',
						'condition'   => array(
							array(
								array(
									'path'    => 'content.content.enable_dropdown_filter',
									'operand' => 'is set',
									'value'   => '',
								),
								array(
									'path'    => 'content.content.dropdown_filter_taxonomy',
									'operand' => 'not equals',
									'value'   => '',
								),
							),
						),
					),
					false,
					false,
					array()
				),
				c( 'default_image', 'Default Image', array(), array( 'type' => 'wpmedia', 'layout' => 'vertical' ), false, false, array() ),
				c(
					'badge_source',
					'Pillar Badge Source',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'None', 'value' => 'none' ),
							array( 'text' => 'Taxonomy', 'value' => 'taxonomy' ),
							array( 'text' => 'ACF Custom Field', 'value' => 'custom_field' ),
						),
					),
					false,
					false,
					array()
				),
				c(
					'badge_taxonomy',
					'Badge Taxonomy',
					array(),
					array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $taxonomy_items ) + $badge_tax_condition,
					false,
					false,
					array()
				),
				c(
					'badge_custom_field',
					'Pillar ACF Field',
					array(),
					array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $acf_items ) + $badge_cf_condition,
					false,
					false,
					array()
				),
				c(
					'badge_taxonomy_display',
					'Display Type',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Name', 'value' => 'name' ),
							array( 'text' => 'ID', 'value' => 'id' ),
						),
						'condition' => array(
							'path'    => 'content.content.badge_source',
							'operand' => 'equals',
							'value'   => 'custom_field',
						),
					),
					false,
					false,
					array()
				),
				c( 'make_card_clickable', 'Make Card Clickable', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'show_read_more', 'Show Read More Button', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c(
					'read_more_text',
					'Read More Button Text',
					array(),
					array( 'type' => 'text', 'layout' => 'inline', 'condition' => array( 'path' => 'content.content.show_read_more', 'operand' => 'is set', 'value' => '' ) ),
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
						'items'  => array(
							array( 'text' => '1', 'value' => '1' ),
							array( 'text' => '2', 'value' => '2' ),
							array( 'text' => '3', 'value' => '3' ),
							array( 'text' => '4', 'value' => '4' ),
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
