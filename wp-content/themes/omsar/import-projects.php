<?php
// Load WordPress environment
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    wp_die('You do not have permission to run this script.');
}

// Unlimited execution
set_time_limit(0);
ob_implicit_flush(true);
ob_end_flush();

global $wpdb;
$table_name = 'projects_data';

// Kentico -> Polylang mapping
$lang_map = array(
    'ar-LB' => 'ar',
    'en-US' => 'en_AU',
);

// Helper function to clean NULL / dirty values
function clean_value($value) {
    if ($value === null || strtoupper(trim($value)) === 'NULL') {
        return '';
    }
    return trim($value);
}

// Fetch data
$projects_items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");

if (!$projects_items) {
    echo "<h2>No data found in the projects_data table.</h2>";
    exit;
}

$total = count($projects_items);
echo "<h2>Starting import of $total rows...</h2><hr>";

$counter = 0;

foreach ($projects_items as $item) {
    $counter++;

    // Skip empty titles
    if (empty($item->title)) {
        echo "Row $counter skipped: empty title.<br>";
        continue;
    }

    // Prevent duplicates
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'projects_data_id' AND meta_value = %d",
        (int)$item->id
    ));

    if ($existing) {
        echo "Row $counter skipped (already imported, Post ID: $existing)<br>";
        continue;
    }

    // Insert post
    $post_data = array(
        'post_title'   => clean_value($item->title),
        'post_content' => isset($item->details) ? clean_value($item->details) : '',
        'post_status'  => 'publish',
        'post_type'    => 'projects',
    );

    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        echo "Row $counter failed: " . $post_id->get_error_message() . "<br>";
        continue;
    }

    // Update meta safely
    update_post_meta($post_id, 'programStatus', isset($item->status) ? clean_value($item->status) : '');
    update_post_meta($post_id, 'cost', isset($item->cost) ? clean_value($item->cost) : '');
    update_post_meta($post_id, 'source_of_fund', isset($item->sourceOfFund) ? clean_value($item->sourceOfFund) : '');
    update_post_meta($post_id, 'scope', isset($item->scope) ? clean_value($item->scope) : '');
    update_post_meta($post_id, 'specific_objectives', isset($item->specificObjectives) ? clean_value($item->specificObjectives) : '');
    update_post_meta($post_id, 'results_to_be_achieved', isset($item->results) ? clean_value($item->results) : '');
    update_post_meta($post_id, 'sector', isset($item->sector) ? clean_value($item->sector) : '');
    update_post_meta($post_id, 'executing_agency', isset($item->agency) ? clean_value($item->agency) : '');
    update_post_meta($post_id, 'regions', isset($item->regions) ? clean_value($item->regions) : '');
    update_post_meta($post_id, 'image', isset($item->image) ? clean_value($item->image) : '');
    update_post_meta($post_id, 'project_date', isset($item->date) ? clean_value($item->date) : '');
    update_post_meta($post_id, 'directorate', isset($item->directorate) ? clean_value($item->directorate) : '');
    update_post_meta($post_id, 'projects_data_id', (int)$item->id);
    update_post_meta($post_id, 'documentCulture', isset($item->culture) ? clean_value($item->culture) : '');
    update_post_meta($post_id, 'attachmentExtension', isset($item->extension) ? clean_value($item->extension) : '');
    update_post_meta($post_id, 'attachmentName', isset($item->name) ? clean_value($item->name) : '');

    // Polylang language
    if (function_exists('pll_set_post_language') && !empty($item->culture)) {
        $pll_lang = $lang_map[$item->culture] ?? null;
        if ($pll_lang) {
            pll_set_post_language($post_id, $pll_lang);
        }
    }

    // ---------------------------------------
    // SMART Kentico file import (image / non-image)
    // ---------------------------------------
    if (!empty($item->image)) {

        $guid = strtolower(trim($item->image));
        $subfolder = substr($guid, 0, 2);
        $base_path = get_template_directory() . "/files_images/files/$subfolder/";

        // Find by GUID with any extension
        $matches = glob($base_path . $guid . '.*');

        if (!empty($matches)) {

            $source_file = $matches[0];

            // Use attachmentName as filename, fallback to GUID if empty
            $filename = !empty($item->name) ? sanitize_file_name($item->name) : basename($source_file);

            // If attachmentName has no extension, add the original extension
            $ext = pathinfo($source_file, PATHINFO_EXTENSION);
            if (!pathinfo($filename, PATHINFO_EXTENSION)) {
                $filename .= '.' . $ext;
            }

            $upload_dir = wp_upload_dir();
            $destination = $upload_dir['path'] . '/' . $filename;

            if (!file_exists($destination)) {
                copy($source_file, $destination);
            }

            $filetype = wp_check_filetype($destination, null);
            $mime = $filetype['type'];

            $attachment = array(
                'post_mime_type' => $mime,
                'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            $attach_id = wp_insert_attachment($attachment, $destination, $post_id);

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $destination);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // IMAGE vs NON-IMAGE
            if (strpos($mime, 'image/') === 0) {
                // Featured image
                set_post_thumbnail($post_id, $attach_id);
                echo "Row $counter: Featured image imported ($filename)<br>";
            } else {
                // Non-image → save as custom field
                update_post_meta($post_id, 'project_attachment_file', $attach_id);
                update_post_meta($post_id, 'project_attachment_url', wp_get_attachment_url($attach_id));
                echo "Row $counter: Non-image file saved as custom field ($filename)<br>";
            }

        } else {
            echo "Row $counter: Program file not found → $base_path{$guid}.*<br>";
        }
    }

    echo "Row $counter/$total imported (Post ID: $post_id)<br>";
}

echo "<hr><h2>Programs import finished successfully.</h2>";
