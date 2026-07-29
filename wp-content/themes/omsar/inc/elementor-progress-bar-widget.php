<?php
/**
 * Elementor OMSAR Progress Bar Widget
 * 
 * Custom widget for displaying progress bars in horizontal or circular format
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
 * Register OMSAR Progress Bar Widget
 */
function omsar_register_progress_bar_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/progress-bar-widget.php';
	$widgets_manager->register( new \OMSAR_Progress_Bar_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_progress_bar_widget' );

/**
 * Enqueue Progress Bar widget styles
 */
function omsar_enqueue_progress_bar_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/progress-bar-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-progress-bar-widget',
			get_template_directory_uri() . '/assets/css/progress-bar-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_enqueue_progress_bar_widget_styles' );
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_progress_bar_widget_styles' );

/**
 * Enqueue Progress Bar widget styles for Elementor editor
 */
function omsar_enqueue_progress_bar_widget_editor_styles() {
	$css_file = get_template_directory() . '/assets/css/progress-bar-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-progress-bar-widget',
			get_template_directory_uri() . '/assets/css/progress-bar-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/editor/after_enqueue_styles', 'omsar_enqueue_progress_bar_widget_editor_styles' );

