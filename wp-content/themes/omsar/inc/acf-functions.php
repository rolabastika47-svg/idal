<?php
/**
 * ACF Custom Functions
 * 
 * This file contains custom functions for Advanced Custom Fields (ACF).
 */

/**
 * Ensure only one former_ministers post can have is_current = 1 at any time
 * When a post is saved with is_current = 1, all other posts are automatically set to 0
 */
add_action('acf/save_post', 'omsar_ensure_single_current_minister', 20);
add_action('save_post_former_ministers', 'omsar_ensure_single_current_minister_after_save', 20);
function omsar_ensure_single_current_minister($post_id) {
    // Prevent infinite loops and skip revisions/autosaves
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Only process former_ministers post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'former_ministers') {
        return;
    }

    // Get the raw meta value to check serialized format
    $is_current_raw = get_post_meta($post_id, 'is_current', true);
    $is_current = get_field('is_current', $post_id);

    // Check if this post has is_current = 1
    // Handle both serialized array and unserialized formats
    $is_current_checked = false;
    
    // Check raw meta value (serialized format: a:1:{i:0;s:1:"1";})
    if (is_string($is_current_raw) && (strpos($is_current_raw, 's:1:"1"') !== false || $is_current_raw === '1')) {
        $is_current_checked = true;
    }
    
    // Also check via get_field (unserialized)
    if (!$is_current_checked) {
        if (is_array($is_current)) {
            $is_current_checked = in_array('1', $is_current, true);
        } elseif ($is_current === '1' || $is_current === 1) {
            $is_current_checked = true;
        }
    }

    // If this post is being set as current (is_current = 1)
    if ($is_current_checked) {
        global $wpdb;
        
        // Query database directly to find all posts with is_current containing "1"
        // This handles serialized format: a:1:{i:0;s:1:"1";}
        $pattern = '%s:1:"1"%';
        $other_post_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT pm.post_id 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.post_id != %d 
            AND p.post_type = 'former_ministers'
            AND pm.meta_key = 'is_current' 
            AND (pm.meta_value LIKE %s OR pm.meta_value = '1')",
            $post_id,
            $pattern
        ));

        // Unset is_current for all other posts
        if (!empty($other_post_ids)) {
            foreach ($other_post_ids as $other_post_id) {
                // Get current value via get_field to preserve format
                $current_value = get_field('is_current', $other_post_id);
                
                // Remove '1' from the array if it exists
                if (is_array($current_value)) {
                    $current_value = array_filter($current_value, function($val) {
                        return $val !== '1' && $val !== 1;
                    });
                    // Re-index array
                    $current_value = array_values($current_value);
                } else {
                    // If it's not an array, set it to empty array
                    $current_value = array();
                }
                
                // Update the field - this will properly serialize it
                update_field('is_current', $current_value, $other_post_id);
            }
        }
    }
}

/**
 * Also run after post save to catch any edge cases
 */
function omsar_ensure_single_current_minister_after_save($post_id) {
    // Prevent infinite loops
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Only process former_ministers post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'former_ministers') {
        return;
    }
    
    // Call the main function
    omsar_ensure_single_current_minister($post_id);
}

/**
 * Cleanup function to fix existing duplicates
 * Run this once via WP-CLI or add to functions.php temporarily:
 * add_action('admin_init', 'omsar_cleanup_duplicate_current_ministers');
 */
function omsar_cleanup_duplicate_current_ministers() {
    // Only run if user has permission
    if (!current_user_can('manage_options')) {
        return;
    }
    
    global $wpdb;
    
    // Find all posts with is_current = 1
    $pattern = '%s:1:"1"%';
    $all_current_posts = $wpdb->get_results($wpdb->prepare(
        "SELECT pm.post_id 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE p.post_type = 'former_ministers'
        AND pm.meta_key = 'is_current' 
        AND (pm.meta_value LIKE %s OR pm.meta_value = '1')
        ORDER BY p.post_date DESC",
        $pattern
    ));
    
    // If more than one, keep only the most recent one
    if (count($all_current_posts) > 1) {
        // Keep the first one (most recent), unset all others
        $keep_post_id = $all_current_posts[0]->post_id;
        
        for ($i = 1; $i < count($all_current_posts); $i++) {
            $other_post_id = $all_current_posts[$i]->post_id;
            $current_value = get_field('is_current', $other_post_id);
            
            if (is_array($current_value)) {
                $current_value = array_filter($current_value, function($val) {
                    return $val !== '1' && $val !== 1;
                });
                $current_value = array_values($current_value);
            } else {
                $current_value = array();
            }
            
            update_field('is_current', $current_value, $other_post_id);
        }
    }
}

