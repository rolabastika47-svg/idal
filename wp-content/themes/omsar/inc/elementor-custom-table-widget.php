<?php
/**
 * Elementor Custom Table Widget Registration
 * 
 * Registers the custom table Elementor widget
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
 * Register Custom Table Widget
 */
function omsar_register_custom_table_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/custom-table-widget.php';
	$widgets_manager->register( new \OMSAR_Data_Table_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_custom_table_widget' );

/**
 * Register Data Matrix Table Widget
 */
function omsar_register_data_matrix_table_widget( $widgets_manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets/data-matrix-table-widget.php';
	$widgets_manager->register( new \OMSAR_Data_Matrix_Table_Widget() );
}
add_action( 'elementor/widgets/register', 'omsar_register_data_matrix_table_widget' );

/**
 * Enqueue script to fix repeater delete button visibility in Elementor editor
 */
function omsar_enqueue_elementor_repeater_fix() {
	$script_path = get_template_directory() . '/assets/js/elementor-repeater-fix.js';
	if ( file_exists( $script_path ) ) {
		wp_enqueue_script(
			'omsar-elementor-repeater-fix',
			get_template_directory_uri() . '/assets/js/elementor-repeater-fix.js',
			[ 'jquery' ],
			filemtime( $script_path ),
			true
		);
	}
}
add_action( 'elementor/editor/before_enqueue_scripts', 'omsar_enqueue_elementor_repeater_fix' );

