<?php
/**
 * Breakdance content controls for List Posts by Taxonomy element.
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
function omsar_bd_posts_by_taxonomy_content_controls() {
	$taxonomy_items = array();
	foreach ( omsar_bd_projects_get_taxonomy_options() as $value => $label ) {
		$taxonomy_items[] = array( 'text' => $label, 'value' => $value );
	}

	$post_type_items = array();
	foreach ( omsar_bd_projects_get_post_type_options() as $value => $label ) {
		$post_type_items[] = array( 'text' => $label, 'value' => $value );
	}

	$taxonomy_set_condition = array(
		'condition' => array(
			'path'    => 'content.content.taxonomy',
			'operand' => 'not equals',
			'value'   => '',
		),
	);

	$style5_condition = array(
		'condition' => array(
			'path'    => 'content.content.listing_style',
			'operand' => 'equals',
			'value'   => 'style5',
		),
	);

	$load_more_condition = array(
		'condition' => array(
			'path'    => 'content.content.enable_load_more',
			'operand' => 'is set',
			'value'   => '',
		),
	);

	$search_condition = array(
		'condition' => array(
			'path'    => 'content.content.enable_search',
			'operand' => 'is set',
			'value'   => '',
		),
	);

	$meta_key_condition = array(
		'condition' => array(
			'path'    => 'content.content.custom_field_meta_key',
			'operand' => 'not equals',
			'value'   => '',
		),
	);

	$style5_side_condition = array(
		'condition' => array(
			array(
				array(
					'path'    => 'content.content.listing_style',
					'operand' => 'equals',
					'value'   => 'style5',
				),
				array(
					'path'    => 'content.content.style5_content_overlay',
					'operand' => 'is not set',
					'value'   => '',
				),
			),
		),
	);

	return array(
		c(
			'content',
			'Content',
			array(
				c( 'taxonomy', 'Taxonomy', array(), array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $taxonomy_items ), false, false, array() ),
				c( 'use_acf_field', 'Use ACF Field for Filtering', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $taxonomy_set_condition, false, false, array() ),
				c(
					'acf_field_name',
					'ACF Field Name',
					array(),
					array(
						'type'        => 'text',
						'layout'      => 'inline',
						'placeholder' => 'publication_category',
						'condition'   => array(
							array(
								array(
									'path'    => 'content.content.taxonomy',
									'operand' => 'not equals',
									'value'   => '',
								),
								array(
									'path'    => 'content.content.use_acf_field',
									'operand' => 'is set',
									'value'   => '',
								),
							),
						),
					),
					false,
					false,
					array()
				),
				c( 'post_type', 'Post Type', array(), array( 'type' => 'dropdown', 'layout' => 'inline', 'items' => $post_type_items ), false, false, array() ),
				c( 'show_empty_terms', 'Show Terms with 0 Posts', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'show_all_tab', 'Show "All" Tab', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'hide_tabs_navigation', 'Hide Tabs Navigation', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'posts_per_page', 'Posts Per Page', array(), array( 'type' => 'number', 'layout' => 'inline', 'rangeOptions' => array( 'min' => 1, 'max' => 100, 'step' => 1 ) ), false, false, array() ),
				c( 'enable_load_more', 'Enable Load More Button', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'load_more_text', 'Load More Button Text', array(), array( 'type' => 'text', 'layout' => 'inline' ) + $load_more_condition, false, false, array() ),
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
				c( 'search_placeholder', 'Search Placeholder', array(), array( 'type' => 'text', 'layout' => 'inline' ) + $search_condition, false, false, array() ),
				c( 'search_label', 'Search Label', array(), array( 'type' => 'text', 'layout' => 'inline' ) + $search_condition, false, false, array() ),
				c(
					'listing_style',
					'Listing Style',
					array(),
					array(
						'type'   => 'dropdown',
						'layout' => 'inline',
						'items'  => array(
							array( 'text' => 'Style 1 - Grid (Image Top)', 'value' => 'style1' ),
							array( 'text' => 'Style 2 - Horizontal (Image Left)', 'value' => 'style2' ),
							array( 'text' => 'Style 3 - Content Above Image', 'value' => 'style3' ),
							array( 'text' => 'Style 5 - Modern Card with Categories', 'value' => 'style5' ),
						),
					),
					false,
					false,
					array()
				),
				c( 'show_date', 'Show Date', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
				c( 'default_image', 'Default Image', array(), array( 'type' => 'wpmedia', 'layout' => 'vertical' ), false, false, array() ),
				c( 'clickable_cards', 'Clickable Cards', array(), array( 'type' => 'toggle', 'layout' => 'inline' ), false, false, array() ),
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
							array( 'text' => '5', 'value' => '5' ),
							array( 'text' => '6', 'value' => '6' ),
						),
					),
					false,
					false,
					array()
				),
				c( 'style5_content_overlay', 'Content Overlay on Image', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_condition, false, false, array() ),
				c( 'style5_side_layout', 'Side Layout (Image Left)', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_side_condition, false, false, array() ),
				c( 'style5_show_categories', 'Show Categories', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_condition, false, false, array() ),
				c( 'style5_show_excerpt', 'Show Description (Excerpt)', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_condition, false, false, array() ),
				c( 'style5_show_date', 'Show Publication Date', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_condition, false, false, array() ),
				c( 'style5_show_author', 'Show Author', array(), array( 'type' => 'toggle', 'layout' => 'inline' ) + $style5_condition, false, false, array() ),
				c( 'custom_field_meta_key', 'Custom Field Meta Key', array(), array( 'type' => 'text', 'layout' => 'inline', 'placeholder' => 'post_type_option' ), false, false, array() ),
				c( 'custom_field_meta_value', 'Custom Field Meta Value', array(), array( 'type' => 'text', 'layout' => 'inline', 'placeholder' => 'news' ) + $meta_key_condition, false, false, array() ),
			),
			array( 'type' => 'section' ),
			false,
			false,
			array()
		),
	);
}