/**
 * Ensure publications can only select one publication_category (taxonomy)
 * When saving, if multiple categories are selected, keep only the first one
 */
add_action('save_post_publications', 'omsar_ensure_single_publication_category_taxonomy', 20);

function omsar_ensure_single_publication_category_taxonomy($post_id) {
    // Prevent infinite loops and skip revisions/autosaves
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Only process publications post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'publications') {
        return;
    }
    
    // Check if taxonomy terms were submitted
    if (isset($_POST['tax_input']['publication_category'])) {
        $terms = $_POST['tax_input']['publication_category'];
        
        // If it's an array with more than one term, keep only the first one
        if (is_array($terms) && count($terms) > 1) {
            // Keep only the first term (remove others)
            $single_term = array($terms[0]);
            $_POST['tax_input']['publication_category'] = $single_term;
        }
    }
    
    // After save, ensure only one term is assigned
    $assigned_terms = wp_get_post_terms($post_id, 'publication_category', array('fields' => 'ids'));
    
    if (is_array($assigned_terms) && count($assigned_terms) > 1) {
        // Keep only the first term
        $single_term_id = $assigned_terms[0];
        wp_set_post_terms($post_id, array($single_term_id), 'publication_category', false);
    }
}

/**
 * Modify taxonomy metabox to enforce single selection
 */
add_action('admin_init', 'omsar_modify_publication_category_metabox');

function omsar_modify_publication_category_metabox() {
    // Remove default metabox
    remove_meta_box('publication_categorydiv', 'publications', 'side');
    
    // Add custom metabox with single selection
    add_meta_box(
        'publication_category_single',
        __('Publication Category', 'omsar'),
        'omsar_render_single_publication_category_metabox',
        'publications',
        'side',
        'default'
    );
}

/**
 * Render custom metabox with single selection (radio buttons)
 */
function omsar_render_single_publication_category_metabox($post) {
    $taxonomy = 'publication_category';
    $tax = get_taxonomy($taxonomy);
    
    // Get currently selected term
    $selected_term = wp_get_post_terms($post->ID, $taxonomy, array('fields' => 'ids'));
    $selected_term_id = !empty($selected_term) ? $selected_term[0] : 0;
    
    // Get all terms
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));
    
    // Use nonce for verification
    wp_nonce_field('omsar_publication_category', 'omsar_publication_category_nonce');
    
    if (!empty($terms) && !is_wp_error($terms)) {
        echo '<div style="max-height: 200px; overflow-y: auto;">';
        echo '<ul style="list-style: none; margin: 0; padding: 0;">';
        
        foreach ($terms as $term) {
            $checked = ($term->term_id == $selected_term_id) ? 'checked="checked"' : '';
            echo '<li style="margin: 5px 0;">';
            echo '<label>';
            echo '<input type="radio" name="tax_input[' . esc_attr($taxonomy) . '][]" value="' . esc_attr($term->term_id) . '" ' . $checked . ' /> ';
            echo esc_html($term->name);
            echo '</label>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p>' . __('No categories available.', 'omsar') . '</p>';
    }
}

/**
 * Save single taxonomy term selection
 */
add_action('save_post_publications', 'omsar_save_single_publication_category', 10, 2);

function omsar_save_single_publication_category($post_id, $post) {
    // Verify nonce
    if (!isset($_POST['omsar_publication_category_nonce']) || 
        !wp_verify_nonce($_POST['omsar_publication_category_nonce'], 'omsar_publication_category')) {
        return;
    }
    
    // Prevent infinite loops
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Check if terms were submitted
    if (isset($_POST['tax_input']['publication_category'])) {
        $terms = $_POST['tax_input']['publication_category'];
        
        // Ensure it's an array
        if (!is_array($terms)) {
            $terms = array($terms);
        }
        
        // Keep only the first term
        $single_term_id = !empty($terms) ? intval($terms[0]) : 0;
        
        if ($single_term_id > 0) {
            // Set only this term
            wp_set_post_terms($post_id, array($single_term_id), 'publication_category', false);
        } else {
            // Remove all terms if none selected
            wp_set_post_terms($post_id, array(), 'publication_category', false);
        }
    }
}

