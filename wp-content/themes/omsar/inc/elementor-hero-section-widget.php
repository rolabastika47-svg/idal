<?php
/**
 * Elementor Custom Hero Section Widget
 * 
 * Custom widget for displaying hero sections with background, title, description, and buttons
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
 * Register Custom Hero Section Widget
 */
function omsar_register_hero_section_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/hero-section-widget.php';
	$widgets_manager->register( new \OMSAR_Hero_Section_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_hero_section_widget' );

/**
 * Enqueue Hero Section widget styles
 */
function omsar_enqueue_hero_section_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/hero-section-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-hero-section-widget',
			get_template_directory_uri() . '/assets/css/hero-section-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_enqueue_hero_section_widget_styles' );
add_action( 'wp_enqueue_scripts', 'omsar_enqueue_hero_section_widget_styles' );

/**
 * Enqueue Hero Section widget styles for Elementor editor
 */
function omsar_enqueue_hero_section_widget_editor_styles() {
	$css_file = get_template_directory() . '/assets/css/hero-section-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-hero-section-widget',
			get_template_directory_uri() . '/assets/css/hero-section-widget.css',
			[],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/editor/after_enqueue_styles', 'omsar_enqueue_hero_section_widget_editor_styles' );

