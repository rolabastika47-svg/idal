<?php
/**
 * Custom Post Types and Taxonomies
 * 
 * This file contains starter code for registering custom post types
 * and taxonomies. Add your custom post types and taxonomies here.
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Custom Post Types
 * 
 * Add your custom post types below using register_post_type()
 * 
 * Example:
 * 
 * register_post_type( 'your_post_type', array(
 *     'labels'             => array(
 *         'name'               => _x( 'Your Post Type', 'post type general name', 'omsar' ),
 *         'singular_name'      => _x( 'Your Post Type', 'post type singular name', 'omsar' ),
 *         'menu_name'          => _x( 'Your Post Types', 'admin menu', 'omsar' ),
 *         'name_admin_bar'     => _x( 'Your Post Type', 'add new on admin bar', 'omsar' ),
 *         'add_new'            => _x( 'Add New', 'your_post_type', 'omsar' ),
 *         'add_new_item'       => __( 'Add New Your Post Type', 'omsar' ),
 *         'new_item'           => __( 'New Your Post Type', 'omsar' ),
 *         'edit_item'          => __( 'Edit Your Post Type', 'omsar' ),
 *         'view_item'          => __( 'View Your Post Type', 'omsar' ),
 *         'all_items'          => __( 'All Your Post Types', 'omsar' ),
 *         'search_items'       => __( 'Search Your Post Types', 'omsar' ),
 *         'parent_item_colon'  => __( 'Parent Your Post Types:', 'omsar' ),
 *         'not_found'          => __( 'No your post types found.', 'omsar' ),
 *         'not_found_in_trash' => __( 'No your post types found in Trash.', 'omsar' ),
 *     ),
 *     'description'        => __( 'Description of your post type.', 'omsar' ),
 *     'public'             => true,
 *     'publicly_queryable' => true,
 *     'show_ui'            => true,
 *     'show_in_menu'       => true,
 *     'query_var'          => true,
 *     'rewrite'            => array( 'slug' => 'your-post-type' ),
 *     'capability_type'    => 'post',
 *     'has_archive'        => true,
 *     'hierarchical'       => false,
 *     'menu_position'      => null,
 *     'menu_icon'          => 'dashicons-admin-post',
 *     'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
 *     'show_in_rest'       => true, // Enable Gutenberg editor
 * ) );
 */
add_action( 'init', 'omsar_register_custom_post_types', 0 );

function omsar_register_custom_post_types() {
	// Add your custom post types here
	
	// Example: Publications Post Type
	// Uncomment and modify as needed
	/*
	register_post_type( 'publications', array(
		'labels'             => array(
			'name'               => _x( 'Publications', 'post type general name', 'omsar' ),
			'singular_name'      => _x( 'Publication', 'post type singular name', 'omsar' ),
			'menu_name'          => _x( 'Publications', 'admin menu', 'omsar' ),
			'name_admin_bar'     => _x( 'Publication', 'add new on admin bar', 'omsar' ),
			'add_new'            => _x( 'Add New', 'publication', 'omsar' ),
			'add_new_item'       => __( 'Add New Publication', 'omsar' ),
			'new_item'           => __( 'New Publication', 'omsar' ),
			'edit_item'          => __( 'Edit Publication', 'omsar' ),
			'view_item'          => __( 'View Publication', 'omsar' ),
			'all_items'          => __( 'All Publications', 'omsar' ),
			'search_items'       => __( 'Search Publications', 'omsar' ),
			'parent_item_colon'  => __( 'Parent Publications:', 'omsar' ),
			'not_found'          => __( 'No publications found.', 'omsar' ),
			'not_found_in_trash' => __( 'No publications found in Trash.', 'omsar' ),
		),
		'description'        => __( 'Publications post type for managing publications.', 'omsar' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'publications' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-book-alt',
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		'show_in_rest'       => true,
	) );
	*/
}

/**
 * Register Custom Taxonomies
 * 
 * Add your custom taxonomies below using register_taxonomy()
 * 
 * Example:
 * 
 * register_taxonomy( 'your_taxonomy', array( 'your_post_type' ), array(
 *     'labels'            => array(
 *         'name'              => _x( 'Your Taxonomies', 'taxonomy general name', 'omsar' ),
 *         'singular_name'     => _x( 'Your Taxonomy', 'taxonomy singular name', 'omsar' ),
 *         'search_items'      => __( 'Search Your Taxonomies', 'omsar' ),
 *         'all_items'         => __( 'All Your Taxonomies', 'omsar' ),
 *         'parent_item'       => __( 'Parent Your Taxonomy', 'omsar' ),
 *         'parent_item_colon' => __( 'Parent Your Taxonomy:', 'omsar' ),
 *         'edit_item'         => __( 'Edit Your Taxonomy', 'omsar' ),
 *         'update_item'       => __( 'Update Your Taxonomy', 'omsar' ),
 *         'add_new_item'      => __( 'Add New Your Taxonomy', 'omsar' ),
 *         'new_item_name'     => __( 'New Your Taxonomy Name', 'omsar' ),
 *         'menu_name'         => __( 'Your Taxonomy', 'omsar' ),
 *     ),
 *     'hierarchical'      => true,
 *     'show_ui'          => true,
 *     'show_admin_column' => true,
 *     'query_var'         => true,
 *     'rewrite'           => array( 'slug' => 'your-taxonomy' ),
 *     'show_in_rest'      => true,
 * ) );
 */
