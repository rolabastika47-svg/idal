<?php
/**
 * Elementor Custom Carousel Media Widget
 * 
 * Custom widget for displaying customizable carousel with images, text content, navigation arrows, and pagination bullets
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
 * Register Custom Carousel Media Widget
 */
function omsar_register_carousel_media_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/carousel-media-widget.php';
	$widgets_manager->register( new \OMSAR_Carousel_Media_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_carousel_media_widget' );

/**
 * Enqueue Carousel Media widget styles
 */
function omsar_enqueue_carousel_media_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/carousel-media-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-carousel-media-widget',
			get_template_directory_uri() . '/assets/css/carousel-media-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_enqueue_carousel_media_widget_styles' );
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_carousel_media_widget_styles' );

/**
 * Enqueue Carousel Media widget styles for Elementor editor
 */
function omsar_enqueue_carousel_media_widget_editor_styles() {
	$css_file = get_template_directory() . '/assets/css/carousel-media-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-carousel-media-widget',
			get_template_directory_uri() . '/assets/css/carousel-media-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/editor/after_enqueue_styles', 'omsar_enqueue_carousel_media_widget_editor_styles' );

