<?php
/**
 * Elementor Statistics Card Widget
 * 
 * Custom widget for displaying statistics cards with number, unit, title, background color, and icon
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if Elementor is installed and active
 */
if ( ! did_action( 'elementor/loaded' ) ) {
	return;
}

/**
 * Register OMSAR Elements Category
 */
function omsar_add_elementor_widget_categories( $elements_manager ) {
	$elements_manager->add_category(
		'omsar-elements',
		[
			'title' => esc_html__( 'OMSAR Elements', 'omsar' ),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'omsar_add_elementor_widget_categories' );

/**
 * Register Statistics Card Widget
 */
function omsar_register_stat_card_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/stat-card-widget.php';
	$widgets_manager->register( new \OMSAR_Stat_Card_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_stat_card_widget' );

/**
 * Register Posts by Taxonomy Widget
 */
function omsar_register_posts_by_taxonomy_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/posts-by-taxonomy-widget.php';
	$widgets_manager->register( new \OMSAR_Posts_By_Taxonomy_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_posts_by_taxonomy_widget' );

/**
 * Register Projects Widget
 */
function omsar_register_projects_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/projects-widget.php';
	$widgets_manager->register( new \OMSAR_Projects_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_projects_widget' );

/**
 * Register Recruitments Widget
 */
function omsar_register_recruitments_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/recruitments-widget.php';
	$widgets_manager->register( new \OMSAR_Recruitments_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_recruitments_widget' );

/**
 * Register Procurement Notices Widget
 */
function omsar_register_procurement_notices_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/procurement-notices-widget.php';
	$widgets_manager->register( new \OMSAR_Procurement_Notices_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_procurement_notices_widget' );

/**
 * Register Knowledge and Resources Widget
 */
function omsar_register_knowledge_and_resources_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/knowledge-and-resources-widget.php';
	$widgets_manager->register( new \OMSAR_Knowledge_And_Resources_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_knowledge_and_resources_widget' );

/**
 * Register Call to Action Widget
 */
function omsar_register_call_to_action_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/call-to-action-widget.php';
	$widgets_manager->register( new \OMSAR_Call_To_Action_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_call_to_action_widget' );

/**
 * Register Page Title Widget
 */
function omsar_register_page_title_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/page-title-widget.php';
	$widgets_manager->register( new \OMSAR_Page_Title_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_page_title_widget' );

/**
 * Enqueue widget styles
 * Load after Elementor styles to allow overrides
 */
function omsar_enqueue_stat_card_styles() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	
	$stat_card_css = get_template_directory() . '/assets/css/elementor-style.css';
	if ( file_exists( $stat_card_css ) ) {
		// Load after Elementor frontend styles to allow overrides
		wp_enqueue_style(
			'omsar-stat-card-css',
			get_template_directory_uri() . '/assets/css/elementor-style.css',
			[ 'elementor-frontend' ], // Dependency on Elementor styles
			filemtime( $stat_card_css )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_stat_card_styles', 20 ); // Higher priority to load after Elementor