add_action( 'init', 'omsar_register_custom_taxonomies', 0 );

function omsar_register_custom_taxonomies() {
	// Add your custom taxonomies here
	
	// Example: Publication Category Taxonomy
	// Uncomment and modify as needed
	/*
	register_taxonomy( 'publication_category', array( 'publications' ), array(
		'labels'            => array(
			'name'              => _x( 'Publication Categories', 'taxonomy general name', 'omsar' ),
			'singular_name'     => _x( 'Publication Category', 'taxonomy singular name', 'omsar' ),
			'search_items'      => __( 'Search Publication Categories', 'omsar' ),
			'all_items'         => __( 'All Publication Categories', 'omsar' ),
			'parent_item'       => __( 'Parent Publication Category', 'omsar' ),
			'parent_item_colon' => __( 'Parent Publication Category:', 'omsar' ),
			'edit_item'         => __( 'Edit Publication Category', 'omsar' ),
			'update_item'       => __( 'Update Publication Category', 'omsar' ),
			'add_new_item'      => __( 'Add New Publication Category', 'omsar' ),
			'new_item_name'     => __( 'New Publication Category Name', 'omsar' ),
			'menu_name'         => __( 'Categories', 'omsar' ),
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'publication-category' ),
		'show_in_rest'      => true,
	) );
	*/
}


