<?php
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
$log_file = get_template_directory() . '/import-recruitments-log.txt';

// Logging function
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo $message . "<br>";
    flush();
}

// Clear previous log
file_put_contents($log_file, "=== Recruitment Import Started ===" . PHP_EOL);

global $wpdb;
$table_name = 'recruitements_english';

// Helper function to sanitize values
function clean_value($value) {
    if ($value === null || strtoupper(trim($value)) === 'NULL') {
        return '';
    }
    return $value;
}

// Helper function to convert date to Ymd format (e.g., 20260210)
function convert_date_to_Ymd($date_string) {
    if (empty($date_string) || strtoupper(trim($date_string)) === 'NULL') {
        return '';
    }
    
    // Try to parse the date
    $timestamp = strtotime($date_string);
    if ($timestamp === false) {
        return '';
    }
    
    // Convert to Ymd format (e.g., 20260210)
    return date('Ymd', $timestamp);
}

// Helper function to map status
function map_status($status) {
    if (empty($status) || strtoupper(trim($status)) === 'NULL') {
        return 'open';
    }
    
    $status = trim(strtolower($status));
    
    if ($status === 'closed') {
        return 'closed';
    } elseif ($status === 'upcoming') {
        return 'upcoming';
    } else {
        return 'open';
    }
}

// Check if ACF is active
if (!function_exists('update_field')) {
    log_message("ERROR: Advanced Custom Fields (ACF) plugin is not active!", $log_file);
    wp_die('ACF plugin is required for this import script.');
}

// Fetch all rows - using same pattern as import-events.php
log_message("Fetching data from table: $table_name", $log_file);

// Try to get rows - if ORDER BY fails, try without it
$recruitments = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");
if (!$recruitments && $wpdb->last_error) {
    log_message("Query with ORDER BY id failed, trying without ORDER BY: " . $wpdb->last_error, $log_file);
    $recruitments = $wpdb->get_results("SELECT * FROM $table_name");
}

