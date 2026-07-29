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
$table_name = 'news_data';

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
$news_items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY NewsID ASC");

if (!$news_items) {
    echo "<h2>No data found in the news_data table.</h2>";
    exit;
}

$total = count($news_items);
echo "<h2>Starting import of $total rows...</h2><hr>";

$counter = 0;

foreach ($news_items as $item) {
    $counter++;

    // Skip empty titles
    if (empty($item->NewsTitle)) {
        echo "Row $counter skipped: empty title.<br>";
        continue;
    }

    // Prevent duplicates
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'news_data_id' AND meta_value = %d",
        (int)$item->NewsID
    ));

    if ($existing) {
        echo "Row $counter skipped (already imported, Post ID: $existing)<br>";
        continue;
    }

    // Parse date
    $timestamp = isset($item->NewsReleaseDate) ? strtotime($item->NewsReleaseDate) : false;
    $post_date = $timestamp ? date('Y-m-d H:i:s', $timestamp) : current_time('mysql');

    // Insert post
    $post_data = array(
        'post_title'   => clean_value($item->NewsTitle),
        'post_content' => isset($item->NewsText) ? clean_value($item->NewsText) : '',
        'post_excerpt' => isset($item->NewsSummary) ? clean_value($item->NewsSummary) : '',
        'post_status'  => 'publish',
        'post_date'    => $post_date,
        'post_type'    => 'post',
    );

    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        echo "Row $counter failed: " . $post_id->get_error_message() . "<br>";
        continue;
    }

    // Update meta safely
    update_post_meta($post_id, 'subtitle', isset($item->NewsSubTitle) ? clean_value($item->NewsSubTitle) : '');
    update_post_meta($post_id, 'hide', isset($item->NewsHideOnHomePage) ? clean_value($item->NewsHideOnHomePage) : '');
    update_post_meta($post_id, 'post_type_option', 'news');
    update_post_meta($post_id, 'news_data_id', (int)$item->NewsID);
    update_post_meta($post_id, 'teaser', isset($item->NewsTeaser) ? clean_value($item->NewsTeaser) : '');
    update_post_meta($post_id, 'attachExtension', isset($item->AttachmentExtension) ? clean_value($item->AttachmentExtension) : '');
    update_post_meta($post_id, 'attachName', isset($item->AttachmentName) ? clean_value($item->AttachmentName) : '');

    // Polylang language
    if (function_exists('pll_set_post_language') && !empty($item->DocumentCulture)) {
        $pll_lang = $lang_map[$item->DocumentCulture] ?? null;
        if ($pll_lang) {
            pll_set_post_language($post_id, $pll_lang);
        }
    }

    // ---------------------------------------
    // SMART Kentico teaser file import (image / non-image)
    // ---------------------------------------
    if (!empty($item->NewsTeaser)) {

        $guid = strtolower(trim($item->NewsTeaser));
        $subfolder = substr($guid, 0, 2);
        $base_path = get_template_directory() . "/files_images/files/$subfolder/";

        // Find file by GUID with ANY extension
        $matches = glob($base_path . $guid . '.*');

        if (!empty($matches)) {

            $source_file = $matches[0];

            // Use attachName as filename, fallback to GUID if empty
            $filename = !empty($item->AttachmentName) ? sanitize_file_name($item->AttachmentName) : basename($source_file);

            // If attachName has no extension, add the original extension
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
                echo "Row $counter: Featured teaser imported ($filename)<br>";
            } else {
                // Non-image → save as custom fields
                update_post_meta($post_id, 'new_attachment_file', $attach_id);
                update_post_meta($post_id, 'new_attachment_url', wp_get_attachment_url($attach_id));
                echo "Row $counter: Non-image teaser saved as custom field ($filename)<br>";
            }

        } else {
            echo "Row $counter: Kentico file not found → $base_path{$guid}.*<br>";
        }
    }

    echo "Row $counter/$total imported (Post ID: $post_id)<br>";
}

echo "<hr><h2>Import finished successfully.</h2>";
