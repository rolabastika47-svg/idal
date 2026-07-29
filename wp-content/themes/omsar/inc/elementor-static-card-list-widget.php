<?php
/**
 * Elementor Static Card List Widget
 * 
 * Custom widget for displaying static card lists with two style options
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
 * Register Static Card List Widget
 */
function omsar_register_static_card_list_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/static-card-list-widget.php';
	$widgets_manager->register( new \OMSAR_Static_Card_List_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_static_card_list_widget' );

/**
 * Enqueue Static Card List widget styles
 */
function omsar_static_card_list_widget_styles() {
	$css_file = get_template_directory() . '/assets/css/static-card-list-widget.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'omsar-static-card-list-widget',
			get_template_directory_uri() . '/assets/css/static-card-list-widget.css',
			[ 'elementor-frontend' ],
			file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
		);
	}
}
add_action( 'elementor/frontend/after_enqueue_styles', 'omsar_static_card_list_widget_styles' );