function cptui_register_my_cpts() {

	/**
	 * Post Type: Testimonials.
	 */

	$labels = [
		"name" => esc_html__( "Testimonials", "omsar" ),
		"singular_name" => esc_html__( "Testimonial", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Testimonials", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "testimonials", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-format-quote",
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type( "testimonials", $args );

	/**
	 * Post Type: Projects.
	 */

	$labels = [
		"name" => esc_html__( "Projects", "omsar" ),
		"singular_name" => esc_html__( "Project", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Projects", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "projects", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-portfolio",
		"supports" => [ "title", "editor", "thumbnail", "excerpt" ],
		"show_in_graphql" => false,
	];

	register_post_type( "projects", $args );

	/**
	 * Post Type: Ministers.
	 */

	$labels = [
		"name" => esc_html__( "Ministers", "omsar" ),
		"singular_name" => esc_html__( "Minister", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Ministers", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "former_ministers", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-bank",
		"supports" => [ "title", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "former_ministers", $args );

	/**
	 * Post Type: Publications.
	 */

	$labels = [
		"name" => esc_html__( "Publications", "omsar" ),
		"singular_name" => esc_html__( "Publication", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Publications", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "publication", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-book",
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "publication", $args );

	/**
	 * Post Type: Contact Us Submissions.
	 */

	$labels = [
		"name" => esc_html__( "Contact Us Submissions", "omsar" ),
		"singular_name" => esc_html__( "Contact Us Submission", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Contact Us Submissions", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "contact_us_forms", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-email-alt",
		"supports" => [ "title", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "contact_us_forms", $args );

	/**
	 * Post Type: Partnerships.
	 */

	$labels = [
		"name" => esc_html__( "Partnerships", "omsar" ),
		"singular_name" => esc_html__( "Partnership", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Partnerships", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "partnerships", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-groups",
		"supports" => [ "title", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "partnerships", $args );

	/**
	 * Post Type: Recruitments.
	 */

	$labels = [
		"name" => esc_html__( "Recruitments", "omsar" ),
		"singular_name" => esc_html__( "Recruitment", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Recruitments", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "recruitments", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-media-document",
		"supports" => [ "title", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "recruitments", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_cpts_testimonials() {

	/**
	 * Post Type: Testimonials.
	 */

	$labels = [
		"name" => esc_html__( "Testimonials", "omsar" ),
		"singular_name" => esc_html__( "Testimonial", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Testimonials", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "testimonials", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-format-quote",
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type( "testimonials", $args );
}

add_action( 'init', 'cptui_register_my_cpts_testimonials' );


function cptui_register_my_cpts_projects() {

	/**
	 * Post Type: Projects.
	 */

	$labels = [
		"name" => esc_html__( "Projects", "omsar" ),
		"singular_name" => esc_html__( "Project", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Projects", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "projects", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-portfolio",
		"supports" => [ "title", "editor", "thumbnail", "excerpt" ],
		"show_in_graphql" => false,
	];

	register_post_type( "projects", $args );
}

add_action( 'init', 'cptui_register_my_cpts_projects' );



function cptui_register_my_cpts_former_ministers() {

	/**
	 * Post Type: Ministers.
	 */

	$labels = [
		"name" => esc_html__( "Ministers", "omsar" ),
		"singular_name" => esc_html__( "Minister", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Ministers", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "former_ministers", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-bank",
		"supports" => [ "title", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "former_ministers", $args );
}

add_action( 'init', 'cptui_register_my_cpts_former_ministers' );



function cptui_register_my_cpts_publication() {

	/**
	 * Post Type: Publications.
	 */

	$labels = [
		"name" => esc_html__( "Publications", "omsar" ),
		"singular_name" => esc_html__( "Publication", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Publications", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "publication", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-book",
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "publication", $args );
}

add_action( 'init', 'cptui_register_my_cpts_publication' );



function cptui_register_my_cpts_contact_us_forms() {

	/**
	 * Post Type: Contact Us Submissions.
	 */

	$labels = [
		"name" => esc_html__( "Contact Us Submissions", "omsar" ),
		"singular_name" => esc_html__( "Contact Us Submission", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Contact Us Submissions", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "contact_us_forms", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-email-alt",
		"supports" => [ "title", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "contact_us_forms", $args );
}

add_action( 'init', 'cptui_register_my_cpts_contact_us_forms' );



function cptui_register_my_cpts_partnerships() {

	/**
	 * Post Type: Partnerships.
	 */

	$labels = [
		"name" => esc_html__( "Partnerships", "omsar" ),
		"singular_name" => esc_html__( "Partnership", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Partnerships", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "partnerships", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-groups",
		"supports" => [ "title", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "partnerships", $args );
}

add_action( 'init', 'cptui_register_my_cpts_partnerships' );



function cptui_register_my_cpts_recruitments() {

	/**
	 * Post Type: Recruitments.
	 */

	$labels = [
		"name" => esc_html__( "Recruitments", "omsar" ),
		"singular_name" => esc_html__( "Recruitment", "omsar" ),
	];

	$args = [
		"label" => esc_html__( "Recruitments", "omsar" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "recruitments", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-media-document",
		"supports" => [ "title", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "recruitments", $args );
}

add_action( 'init', 'cptui_register_my_cpts_recruitments' );



function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Pillars.
	 */

	$labels = [
		"name" => esc_html__( "Pillars", "omsar" ),
		"singular_name" => esc_html__( "Pillars", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Pillars", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'pillars', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "pillars",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "pillars", [ "projects" ], $args );

	/**
	 * Taxonomy: Post Types.
	 */

	$labels = [
		"name" => esc_html__( "Post Types", "omsar" ),
		"singular_name" => esc_html__( "Post Type", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Post Types", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'posts_type', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "posts_type",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "posts_type", [ "post" ], $args );

	/**
	 * Taxonomy: Project Statuses.
	 */

	$labels = [
		"name" => esc_html__( "Project Statuses", "omsar" ),
		"singular_name" => esc_html__( "Project Status", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Project Statuses", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		// Keep UI available for code use but hide from the admin menu.
		"show_in_menu" => false,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'project_status', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "project_status",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "project_status", [ "projects" ], $args );

	/**
	 * Taxonomy: Publication Categories.
	 */

	$labels = [
		"name" => esc_html__( "Publication Categories", "omsar" ),
		"singular_name" => esc_html__( "Publication Category", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Publication Categories", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'publication_category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "publication_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "publication_category", [ "publication" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );



function cptui_register_my_taxes_pillars() {

	/**
	 * Taxonomy: Pillars.
	 */

	$labels = [
		"name" => esc_html__( "Pillars", "omsar" ),
		"singular_name" => esc_html__( "Pillars", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Pillars", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'pillars', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "pillars",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "pillars", [ "projects" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_pillars' );


function cptui_register_my_taxes_posts_type() {

	/**
	 * Taxonomy: Post Types.
	 */

	$labels = [
		"name" => esc_html__( "Post Types", "omsar" ),
		"singular_name" => esc_html__( "Post Type", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Post Types", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'posts_type', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "posts_type",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "posts_type", [ "post" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_posts_type' );


function cptui_register_my_taxes_project_status() {

	/**
	 * Taxonomy: Project Statuses.
	 */

	$labels = [
		"name" => esc_html__( "Project Statuses", "omsar" ),
		"singular_name" => esc_html__( "Project Status", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Project Statuses", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'project_status', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "project_status",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "project_status", [ "projects" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_project_status' );



function cptui_register_my_taxes_publication_category() {

	/**
	 * Taxonomy: Publication Categories.
	 */

	$labels = [
		"name" => esc_html__( "Publication Categories", "omsar" ),
		"singular_name" => esc_html__( "Publication Category", "omsar" ),
	];

	
	$args = [
		"label" => esc_html__( "Publication Categories", "omsar" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'publication_category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => false,
		"show_tagcloud" => false,
		"rest_base" => "publication_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "publication_category", [ "publication" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_publication_category' );


/**
 * Flush rewrite rules on theme activation
 * This ensures custom post types and taxonomies work correctly
 * 
 * Note: The actual CPT registrations are handled by CPTUI functions
 * (cptui_register_my_cpts and cptui_register_my_taxes) which are
 * already hooked to 'init'. This function just flushes rewrite rules.
 */
add_action( 'after_switch_theme', 'omsar_flush_rewrite_rules' );

function omsar_flush_rewrite_rules() {
	// Flush rewrite rules to ensure custom post types and taxonomies work correctly
	flush_rewrite_rules();
}
