<?php
/**
 * Recruitment Arabic Sync Script
 * 
 * Syncs Arabic data from MySQL table `recruitements_arabic` 
 * into existing WordPress posts of type `recruitments`
 * 
 * This script only UPDATES existing posts that have matching external_recruitment_id.
 * It does NOT create new posts.
 * 
 * Usage: Access via browser: /wp-content/themes/omsar/sync-recruitments-arabic.php
 */

// Load WordPress environment
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    wp_die('You do not have permission to run this script.');
}

// Remove execution time limit
set_time_limit(0);
ob_implicit_flush(true);
ob_end_flush();

// Log file path
$log_file = get_template_directory() . '/sync-recruitments-arabic-log.txt';

// Logging function
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo $message . "<br>";
    flush();
}

// Clear previous log
file_put_contents($log_file, "=== Recruitment Arabic Sync Started ===" . PHP_EOL);

global $wpdb;
$table_name = 'recruitements_arabic';

// Helper function to sanitize values
function clean_value($value) {
    if ($value === null || strtoupper(trim($value)) === 'NULL') {
        return '';
    }
    return trim($value);
}

// Check if ACF is active
if (!function_exists('update_field')) {
    log_message("ERROR: Advanced Custom Fields (ACF) plugin is not active!", $log_file);
    wp_die('ACF plugin is required for this sync script.');
}

// Fetch all rows from the Arabic table
log_message("Fetching data from table: $table_name", $log_file);
$arabic_recruitments = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");

if (!$arabic_recruitments) {
    log_message("No data found in the $table_name table.", $log_file);
    echo "<h2>No data found in the $table_name table.</h2>";
    exit;
}

$total = count($arabic_recruitments);
log_message("Found $total rows to process", $log_file);

// Debug: Show available columns from first row
if ($total > 0 && isset($arabic_recruitments[0])) {
    $first_row = $arabic_recruitments[0];
    $columns = array_keys(get_object_vars($first_row));
    log_message("Available columns: " . implode(', ', $columns), $log_file);
    echo "<p><strong>Available columns:</strong> " . esc_html(implode(', ', $columns)) . "</p>";
}

echo "<h2>Starting sync of $total rows...</h2><hr>";

$counter = 0;
$updated = 0;
$not_found = 0;
$skipped = 0;
$errors = 0;

foreach ($arabic_recruitments as $item) {
    $counter++;

    // Get the ID from the Arabic table row
    $external_id = null;
    
    // Try different ID column name variations
    $id_variations = array('id', 'ID', 'Id', 'iD');
    foreach ($id_variations as $id_var) {
        if (isset($item->$id_var)) {
            $external_id = (int)$item->$id_var;
            break;
        }
        if (isset($item->{$id_var})) {
            $external_id = (int)$item->{$id_var};
            break;
        }
    }
    
    // If still not found, try to find any column containing 'id' (case insensitive)
    if (!$external_id) {
        $vars = get_object_vars($item);
        foreach ($vars as $key => $value) {
            if (stripos($key, 'id') !== false && is_numeric($value) && (int)$value > 0) {
                $external_id = (int)$value;
                break;
            }
        }
    }
    
    if (!$external_id) {
        log_message("Row $counter/$total skipped: No ID found", $log_file);
        $skipped++;
        continue;
    }

    // Get Position Title (Arabic)
    $position_title_ar = '';
    if (isset($item->{'Position Title'})) {
        $position_title_ar = clean_value($item->{'Position Title'});
    } elseif (isset($item->position_title)) {
        $position_title_ar = clean_value($item->position_title);
    } elseif (isset($item->Position_Title)) {
        $position_title_ar = clean_value($item->Position_Title);
    }

    // Get Entity (Arabic)
    $entity_ar = '';
    if (isset($item->Entity)) {
        $entity_ar = clean_value($item->Entity);
    } elseif (isset($item->entity)) {
        $entity_ar = clean_value($item->entity);
    }

    // Find existing WordPress post by external_recruitment_id
    $existing_post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = 'external_recruitment_id' AND meta_value = %d
         LIMIT 1",
        $external_id
    ));

    if (!$existing_post_id) {
        log_message("Row $counter/$total (External ID: $external_id) skipped: No matching WordPress post found", $log_file);
        $not_found++;
        continue;
    }

    // Verify the post exists and is of type 'recruitments'
    $post = get_post($existing_post_id);
    if (!$post || $post->post_type !== 'recruitments') {
        log_message("Row $counter/$total (External ID: $external_id) skipped: Post ID $existing_post_id is not a 'recruitments' post type", $log_file);
        $skipped++;
        continue;
    }

    // Update ACF fields
    $update_success = true;
    $update_errors = array();

    // Update job_title_ar
    if (!empty($position_title_ar)) {
        $result = update_field('job_title_ar', $position_title_ar, $existing_post_id);
        if ($result === false) {
            $update_errors[] = 'job_title_ar';
            $update_success = false;
        }
    } else {
        log_message("Row $counter/$total (External ID: $external_id, Post ID: $existing_post_id) INFO: Position Title is empty", $log_file);
    }

    // Update entity_ar
    if (!empty($entity_ar)) {
        $result = update_field('entity_ar', $entity_ar, $existing_post_id);
        if ($result === false) {
            $update_errors[] = 'entity_ar';
            $update_success = false;
        }
    } else {
        log_message("Row $counter/$total (External ID: $external_id, Post ID: $existing_post_id) INFO: Entity is empty", $log_file);
    }

    if (!$update_success) {
        log_message("Row $counter/$total (External ID: $external_id, Post ID: $existing_post_id) ERROR: Failed to update fields: " . implode(', ', $update_errors), $log_file);
        $errors++;
        continue;
    }

    // Log success
    $updated++;
    log_message("Row $counter/$total SUCCESS: Updated post ID $existing_post_id (External ID: $external_id)", $log_file);
    log_message("  - Job Title AR: " . ($position_title_ar ?: 'empty'), $log_file);
    log_message("  - Entity AR: " . ($entity_ar ?: 'empty'), $log_file);
}

// Summary
log_message("", $log_file);
log_message("=== Sync Summary ===", $log_file);
log_message("Total rows processed: $total", $log_file);
log_message("Updated: $updated", $log_file);
log_message("Not found (no matching post): $not_found", $log_file);
log_message("Skipped: $skipped", $log_file);
log_message("Errors: $errors", $log_file);
log_message("=== Sync Completed ===", $log_file);

echo "<hr>";
echo "<h2>Sync Complete!</h2>";
echo "<p><strong>Total rows:</strong> $total</p>";
echo "<p><strong>Updated:</strong> $updated</p>";
echo "<p><strong>Not found (no matching post):</strong> $not_found</p>";
echo "<p><strong>Skipped:</strong> $skipped</p>";
echo "<p><strong>Errors:</strong> $errors</p>";
echo "<p><strong>Log file:</strong> <code>$log_file</code></p>";
?>
