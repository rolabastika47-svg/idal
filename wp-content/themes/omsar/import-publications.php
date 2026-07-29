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
$table_name = 'publications_data';

// Kentico -> Polylang mapping
$lang_map = array(
    'ar-LB' => 'ar',
    'en-US' => 'en_AU',
);

// Helper: clean values
function clean_value($value) {
    if ($value === null || strtoupper(trim($value)) === 'NULL') {
        return '';
    }
    return trim($value);
}

// Fetch data
$items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");

if (!$items) {
    echo "<h2>No data found in publications_data table.</h2>";
    exit;
}

$total = count($items);
echo "<h2>Starting import of $total rows...</h2><hr>";

$counter = 0;

foreach ($items as $item) {
    $counter++;

    if (empty($item->title)) {
        echo "Row $counter skipped: empty title.<br>";
        continue;
    }

    // Prevent duplicates
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'publication_data_id' AND meta_value = %d",
        (int)$item->id
    ));
    if ($existing) {
        echo "Row $counter skipped (already imported, Post ID: $existing)<br>";
        continue;
    }

    // -----------------------------
    // Insert post
    // -----------------------------
    $post_data = array(
        'post_title'   => clean_value($item->title),
        'post_content' => isset($item->details) ? clean_value($item->details) : '',
        'post_status'  => 'publish',
        'post_type'    => 'publication',
    );

    $post_id = wp_insert_post($post_data, true);
    if (is_wp_error($post_id)) {
        echo "Row $counter failed: " . $post_id->get_error_message() . "<br>";
        continue;
    }

    // -----------------------------
    // Meta fields
    // -----------------------------
    update_post_meta($post_id, 'pub_image', clean_value($item->image));
    update_post_meta($post_id, 'pub_file', clean_value($item->file));
    update_post_meta($post_id, 'pub_date', clean_value($item->date));
    update_post_meta($post_id, 'showNotification', clean_value($item->showNotification));
    update_post_meta($post_id, 'publication_data_id', (int)$item->id);
    update_post_meta($post_id, 'pub_Culture', clean_value($item->culture));

    // Separated attachment fields
    update_post_meta($post_id, 'pub_image_name', clean_value($item->image_name));
    update_post_meta($post_id, 'pub_image_extension', clean_value($item->image_extension));
    update_post_meta($post_id, 'pub_file_name', clean_value($item->file_name));
    update_post_meta($post_id, 'pub_file_extension', clean_value($item->file_extension));

    // -----------------------------
    // Polylang language
    // -----------------------------
    if (function_exists('pll_set_post_language') && !empty($item->culture)) {
        $pll_lang = $lang_map[$item->culture] ?? null;
        if ($pll_lang) {
            pll_set_post_language($post_id, $pll_lang);
        }
    }

    // =====================================================
    // FEATURED IMAGE (pub_image)
    // =====================================================
    if (!empty($item->image)) {
        $guid = strtolower(trim($item->image));
        $subfolder = substr($guid, 0, 2);
        $base_path = get_template_directory() . "/files_images/files/$subfolder/";
        $matches = glob($base_path . $guid . '.*');

        if (!empty($matches)) {
            $source_file = $matches[0];

            $filename = !empty($item->image_name)
                ? sanitize_file_name($item->image_name . '.' . $item->image_extension)
                : basename($source_file);

            $upload_dir = wp_upload_dir();
            $destination = $upload_dir['path'] . '/' . $filename;

            if (!file_exists($destination)) {
                copy($source_file, $destination);
            }

            $filetype = wp_check_filetype($destination, null);
            $mime = $filetype['type'];

            if (strpos($mime, 'image/') === 0) {
                $attachment = array(
                    'post_mime_type' => $mime,
                    'post_title'     => pathinfo($filename, PATHINFO_FILENAME),
                    'post_status'    => 'inherit',
                );

                $attach_id = wp_insert_attachment($attachment, $destination, $post_id);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $destination);
                wp_update_attachment_metadata($attach_id, $attach_data);

                set_post_thumbnail($post_id, $attach_id);
                echo "Row $counter: Featured image imported ($filename)<br>";
            }
        }
    }

    // =====================================================
    // DOCUMENT FILE (pub_file)
    // =====================================================
    if (!empty($item->file)) {
        $guid = strtolower(trim($item->file));
        $subfolder = substr($guid, 0, 2);
        $base_path = get_template_directory() . "/files_images/files/$subfolder/";
        $matches = glob($base_path . $guid . '.*');

        if (!empty($matches)) {
            $source_file = $matches[0];

            $filename = !empty($item->file_name)
                ? sanitize_file_name($item->file_name . '.' . $item->file_extension)
                : basename($source_file);

            $upload_dir = wp_upload_dir();
            $destination = $upload_dir['path'] . '/' . $filename;

            if (!file_exists($destination)) {
                copy($source_file, $destination);
            }

            $filetype = wp_check_filetype($destination, null);
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title'     => pathinfo($filename, PATHINFO_FILENAME),
                'post_status'    => 'inherit',
            );

            $attach_id = wp_insert_attachment($attachment, $destination, $post_id);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $destination);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // ACF repeater
            if (function_exists('update_field')) {
                $row = array('pdf' => $attach_id);
                $existing_rows = get_field('documents', $post_id) ?: array();
                $existing_rows[] = $row;
                update_field('documents', $existing_rows, $post_id);
            }

            echo "Row $counter: Document added ($filename)<br>";
        }
    }

    echo "Row $counter/$total imported (Post ID: $post_id)<br>";
}

echo "<hr><h2>Publications import finished successfully.</h2>";
