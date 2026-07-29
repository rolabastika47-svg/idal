<?php
// Load WordPress environment
require_once( '../../../wp-load.php' );

// Security check
if ( !current_user_can('administrator') ) {
    wp_die('You do not have permission to run this script.');
}

// Remove execution time limit
set_time_limit(0);
ob_implicit_flush(true);
ob_end_flush();

global $wpdb;
$table_name = 'events_data';

// Language mapping
$lang_map = array(
    'ar-LB' => 'ar',
    'en-US' => 'en_AU',
);

// Helper function to sanitize values
function clean_value($value) {
    if ($value === null || strtoupper(trim($value)) === 'NULL') {
        return '';
    }
    return $value;
}

// Fetch all rows
$events_items = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id ASC" );

if ( $events_items ) {
    $total = count($events_items);
    echo "<h2>Starting import of $total rows...</h2><hr>";

    $counter = 0;

    foreach ( $events_items as $item ) {
        $counter++;

        // Skip rows with empty title
        if ( empty($item->title) || strtoupper(trim($item->title)) === 'NULL' ) {
            echo "Row $counter/$total skipped: missing title.<br>";
            flush();
            continue;
        }

        // Check if this row was already imported
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'event_data_id' AND meta_value = %d",
            $item->id
        ));

        if ( $existing ) {
            echo "Row $counter/$total skipped (already imported, Post ID: $existing)<br>";
            flush();
            continue;
        }

        // Convert date to proper MySQL format
        $post_date = null;
        if (!empty($item->date) && strtoupper(trim($item->date)) !== 'NULL') {
            $timestamp = strtotime($item->date);
            if ($timestamp) {
                $post_date = date('Y-m-d H:i:s', $timestamp);
            } else {
                $post_date = current_time('mysql');
            }
        } else {
            $post_date = current_time('mysql');
        }

        // Prepare post data
        $post_data = array(
            'post_title'    => clean_value($item->title),
            'post_content'  => clean_value($item->description),
            'post_excerpt'  => clean_value($item->summary),
            'post_status'   => 'publish',
            'post_date'     => $post_date,
            'post_type'     => 'post',
        );

        // Insert post with error handling
        $post_id = wp_insert_post( $post_data, true );
        if ( is_wp_error($post_id) ) {
            echo "Row $counter/$total failed to insert: " . $post_id->get_error_message() . "<br>";
            flush();
            continue;
        }

        // Update meta fields
        update_post_meta( $post_id, 'location', clean_value($item->location) );
        update_post_meta( $post_id, 'custom_date', clean_value($item->date) );
        update_post_meta( $post_id, 'post_type_option', 'events' );
        update_post_meta( $post_id, 'event_data_id', $item->id ); // store table ID to avoid duplicates

        // Assign Polylang language
        if ( function_exists('pll_set_post_language') && !empty($item->language) ) {
            $pll_lang = isset($lang_map[$item->language]) ? $lang_map[$item->language] : null;
            if ($pll_lang) {
                pll_set_post_language($post_id, $pll_lang);
            } else {
                echo "Row $counter: Language mapping missing for '{$item->language}'<br>";
            }
        }

        echo "Row $counter/$total processed successfully (Post ID: $post_id)<br>";
        flush();
    }

    echo "<hr><h2>Import complete! Total rows processed: $total</h2>";
} else {
    echo "<h2>No data found in the events_data table.</h2>";
}
