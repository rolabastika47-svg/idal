<?php
/**
 * Elementor Chart Widgets Registration
 * 
 * Registers all chart-related Elementor widgets (Bar Chart, Circular Chart, Line Chart)
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
 * Register Chart Widget
 */
function omsar_register_chart_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/chart-widget.php';
	$widgets_manager->register( new \OMSAR_Chart_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_chart_widget' );

/**
 * Register Circular Chart Widget
 */
function omsar_register_circular_chart_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/circular-chart-widget.php';
	$widgets_manager->register( new \OMSAR_Circular_Chart_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_circular_chart_widget' );

/**
 * Register Line Chart Widget
 */
function omsar_register_line_chart_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/line-chart-widget.php';
	$widgets_manager->register( new \OMSAR_Line_Chart_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_line_chart_widget' );

/**
 * Enqueue Chart.js library for chart widgets
 */
function omsar_enqueue_chart_js() {
	// Enqueue Chart.js from CDN
	wp_enqueue_script(
		'chart-js',
		'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js',
		[],
		'4.4.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_chart_js', 5 );
add_action( 'elementor/frontend/before_enqueue_scripts', 'omsar_enqueue_chart_js' );