if ($recruitments) {
    $total = count($recruitments);
    log_message("Found $total rows to process", $log_file);
    
    // Debug: Show available columns from first row
    if ($total > 0 && isset($recruitments[0])) {
        $first_row = $recruitments[0];
        $columns = array_keys(get_object_vars($first_row));
        log_message("Available columns: " . implode(', ', $columns), $log_file);
        echo "<p><strong>Available columns:</strong> " . esc_html(implode(', ', $columns)) . "</p>";
        
        // Debug: Show first row data and test ID access
        echo "<h3>First row data (for debugging):</h3><pre>";
        print_r($first_row);
        echo "</pre>";
        
        // Test ID access methods
        echo "<h3>ID Column Access Test:</h3><ul>";
        $id_variations = array('id', 'ID', 'Id', 'iD', 'ID ', ' ID');
        foreach ($id_variations as $id_var) {
            $test_val = isset($first_row->$id_var) ? $first_row->$id_var : (isset($first_row->{$id_var}) ? $first_row->{$id_var} : 'NOT FOUND');
            echo "<li><strong>\$item->$id_var:</strong> " . htmlspecialchars($test_val) . "</li>";
        }
        // Try accessing all properties
        foreach ($columns as $col) {
            if (stripos($col, 'id') !== false) {
                $test_val = isset($first_row->$col) ? $first_row->$col : (isset($first_row->{$col}) ? $first_row->{$col} : 'NOT FOUND');
                echo "<li><strong>\$item->$col:</strong> " . htmlspecialchars($test_val) . "</li>";
            }
        }
        echo "</ul>";
    }
    
    echo "<h2>Starting import of $total rows...</h2><hr>";

    $counter = 0;
    $created = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($recruitments as $item) {
        $counter++;

        // Get the ID from the row FIRST - try multiple variations
        $external_id = null;
        $id_column_name = null;
        
        // Get all properties to check
        $vars = get_object_vars($item);
        
        // Try different ID column name variations
        $id_variations = array('id', 'ID', 'Id', 'iD', 'ID ', ' ID');
        foreach ($id_variations as $id_var) {
            if (isset($vars[$id_var])) {
                $val = $vars[$id_var];
                if (is_numeric($val) && (int)$val > 0) {
                    $external_id = (int)$val;
                    $id_column_name = $id_var;
                    break;
                }
            }
        }
        
        // If still not found, try to find any column containing 'id' (case insensitive)
        if (!$external_id) {
            foreach ($vars as $key => $value) {
                if (stripos($key, 'id') !== false && is_numeric($value) && (int)$value > 0) {
                    $external_id = (int)$value;
                    $id_column_name = $key;
                    log_message("Row $counter: Found ID in column '$key': $external_id", $log_file);
                    break;
                }
            }
        }
        
        // If still no ID, show debug info and use row counter as fallback
        if (!$external_id) {
            if ($counter === 1) {
                log_message("DEBUG: Available properties in first row:", $log_file);
                foreach ($vars as $key => $value) {
                    $display_val = is_string($value) ? substr($value, 0, 50) : $value;
                    log_message("  '$key' => " . var_export($display_val, true), $log_file);
                }
            }
            $external_id = $counter;
            log_message("Row $counter/$total WARNING: No ID column found, using row number as ID: $external_id", $log_file);
        }

        // Get Position Title - try different possible column names
        $position_title = '';
        if (isset($item->{'Position Title'})) {
            $position_title = clean_value($item->{'Position Title'});
        } elseif (isset($item->position_title)) {
            $position_title = clean_value($item->position_title);
        } elseif (isset($item->Position_Title)) {
            $position_title = clean_value($item->Position_Title);
        }

        // Skip rows with empty title
        if (empty($position_title) || strtoupper(trim($position_title)) === 'NULL') {
            log_message("Row $counter/$total (ID: $external_id) skipped: missing Position Title", $log_file);
            $skipped++;
            continue;
        }

        // Check if this row was already imported
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'external_recruitment_id' AND meta_value = %d",
            $external_id
        ));

        $is_update = !empty($existing);

        // Get Start Date
        $start_date = '';
        if (isset($item->{'Start Date'})) {
            $start_date_raw = clean_value($item->{'Start Date'});
            $start_date = convert_date_to_Ymd($start_date_raw);
        } elseif (isset($item->start_date)) {
            $start_date_raw = clean_value($item->start_date);
            $start_date = convert_date_to_Ymd($start_date_raw);
        } elseif (isset($item->Start_Date)) {
            $start_date_raw = clean_value($item->Start_Date);
            $start_date = convert_date_to_Ymd($start_date_raw);
        }

        // Get End Date - try multiple possible column names
        $end_date = '';
        if (isset($item->{'End Date (Incl# Extensions)'})) {
            $end_date_raw = clean_value($item->{'End Date (Incl# Extensions)'});
            $end_date = convert_date_to_Ymd($end_date_raw);
        } elseif (isset($item->{'End Date'})) {
            $end_date_raw = clean_value($item->{'End Date'});
            $end_date = convert_date_to_Ymd($end_date_raw);
        } elseif (isset($item->end_date)) {
            $end_date_raw = clean_value($item->end_date);
            $end_date = convert_date_to_Ymd($end_date_raw);
        } elseif (isset($item->End_Date)) {
            $end_date_raw = clean_value($item->End_Date);
            $end_date = convert_date_to_Ymd($end_date_raw);
        }

        // Get Status
        $status = 'open';
        if (isset($item->Status)) {
            $status = map_status(clean_value($item->Status));
        } elseif (isset($item->status)) {
            $status = map_status(clean_value($item->status));
        }

        // Get Entity
        $entity = '';
        if (isset($item->Entity)) {
            $entity = clean_value($item->Entity);
        } elseif (isset($item->entity)) {
            $entity = clean_value($item->entity);
        }

        // Prepare post data
        $post_data = array(
            'post_title'   => $position_title,
            'post_status'  => 'publish',
            'post_type'    => 'recruitments',
        );

        if ($is_update) {
            $post_data['ID'] = $existing;
            $post_id = wp_update_post($post_data, true);
            log_message("Row $counter/$total: Updating existing post (ID: $post_id, External ID: $external_id)", $log_file);
        } else {
            $post_id = wp_insert_post($post_data, true);
            log_message("Row $counter/$total: Creating new post (ID: $post_id, External ID: $external_id)", $log_file);
        }

        if (is_wp_error($post_id)) {
            log_message("Row $counter/$total ERROR: Failed to " . ($is_update ? 'update' : 'create') . " post - " . $post_id->get_error_message(), $log_file);
            $errors++;
            continue;
        }

        // Update external_recruitment_id meta
        update_post_meta($post_id, 'external_recruitment_id', $external_id);

        // Update ACF fields
        if (!empty($start_date)) {
            update_field('opening_date', $start_date, $post_id);
        }
        if (!empty($end_date)) {
            update_field('closing_date', $end_date, $post_id);
        }
        update_field('recruitement_status', $status, $post_id);
        if (!empty($entity)) {
            update_field('entity', $entity, $post_id);
        }

        // Log success
        if ($is_update) {
            $updated++;
            log_message("Row $counter/$total SUCCESS: Updated post ID $post_id (External ID: $external_id) - Title: $position_title, Status: $status", $log_file);
        } else {
            $created++;
            log_message("Row $counter/$total SUCCESS: Created post ID $post_id (External ID: $external_id) - Title: $position_title, Status: $status", $log_file);
        }

        // Log field values
        log_message("  - Opening Date: " . ($start_date ?: 'empty'), $log_file);
        log_message("  - Closing Date: " . ($end_date ?: 'empty'), $log_file);
        log_message("  - Status: $status", $log_file);
        log_message("  - Entity: " . ($entity ?: 'empty'), $log_file);
    }

    // Summary
    log_message("", $log_file);
    log_message("=== Import Summary ===", $log_file);
    log_message("Total rows processed: $total", $log_file);
    log_message("Created: $created", $log_file);
    log_message("Updated: $updated", $log_file);
    log_message("Skipped: $skipped", $log_file);
    log_message("Errors: $errors", $log_file);
    log_message("=== Import Completed ===", $log_file);

    echo "<hr>";
    echo "<h2>Import Complete!</h2>";
    echo "<p><strong>Total rows:</strong> $total</p>";
    echo "<p><strong>Created:</strong> $created</p>";
    echo "<p><strong>Updated:</strong> $updated</p>";
    echo "<p><strong>Skipped:</strong> $skipped</p>";
    echo "<p><strong>Errors:</strong> $errors</p>";
    echo "<p><strong>Log file:</strong> <code>$log_file</code></p>";
} else {
    log_message("No data found in the $table_name table.", $log_file);
    echo "<h2>No data found in the $table_name table.</h2>";
}
?>
