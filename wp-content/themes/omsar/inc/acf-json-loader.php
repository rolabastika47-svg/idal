<?php
/**
 * ACF JSON Field Loader
 * 
 * This file handles automatic loading of ACF field definitions
 * from JSON files stored in the theme's inc/acf-json directory.
 * 
 * Place your ACF JSON field group files in:
 * wp-content/themes/omsar/inc/acf-json/
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set ACF JSON save point to theme directory
 * This allows saving ACF field groups to the theme folder
 */
add_filter( 'acf/settings/save_json', 'omsar_acf_json_save_point' );

function omsar_acf_json_save_point( $path ) {
	// Update path
	$path = get_stylesheet_directory() . '/inc/acf-json';
	
	// Create directory if it doesn't exist
	if ( ! file_exists( $path ) ) {
		wp_mkdir_p( $path );
	}
	
	// Return the path
	return $path;
}

/**
 * Set ACF JSON load point to theme directory
 * This loads ACF field groups from the theme folder
 */
add_filter( 'acf/settings/load_json', 'omsar_acf_json_load_point' );

function omsar_acf_json_load_point( $paths ) {
	// Remove original path (optional)
	unset( $paths[0] );
	
	// Append theme path
	$paths[] = get_stylesheet_directory() . '/inc/acf-json';
	
	return $paths;
}

/**
 * Automatically import ACF field groups from JSON files on theme activation
 * This ensures field groups are available immediately after theme activation
 */
add_action( 'after_switch_theme', 'omsar_import_acf_json_fields' );

function omsar_import_acf_json_fields() {
	// Check if ACF is active
	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		return;
	}
	
	$json_dir = get_stylesheet_directory() . '/inc/acf-json';
	
	// Check if directory exists
	if ( ! file_exists( $json_dir ) || ! is_dir( $json_dir ) ) {
		return;
	}
	
	// Get all JSON files in the directory
	$json_files = glob( $json_dir . '/*.json' );
	
	if ( empty( $json_files ) ) {
		return;
	}
	
	// Import each JSON file
	foreach ( $json_files as $json_file ) {
		// Read JSON file
		$json_data = file_get_contents( $json_file );
		
		if ( empty( $json_data ) ) {
			continue;
		}
		
		// Decode JSON
		$field_group = json_decode( $json_data, true );
		
		if ( empty( $field_group ) || ! is_array( $field_group ) ) {
			continue;
		}
		
		// Check if field group already exists
		$existing_groups = acf_get_field_groups();
		$group_key = isset( $field_group['key'] ) ? $field_group['key'] : '';
		
		$exists = false;
		foreach ( $existing_groups as $existing_group ) {
			if ( isset( $existing_group['key'] ) && $existing_group['key'] === $group_key ) {
				$exists = true;
				break;
			}
		}
		
		// Import field group if it doesn't exist
		if ( ! $exists && function_exists( 'acf_import_field_group' ) ) {
			acf_import_field_group( $field_group );
		}
	}
}

/**
 * Display admin notice if ACF JSON directory is empty
 * This helps users know where to place their ACF JSON files
 */
add_action( 'admin_notices', 'omsar_acf_json_directory_notice' );

function omsar_acf_json_directory_notice() {
	// Only show on theme options or ACF pages
	$screen = get_current_screen();
	if ( ! $screen || ( strpos( $screen->id, 'acf' ) === false && strpos( $screen->id, 'theme' ) === false ) ) {
		return;
	}
	
	// Check if ACF is active
	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		return;
	}
	
	$json_dir = get_stylesheet_directory() . '/inc/acf-json';
	
	// Check if directory exists and is empty
	if ( file_exists( $json_dir ) && is_dir( $json_dir ) ) {
		$json_files = glob( $json_dir . '/*.json' );
		
		if ( empty( $json_files ) ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<strong><?php esc_html_e( 'ACF JSON Fields', 'omsar' ); ?></strong><br>
					<?php esc_html_e( 'Place your ACF field group JSON files in:', 'omsar' ); ?>
					<code><?php echo esc_html( $json_dir ); ?></code>
				</p>
			</div>
			<?php
		}
	}
}
