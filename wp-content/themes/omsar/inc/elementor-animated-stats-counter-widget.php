<?php
/**
 * Elementor OMSAR Animated Stats Counter Widget
 * 
 * Custom widget for displaying animated statistics counters with icons, numbers, and labels
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
 * Register OMSAR Animated Stats Counter Widget
 */
function omsar_register_animated_stats_counter_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/animated-stats-counter-widget.php';
	$widgets_manager->register( new \OMSAR_Animated_Stats_Counter_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_animated_stats_counter_widget' );

/**
 * Enqueue Animated Stats Counter widget styles and scripts
 */
function omsar_enqueue_animated_stats_counter_widget_assets() {
	$css_file = get_template_directory() . '/assets/css/animated-stats-counter-widget.css';
	$js_file = get_template_directory() . '/assets/js/animated-stats-counter-widget.js';
	
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-animated-stats-counter-widget',
			get_template_directory_uri() . '/assets/css/animated-stats-counter-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
	
	if ( file_exists( $js_file ) ) {
		wp_enqueue_script(
			'omsar-animated-stats-counter-widget',
			get_template_directory_uri() . '/assets/js/animated-stats-counter-widget.js',
			[ 'jquery' ],
			file_exists( $js_file ) ? filemtime( $js_file ) : '1.0.0',
			true
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_enqueue_animated_stats_counter_widget_assets' );
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_animated_stats_counter_widget_assets' );

/**
 * Enqueue Animated Stats Counter widget assets for Elementor editor
 */
function omsar_enqueue_animated_stats_counter_widget_editor_assets() {
	$css_file = get_template_directory() . '/assets/css/animated-stats-counter-widget.css';
	
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-animated-stats-counter-widget',
			get_template_directory_uri() . '/assets/css/animated-stats-counter-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/editor/after_enqueue_styles', 'omsar_enqueue_animated_stats_counter_widget_editor_assets' );

