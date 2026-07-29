<?php
/**
 * Export ACF Options Page Values
 * 
 * Run this file on your SOURCE site to export the theme options values.
 * 
 * Usage:
 * 1. Place this file in your theme root directory
 * 2. Access it via browser: http://yoursite.com/wp-content/themes/omsar/export-options-values.php
 * 3. Download the generated JSON file
 * 4. Replace the acf-options-page.json file in demo-content folder with the downloaded file
 * 
 * IMPORTANT: Delete this file after exporting for security!
 */

// Load WordPress
require_once( '../../../wp-load.php' );

// Security check - you may want to add authentication here
// For now, this is a simple check - you should delete this file after use
if ( ! current_user_can( 'manage_options' ) ) {
	die( 'Access denied. This script should only be run by administrators and deleted after use.' );
}

// Only proceed if ACF is active
if ( ! function_exists( 'get_field' ) || ! function_exists( 'acf_get_field_groups' ) ) {
	die( 'ACF is not active. This script requires Advanced Custom Fields plugin.' );
}

// Get field groups assigned to the theme-options page
$field_groups = acf_get_field_groups( array(
	'options_page' => 'theme-options',
) );

if ( empty( $field_groups ) ) {
	die( 'No field groups found for theme-options page.' );
}

// Get all fields from the field groups
$all_fields = array();
foreach ( $field_groups as $field_group ) {
	$fields = acf_get_fields( $field_group['ID'] );
	if ( $fields ) {
		$all_fields = array_merge( $all_fields, $fields );
	}
}

// Function to recursively get all field names (including sub fields)
function omsar_get_all_field_names( $fields, $prefix = '' ) {
	$field_names = array();
	
	foreach ( $fields as $field ) {
		// Skip tabs and other non-value fields
		if ( in_array( $field['type'], array( 'tab', 'accordion', 'message' ) ) ) {
			continue;
		}
		
		// Add field name if it has a name
		if ( ! empty( $field['name'] ) ) {
			$field_names[] = $field['name'];
		}
		
		// Recursively get sub fields (for repeater, flexible content, etc.)
		if ( ! empty( $field['sub_fields'] ) ) {
			$sub_names = omsar_get_all_field_names( $field['sub_fields'], $field['name'] . '_' );
			$field_names = array_merge( $field_names, $sub_names );
		}
	}
	
	return $field_names;
}

// Get all field names
$field_names = omsar_get_all_field_names( $all_fields );

// Export field values
$export_data = array();

foreach ( $field_names as $field_name ) {
	$value = get_field( $field_name, 'option' );
	
	// Only include fields that have values (skip empty/null values)
	if ( $value !== null && $value !== '' && $value !== false ) {
		$export_data[ $field_name ] = $value;
	}
}

// Output as JSON
header( 'Content-Type: application/json' );
header( 'Content-Disposition: attachment; filename="acf-options-page.json"' );
echo json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
exit;
