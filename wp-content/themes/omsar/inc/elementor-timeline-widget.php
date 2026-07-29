<?php
/**
 * Elementor Custom Timeline Widget
 * 
 * Custom widget for displaying customizable timelines with configurable items, colors, sizes, and layouts
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
 * Register Custom Timeline Widget
 */
function omsar_register_timeline_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/timeline-widget.php';
	$widgets_manager->register( new \OMSAR_Timeline_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_timeline_widget' );

/**
 * Enqueue Timeline widget styles
 */
function omsar_enqueue_timeline_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/timeline-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-timeline-widget',
			get_template_directory_uri() . '/assets/css/timeline-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_enqueue_timeline_widget_styles' );
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_timeline_widget_styles' );

/**
 * Enqueue Timeline widget styles for Elementor editor
 */
function omsar_enqueue_timeline_widget_editor_styles() {
	$css_file = get_template_directory() . '/assets/css/timeline-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-timeline-widget',
			get_template_directory_uri() . '/assets/css/timeline-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/editor/after_enqueue_styles', 'omsar_enqueue_timeline_widget_editor_styles' );