/**
 * Get ACF taxonomy terms for the current post
 * 
 * Retrieves taxonomy terms from an ACF taxonomy field and returns them as an array of term objects.
 * Handles various ACF return formats (term objects, term IDs, arrays, etc.)
 * 
 * @param string $field_name The ACF field name
 * @param int|null $post_id Optional. Post ID. Defaults to current post ID.
 * @return array Array of term objects, or empty array if no terms found
 */
function omsar_get_acf_taxonomy_terms($field_name, $post_id = null) {
    // Use current post ID if not provided
    if ($post_id === null) {
        $post_id = get_the_ID();
    }
    
    // Return empty array if no post ID
    if (!$post_id) {
        return array();
    }
    
    // Check if ACF is available
    if (!function_exists('get_field')) {
        return array();
    }
    
    // Get the ACF field value
    $value = get_field($field_name, $post_id);
    
    // Return empty array if no value
    if (empty($value)) {
        return array();
    }
    
    $terms = array();
    
    // Handle array of values (multiple terms)
    if (is_array($value)) {
        foreach ($value as $item) {
            $term = null;
            
            // If it's already a term object
            if (is_object($item) && isset($item->term_id)) {
                $term = $item;
            }
            // If it's a term ID (numeric)
            elseif (is_numeric($item)) {
                $term = get_term($item);
            }
            // If it's an array with term_id
            elseif (is_array($item) && isset($item['term_id'])) {
                $term = get_term($item['term_id']);
            }
            // If it's an array with value key (ACF format)
            elseif (is_array($item) && isset($item['value'])) {
                $term_id = is_numeric($item['value']) ? $item['value'] : null;
                if ($term_id) {
                    $term = get_term($term_id);
                }
            }
            // If it's a string that might be a term ID
            elseif (is_string($item) && is_numeric($item)) {
                $term = get_term((int) $item);
            }
            
            // Add term to array if valid
            if ($term && !is_wp_error($term) && !empty($term->term_id)) {
                $terms[] = $term;
            }
        }
    }
    // Handle single value
    else {
        $term = null;
        
        // If it's already a term object
        if (is_object($value) && isset($value->term_id)) {
            $term = $value;
        }
        // If it's a term ID (numeric)
        elseif (is_numeric($value)) {
            $term = get_term($value);
        }
        // If it's an array with term_id
        elseif (is_array($value) && isset($value['term_id'])) {
            $term = get_term($value['term_id']);
        }
        // If it's an array with value key (ACF format)
        elseif (is_array($value) && isset($value['value'])) {
            $term_id = is_numeric($value['value']) ? $value['value'] : null;
            if ($term_id) {
                $term = get_term($term_id);
            }
        }
        // If it's a string that might be a term ID
        elseif (is_string($value) && is_numeric($value)) {
            $term = get_term((int) $value);
        }
        // Try to get term by slug or name (need to know taxonomy)
        else {
            // Get field object to find taxonomy
            if (function_exists('get_field_object')) {
                $field_object = get_field_object($field_name, $post_id);
                $taxonomy_name = !empty($field_object['taxonomy']) ? $field_object['taxonomy'] : '';
                
                if ($taxonomy_name && is_string($value)) {
                    $term = get_term_by('slug', $value, $taxonomy_name);
                    if (!$term) {
                        $term = get_term_by('name', $value, $taxonomy_name);
                    }
                }
            }
        }
        
        // Add term to array if valid
        if ($term && !is_wp_error($term) && !empty($term->term_id)) {
            $terms[] = $term;
        }
    }
    
    // Remove duplicates based on term_id
    $unique_terms = array();
    $seen_ids = array();
    foreach ($terms as $term) {
        if (!in_array($term->term_id, $seen_ids)) {
            $unique_terms[] = $term;
            $seen_ids[] = $term->term_id;
        }
    }
    
    return $unique_terms;
}

