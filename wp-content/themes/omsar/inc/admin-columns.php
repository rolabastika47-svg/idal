<?php
/**
 * Admin Columns for Custom Post Types
 * 
 * Adds custom columns to the admin post type listings.
 */

/**
 * Add custom column for is_current in former_ministers post type
 */
add_filter('manage_former_ministers_posts_columns', 'omsar_add_is_current_column');
function omsar_add_is_current_column($columns) {
    // Add the column before the date column
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'date') {
            $new_columns['is_current'] = __('Is Current', 'omsar');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

/**
 * Display the is_current column content
 */
add_action('manage_former_ministers_posts_custom_column', 'omsar_display_is_current_column', 10, 2);
function omsar_display_is_current_column($column, $post_id) {
    if ($column === 'is_current') {
        $is_current = get_field('is_current', $post_id);
        
        // Check if is_current is set to 1
        $is_checked = false;
        if (is_array($is_current)) {
            $is_checked = in_array('1', $is_current, true);
        } elseif ($is_current === '1' || $is_current === 1) {
            $is_checked = true;
        }
        
        $checked_class = $is_checked ? 'is-current-active' : '';
        $checked_attr = $is_checked ? 'data-checked="1"' : 'data-checked="0"';
        $nonce = wp_create_nonce('toggle_is_current_' . $post_id);
        
        echo '<a href="#" class="omsar-toggle-is-current ' . esc_attr($checked_class) . '" ' . $checked_attr . ' data-post-id="' . esc_attr($post_id) . '" data-nonce="' . esc_attr($nonce) . '" title="Click to toggle">';
        echo '<span class="omsar-star-icon"></span>';
        echo '</a>';
    }
}

/**
 * Make the is_current column sortable
 */
add_filter('manage_edit-former_ministers_sortable_columns', 'omsar_make_is_current_column_sortable');
function omsar_make_is_current_column_sortable($columns) {
    $columns['is_current'] = 'is_current';
    return $columns;
}

/**
 * Enqueue admin scripts and styles
 */
add_action('admin_enqueue_scripts', 'omsar_enqueue_admin_column_assets');
function omsar_enqueue_admin_column_assets($hook) {
    if ($hook !== 'edit.php') {
        return;
    }
    $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'post';
    $is_former_ministers = ($post_type === 'former_ministers');
    $is_procurement = ($post_type === 'procurement_notices');
    if (!$is_former_ministers && !$is_procurement) {
        return;
    }
    
    $inline_css = '';
    if ($is_former_ministers) {
        $inline_css .= '
        .omsar-toggle-is-current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: #E3F2FD;
            border: 2px solid #90CAF9;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .omsar-toggle-is-current:hover {
            background-color: #BBDEFB;
            border-color: #64B5F6;
            transform: scale(1.1);
        }
        
        .omsar-toggle-is-current.is-current-active {
            background-color: #E3F2FD;
            border-color: #1976D2;
        }
        
        .omsar-toggle-is-current.is-current-active:hover {
            background-color: #BBDEFB;
            border-color: #1565C0;
        }
        
        .omsar-star-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            position: relative;
        }
        
        .omsar-star-icon::after {
            content: "★";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: #1565C0;
            opacity: 0;
            transition: opacity 0.2s ease;
            line-height: 1;
            font-weight: normal;
        }
        
        .omsar-toggle-is-current.is-current-active .omsar-star-icon::after {
            opacity: 1;
        }
        
        .omsar-toggle-is-current.loading {
            opacity: 0.6;
            cursor: wait;
            pointer-events: none;
        }
        
        .omsar-toggle-is-current.loading .omsar-star-icon::after {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        th.column-is_current {
            width: 100px;
            text-align: center;
        }
        
        td.column-is_current {
            text-align: center;
        }
        ';
    }
    if ($inline_css !== '') {
        wp_add_inline_style('wp-admin', $inline_css);
    }
    
    if ($is_former_ministers) {
        wp_add_inline_script('jquery-core', '
            jQuery(document).ready(function($) {
                $(document).on("click", ".omsar-toggle-is-current", function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var postId = $button.data("post-id");
                    var nonce = $button.data("nonce");
                    var isChecked = $button.data("checked") === "1" ? 0 : 1;
                    if ($button.hasClass("loading")) return false;
                    $button.addClass("loading");
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: { action: "omsar_toggle_is_current", post_id: postId, is_current: isChecked, nonce: nonce },
                        success: function(response) {
                            if (response.success) {
                                if (isChecked === 1) {
                                    $(".omsar-toggle-is-current").each(function() {
                                        var $o = $(this);
                                        if ($o.data("post-id") !== postId) $o.removeClass("is-current-active").data("checked", "0");
                                    });
                                    $button.addClass("is-current-active").data("checked", "1");
                                } else {
                                    $button.removeClass("is-current-active").data("checked", "0");
                                }
                            } else {
                                alert("Error: " + (response.data && response.data.message ? response.data.message : "Could not update status"));
                            }
                        },
                        error: function() { alert("An error occurred. Please try again."); },
                        complete: function() { $button.removeClass("loading"); }
                    });
                    return false;
                });
            });
        ');
    }
    
    
    add_action('admin_footer', function() use ($is_former_ministers, $is_procurement) {
        if (!$is_former_ministers && !$is_procurement) return;
        if ($is_former_ministers) {
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                if (typeof ajaxurl === 'undefined') {
                    return;
                }
                
                $(document).on("click", ".omsar-toggle-is-current", function(e) {
                    e.preventDefault();
                    
                    var $button = $(this);
                    var postId = $button.data("post-id");
                    var nonce = $button.data("nonce");
                    var isChecked = $button.data("checked") === "1" ? 0 : 1;
                    
                    if ($button.hasClass("loading")) {
                        return false;
                    }
                    
                    $button.addClass("loading");
                    
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "omsar_toggle_is_current",
                            post_id: postId,
                            is_current: isChecked,
                            nonce: nonce
                        },
                    success: function(response) {
                        if (response.success) {
                            if (isChecked === 1) {
                                // When setting one as current, unset all others in the current view
                                $(".omsar-toggle-is-current").each(function() {
                                    var $other = $(this);
                                    if ($other.data("post-id") !== postId) {
                                        $other.removeClass("is-current-active").data("checked", "0");
                                    }
                                });
                                $button.addClass("is-current-active").data("checked", "1");
                            } else {
                                $button.removeClass("is-current-active").data("checked", "0");
                            }
                        } else {
                            alert("Error: " + (response.data && response.data.message ? response.data.message : "Could not update status"));
                        }
                    },
                        error: function() {
                            alert("An error occurred. Please try again.");
                        },
                        complete: function() {
                            $button.removeClass("loading");
                        }
                    });
                    
                    return false;
                });
            });
            </script>
            <?php
        }
    });
}

/**
 * Handle AJAX request to toggle is_current
 */
add_action('wp_ajax_omsar_toggle_is_current', 'omsar_handle_toggle_is_current');
function omsar_handle_toggle_is_current() {
    // Verify nonce
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    
    if (!wp_verify_nonce($nonce, 'toggle_is_current_' . $post_id)) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(array('message' => 'You do not have permission to edit this post'));
        return;
    }
    
    // Verify post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'former_ministers') {
        wp_send_json_error(array('message' => 'Invalid post type'));
        return;
    }
    
    // Get the new value
    $is_current = isset($_POST['is_current']) ? intval($_POST['is_current']) : 0;
    
    // Update the field
    if ($is_current === 1) {
        update_field('is_current', array('1'), $post_id);
    } else {
        update_field('is_current', array(), $post_id);
    }
    
    // The existing function in acf-functions.php will handle ensuring only one is current
    // But we trigger it manually to ensure it runs
    if ($is_current === 1) {
        omsar_ensure_single_current_minister($post_id);
    }
    
    wp_send_json_success(array('message' => 'Status updated successfully'));
}


/**
 * Add custom columns for recruitment_sub post type
 * Columns: ID, Email, Job Title, Subscription Date
 */
add_filter('manage_recruitment_sub_posts_columns', 'omsar_add_recruitment_sub_columns');
function omsar_add_recruitment_sub_columns($columns) {
    // Create new columns array
    $new_columns = array();
    
    // Add checkbox column first
    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }
    
    // Add ID column
    $new_columns['recruitment_sub_id'] = __('ID', 'omsar');
    
    // Add Email column
    $new_columns['recruitment_sub_email'] = __('Email', 'omsar');
    
    // Add Job Title column
    $new_columns['recruitment_sub_job_title'] = __('Job Title', 'omsar');
    
    // Add Subscription Date column
    $new_columns['recruitment_sub_subscription_date'] = __('Subscription Date', 'omsar');
    
    // Remove title and date columns (we don't need them)
    // Keep any other columns that might exist
    foreach ($columns as $key => $value) {
        if (!in_array($key, array('cb', 'title', 'date'))) {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}

/**
 * Display content for custom columns in recruitment_sub post type
 */
add_action('manage_recruitment_sub_posts_custom_column', 'omsar_display_recruitment_sub_column', 10, 2);
function omsar_display_recruitment_sub_column($column, $post_id) {
    switch ($column) {
        case 'recruitment_sub_id':
            echo esc_html($post_id);
            break;
            
        case 'recruitment_sub_email':
            // Get email from post meta or ACF field
            $email = '';
            if (function_exists('get_field')) {
                $email = get_field('email', $post_id);
            }
            if (empty($email)) {
                $email = get_post_meta($post_id, 'email', true);
            }
            if (!empty($email)) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            } else {
                echo '—';
            }
            break;
            
        case 'recruitment_sub_job_title':
            // Get recruitment_id from post meta or ACF field
            $recruitment_id = '';
            if (function_exists('get_field')) {
                $recruitment_id = get_field('recruitment_id', $post_id);
            }
            if (empty($recruitment_id)) {
                $recruitment_id = get_post_meta($post_id, 'recruitment_id', true);
            }
            
            if (!empty($recruitment_id) && get_post($recruitment_id)) {
                // Get job title from post title
                $job_title = get_the_title($recruitment_id);
                
                // Create link to edit the recruitment post
                $edit_link = get_edit_post_link($recruitment_id);
                if ($edit_link) {
                    echo '<a href="' . esc_url($edit_link) . '">' . esc_html($job_title) . '</a>';
                } else {
                    echo esc_html($job_title);
                }
            } else {
                echo '—';
            }
            break;

        case 'recruitment_sub_subscription_date':
            $subscription_date = '';
            if (function_exists('get_field')) {
                $subscription_date = get_field('subscription_date', $post_id);
            }
            if (empty($subscription_date)) {
                $subscription_date = get_post_meta($post_id, 'subscription_date', true);
            }
            
            if (!empty($subscription_date)) {
                // Format the date nicely
                // current_time('mysql') saves in local timezone, so we need to treat it as local time
                // Convert MySQL datetime to timestamp, then format it
                $timestamp = strtotime($subscription_date);
                if ($timestamp !== false) {
                    // Format using WordPress date/time format settings
                    $formatted_date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
                    echo esc_html($formatted_date);
                } else {
                    // Fallback: display as-is if conversion fails
                    echo esc_html($subscription_date);
                }
            } else {
                // Fallback to post date if subscription_date is not available
                echo esc_html(get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $post_id));
            }
            break;
    }
}

/**
 * Make ID and Subscription Date columns sortable for recruitment_sub
 */
add_filter('manage_edit-recruitment_sub_sortable_columns', 'omsar_make_recruitment_sub_columns_sortable');
function omsar_make_recruitment_sub_columns_sortable($columns) {
    $columns['recruitment_sub_id'] = 'ID';
    $columns['recruitment_sub_subscription_date'] = 'subscription_date';
    return $columns;
}

/**
 * Handle sorting for ID and Subscription Date columns
 */
add_action('pre_get_posts', 'omsar_recruitment_sub_column_orderby');
function omsar_recruitment_sub_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('post_type') !== 'recruitment_sub') {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby === 'ID') {
        $query->set('orderby', 'ID');
    } elseif ($orderby === 'subscription_date') {
        $query->set('meta_key', 'subscription_date');
        $query->set('orderby', 'meta_value');
    }
}

/**
 * Add filter dropdown for recruitment_sub to filter by recruitment
 */
add_action('restrict_manage_posts', 'omsar_add_recruitment_sub_filter');
function omsar_add_recruitment_sub_filter($post_type) {
    // Only add filter for recruitment_sub post type
    if ($post_type !== 'recruitment_sub') {
        return;
    }
    
    // Get all published recruitments
    $recruitments = get_posts(array(
        'post_type' => 'recruitments',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    // Get selected recruitment from URL
    $selected_recruitment = isset($_GET['filter_recruitment']) ? intval($_GET['filter_recruitment']) : 0;
    
    // Output dropdown
    echo '<select name="filter_recruitment" id="filter_recruitment">';
    echo '<option value="">' . __('All Recruitments', 'omsar') . '</option>';
    
    foreach ($recruitments as $recruitment) {
        // Get job title from post title
        $job_title = get_the_title($recruitment->ID);
        
        $selected = ($selected_recruitment === $recruitment->ID) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($recruitment->ID) . '" ' . $selected . '>' . esc_html($job_title) . '</option>';
    }
    
    echo '</select>';
}

/**
 * Filter recruitment_sub posts by selected recruitment
 */
add_action('pre_get_posts', 'omsar_filter_recruitment_sub_by_recruitment');
function omsar_filter_recruitment_sub_by_recruitment($query) {
    // Only in admin and for main query
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Only for recruitment_sub post type
    if ($query->get('post_type') !== 'recruitment_sub') {
        return;
    }
    
    // Check if filter is set
    $filter_recruitment = isset($_GET['filter_recruitment']) ? intval($_GET['filter_recruitment']) : 0;
    
    if ($filter_recruitment > 0) {
        // Add meta query to filter by recruitment_id
        $meta_query = $query->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }

        $meta_query[] = array(
            'key' => 'recruitment_id',
            'value' => $filter_recruitment,
            'compare' => '='
        );

        $query->set('meta_query', $meta_query);
    }
}

/**
 * Add export button to recruitment_sub admin page
 */
add_filter('views_edit-recruitment_sub', 'omsar_add_recruitment_sub_export_button');
function omsar_add_recruitment_sub_export_button($views) {
    // Check if user has access
    if (!function_exists('omsar_user_can_access_recruitment_sub') || !omsar_user_can_access_recruitment_sub()) {
        return $views;
    }
    
    // Build export URL with current filters and nonce
    $export_url = admin_url('admin.php');
    $export_url = add_query_arg('page', 'export_recruitment_sub', $export_url);
    $export_url = add_query_arg('_wpnonce', wp_create_nonce('export_recruitment_sub'), $export_url);
    
    $filter_recruitment = isset($_GET['filter_recruitment']) ? intval($_GET['filter_recruitment']) : 0;
    if ($filter_recruitment > 0) {
        $export_url = add_query_arg('filter_recruitment', $filter_recruitment, $export_url);
    }
    
    // Add export button
    $views['export'] = '<a href="' . esc_url($export_url) . '" class="button" style="margin-left: 10px;">' . __('Export to CSV', 'omsar') . '</a>';
    
    return $views;
}

/**
 * Handle export request for recruitment_sub posts
 * Intercept early to prevent WordPress from outputting HTML
 */
add_action('admin_init', 'omsar_handle_recruitment_sub_export');
function omsar_handle_recruitment_sub_export() {
    // Check if this is an export request
    if (!isset($_GET['page']) || $_GET['page'] !== 'export_recruitment_sub') {
        return;
    }
    
    // Check if user has access
    if (!function_exists('omsar_user_can_access_recruitment_sub') || !omsar_user_can_access_recruitment_sub()) {
        wp_die(__('You do not have permission to export Recruitment Subscribers.', 'omsar'));
    }
    
    // Check nonce for security
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'export_recruitment_sub')) {
        wp_die(__('Security check failed.', 'omsar'));
    }
    
    // Call export function
    omsar_export_recruitment_sub_csv();
}

/**
 * Register admin menu page (for URL routing)
 */
add_action('admin_menu', 'omsar_add_recruitment_sub_export_page');
function omsar_add_recruitment_sub_export_page() {
    add_submenu_page(
        null, // Don't add to menu
        __('Export Recruitment Subscribers', 'omsar'),
        __('Export Recruitment Subscribers', 'omsar'),
        'edit_posts',
        'export_recruitment_sub',
        '__return_empty_string' // Empty callback since we handle it in admin_init
    );
}

/**
 * Export recruitment_sub posts to CSV
 */
function omsar_export_recruitment_sub_csv() {
    // Clear all output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Disable error reporting to prevent any notices/warnings from appearing
    @ini_set('display_errors', 0);
    @set_time_limit(0);
    
    // Set headers for CSV download - must be before any output
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="recruitment_subscribers_' . date('Y-m-d_His') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 (helps Excel recognize UTF-8)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Email',
        'Job Title',
        'Date'
    ));
    
    // Add data rows in batches to avoid memory/time issues with large datasets
    $posts_per_page = 500;
    $paged = 1;

    do {
        $args = array(
            'post_type'      => 'recruitment_sub',
            'post_status'    => 'any',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        );

        // Apply filter if set
        $filter_recruitment = isset($_GET['filter_recruitment']) ? intval($_GET['filter_recruitment']) : 0;
        if ($filter_recruitment > 0) {
            $args['meta_query'] = array(
                array(
                    'key'   => 'recruitment_id',
                    'value' => $filter_recruitment,
                    'compare' => '=',
                ),
            );
        }

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            break;
        }

        foreach ($query->posts as $post_id) {
        
        // Get email
        $email = '';
        if (function_exists('get_field')) {
            $email = get_field('email', $post_id);
        }
        if (empty($email)) {
            $email = get_post_meta($post_id, 'email', true);
        }
        
        // Get recruitment_id and job title
        $recruitment_id = '';
        if (function_exists('get_field')) {
            $recruitment_id = get_field('recruitment_id', $post_id);
        }
        if (empty($recruitment_id)) {
            $recruitment_id = get_post_meta($post_id, 'recruitment_id', true);
        }
        
        $job_title = '';
        if (!empty($recruitment_id) && get_post($recruitment_id)) {
            $job_title = get_the_title($recruitment_id);
        }
        
        // Get post date
        $post_date = get_the_date('Y-m-d H:i:s', $post_id);
        
        // Write row
        fputcsv($output, array(
            $post_id,
            $email,
            $job_title,
            $post_date
        ));
        }

        wp_reset_postdata();
        $paged++;
    } while (true);
    
    // Close output stream
    fclose($output);
    
    // Exit immediately to prevent WordPress from outputting anything else
    exit;
}


/**
 * Add custom columns for procurement_sub post type
 * Columns: ID, Email, Procurement Title, Submission Date
 */
add_filter('manage_procurement_sub_posts_columns', 'omsar_add_procurement_sub_columns');
function omsar_add_procurement_sub_columns($columns) {
    $new_columns = array();

    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }

    $new_columns['procurement_sub_id'] = __('ID', 'omsar');
    $new_columns['procurement_sub_email'] = __('Email', 'omsar');
    $new_columns['procurement_sub_procurement_title'] = __('Procurement Title', 'omsar');
    $new_columns['procurement_sub_submission_date'] = __('Submission Date', 'omsar');

    foreach ($columns as $key => $value) {
        if (!in_array($key, array('cb', 'title', 'date'))) {
            $new_columns[$key] = $value;
        }
    }

    return $new_columns;
}

/**
 * Display content for custom columns in procurement_sub post type
 */
add_action('manage_procurement_sub_posts_custom_column', 'omsar_display_procurement_sub_column', 10, 2);
function omsar_display_procurement_sub_column($column, $post_id) {
    switch ($column) {
        case 'procurement_sub_id':
            echo esc_html($post_id);
            break;

        case 'procurement_sub_email':
            $email = '';
            if (function_exists('get_field')) {
                $email = get_field('email', $post_id);
            }
            if (empty($email)) {
                $email = get_post_meta($post_id, 'email', true);
            }
            if (!empty($email)) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            } else {
                echo '—';
            }
            break;

        case 'procurement_sub_procurement_title':
            $procurement_id = '';
            if (function_exists('get_field')) {
                $procurement_id = get_field('procurement_id', $post_id);
            }
            if (empty($procurement_id)) {
                $procurement_id = get_post_meta($post_id, 'procurement_id', true);
            }

            if (!empty($procurement_id) && get_post($procurement_id)) {
                $procurement_title = get_the_title($procurement_id);
                $edit_link = get_edit_post_link($procurement_id);
                if ($edit_link) {
                    echo '<a href="' . esc_url($edit_link) . '">' . esc_html($procurement_title) . '</a>';
                } else {
                    echo esc_html($procurement_title);
                }
            } else {
                echo '—';
            }
            break;

        case 'procurement_sub_submission_date':
            $submission_date = '';
            if (function_exists('get_field')) {
                $submission_date = get_field('submission_date', $post_id);
            }
            if (empty($submission_date)) {
                $submission_date = get_post_meta($post_id, 'submission_date', true);
            }
            
            if (!empty($submission_date)) {
                // Format the date nicely - use gmt=false to prevent timezone conversion
                // since current_time('mysql') already saves in site's local timezone
                $formatted_date = mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $submission_date, false);
                echo esc_html($formatted_date);
            } else {
                // Fallback to post date if submission_date is not available
                echo esc_html(get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $post_id));
            }
            break;
    }
}

/**
 * Make ID and Submission Date columns sortable for procurement_sub
 */
add_filter('manage_edit-procurement_sub_sortable_columns', 'omsar_make_procurement_sub_columns_sortable');
function omsar_make_procurement_sub_columns_sortable($columns) {
    $columns['procurement_sub_id'] = 'ID';
    $columns['procurement_sub_submission_date'] = 'submission_date';
    return $columns;
}

/**
 * Handle sorting for ID and Submission Date columns in procurement_sub
 */
add_action('pre_get_posts', 'omsar_procurement_sub_column_orderby');
function omsar_procurement_sub_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'procurement_sub') {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'ID') {
        $query->set('orderby', 'ID');
    } elseif ($orderby === 'submission_date') {
        $query->set('meta_key', 'submission_date');
        $query->set('orderby', 'meta_value');
    }
}

/**
 * Add export button to procurement_sub admin page
 */
add_filter('views_edit-procurement_sub', 'omsar_add_procurement_sub_export_button');
function omsar_add_procurement_sub_export_button($views) {
    // Check if user has access
    if (!function_exists('omsar_user_can_access_procurement_sub') || !omsar_user_can_access_procurement_sub()) {
        return $views;
    }
    
    // Build export URL with current filters and nonce
    $export_url = admin_url('admin.php');
    $export_url = add_query_arg('page', 'export_procurement_sub', $export_url);
    $export_url = add_query_arg('_wpnonce', wp_create_nonce('export_procurement_sub'), $export_url);
    
    // Add export button
    $views['export'] = '<a href="' . esc_url($export_url) . '" class="button" style="margin-left: 10px;">' . __('Export to CSV', 'omsar') . '</a>';
    
    return $views;
}

/**
 * Handle export request for procurement_sub posts
 * Intercept early to prevent WordPress from outputting HTML
 */
add_action('admin_init', 'omsar_handle_procurement_sub_export');
function omsar_handle_procurement_sub_export() {
    // Check if this is an export request
    if (!isset($_GET['page']) || $_GET['page'] !== 'export_procurement_sub') {
        return;
    }
    
    // Check if user has access
    if (!function_exists('omsar_user_can_access_procurement_sub') || !omsar_user_can_access_procurement_sub()) {
        wp_die(__('You do not have permission to export Procurement Subscribers.', 'omsar'));
    }
    
    // Check nonce for security
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'export_procurement_sub')) {
        wp_die(__('Security check failed.', 'omsar'));
    }
    
    // Call export function
    omsar_export_procurement_sub_csv();
}

/**
 * Register admin menu page (for URL routing)
 */
add_action('admin_menu', 'omsar_add_procurement_sub_export_page');
function omsar_add_procurement_sub_export_page() {
    add_submenu_page(
        null, // Don't add to menu
        __('Export Procurement Subscribers', 'omsar'),
        __('Export Procurement Subscribers', 'omsar'),
        'edit_posts',
        'export_procurement_sub',
        '__return_empty_string' // Empty callback since we handle it in admin_init
    );
}

/**
 * Export procurement_sub posts to CSV
 */
function omsar_export_procurement_sub_csv() {
    // Clear all output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Disable error reporting to prevent any notices/warnings from appearing
    @ini_set('display_errors', 0);
    @set_time_limit(0);
    
    // Set headers for CSV download - must be before any output
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="procurement_subscribers_' . date('Y-m-d_His') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 (helps Excel recognize UTF-8)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Email',
        'Procurement Title',
        'Date'
    ));
    
    // Add data rows in batches to avoid memory/time issues with large datasets
    $posts_per_page = 500;
    $paged = 1;

    do {
        $args = array(
            'post_type'      => 'procurement_sub',
            'post_status'    => 'any',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        );

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            break;
        }

        foreach ($query->posts as $post_id) {
        
        // Get email
        $email = '';
        if (function_exists('get_field')) {
            $email = get_field('email', $post_id);
        }
        if (empty($email)) {
            $email = get_post_meta($post_id, 'email', true);
        }
        
        // Get procurement_id and procurement title
        $procurement_id = '';
        if (function_exists('get_field')) {
            $procurement_id = get_field('procurement_id', $post_id);
        }
        if (empty($procurement_id)) {
            $procurement_id = get_post_meta($post_id, 'procurement_id', true);
        }
        
        $procurement_title = '';
        if (!empty($procurement_id) && get_post($procurement_id)) {
            $procurement_title = get_the_title($procurement_id);
        }
        
        // Get post date
        $post_date = get_the_date('Y-m-d H:i:s', $post_id);
        
        // Write row
        fputcsv($output, array(
            $post_id,
            $email,
            $procurement_title,
            $post_date
        ));
        }

        wp_reset_postdata();
        $paged++;
    } while (true);
    
    // Close output stream
    fclose($output);
    
    // Exit immediately to prevent WordPress from outputting anything else
    exit;
}

/**
 * Add custom column for verification_code in survey_submissions post type
 */
add_filter('manage_survey_submissions_posts_columns', 'omsar_add_survey_submissions_verification_code_column');
function omsar_add_survey_submissions_verification_code_column($columns) {
    // Add the column before the date column
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'date') {
            $new_columns['verification_code'] = __('Verification Code', 'omsar');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

/**
 * Display the verification_code column content
 */
add_action('manage_survey_submissions_posts_custom_column', 'omsar_display_survey_submissions_verification_code_column', 10, 2);
function omsar_display_survey_submissions_verification_code_column($column, $post_id) {
    if ($column === 'verification_code') {
        $verification_code = '';
        if (function_exists('get_field')) {
            $verification_code = get_field('verification_code', $post_id);
        }
        if (empty($verification_code)) {
            $verification_code = get_post_meta($post_id, 'verification_code', true);
        }
        
        if (!empty($verification_code)) {
            echo '<code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 12px;">' . esc_html($verification_code) . '</code>';
        } else {
            echo '—';
        }
    }
}

/**
 * Make the verification_code column sortable
 */
add_filter('manage_edit-survey_submissions_sortable_columns', 'omsar_make_survey_submissions_verification_code_column_sortable');
function omsar_make_survey_submissions_verification_code_column_sortable($columns) {
    $columns['verification_code'] = 'verification_code';
    return $columns;
}

/**
 * Handle sorting for verification_code column
 */
add_action('pre_get_posts', 'omsar_survey_submissions_verification_code_column_orderby');
function omsar_survey_submissions_verification_code_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('post_type') !== 'survey_submissions') {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby === 'verification_code') {
        $query->set('meta_key', 'verification_code');
        $query->set('orderby', 'meta_value');
    }
}

/**
 * Add filter dropdown for survey_submissions to filter by verification code
 */
add_action('restrict_manage_posts', 'omsar_add_survey_submissions_verification_code_filter');
function omsar_add_survey_submissions_verification_code_filter($post_type) {
    // Only add filter for survey_submissions post type
    if ($post_type !== 'survey_submissions') {
        return;
    }
    
    // Get all unique verification codes from survey submissions
    global $wpdb;
    $verification_codes = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} 
        WHERE meta_key = %s AND meta_value != '' 
        ORDER BY meta_value ASC",
        'verification_code'
    ));
    
    // Get selected verification code from URL
    $selected_code = isset($_GET['filter_verification_code']) ? sanitize_text_field($_GET['filter_verification_code']) : '';
    
    // Output dropdown
    echo '<select name="filter_verification_code" id="filter_verification_code">';
    echo '<option value="">' . __('All Verification Codes', 'omsar') . '</option>';
    
    foreach ($verification_codes as $code) {
        if (!empty($code)) {
            $selected = ($selected_code === $code) ? 'selected="selected"' : '';
            echo '<option value="' . esc_attr($code) . '" ' . $selected . '>' . esc_html($code) . '</option>';
        }
    }
    
    echo '</select>';
}

/**
 * Filter survey_submissions posts by selected verification code
 */
add_action('pre_get_posts', 'omsar_filter_survey_submissions_by_verification_code');
function omsar_filter_survey_submissions_by_verification_code($query) {
    // Only in admin and for main query
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Only for survey_submissions post type
    if ($query->get('post_type') !== 'survey_submissions') {
        return;
    }
    
    // Check if filter is set
    $filter_code = isset($_GET['filter_verification_code']) ? sanitize_text_field($_GET['filter_verification_code']) : '';
    
    if (!empty($filter_code)) {
        // Add meta query to filter by verification_code
        $meta_query = $query->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }

        $meta_query[] = array(
            'key' => 'verification_code',
            'value' => $filter_code,
            'compare' => '='
        );

        $query->set('meta_query', $meta_query);
    }
}

/**
 * Add export button to survey_submissions admin page
 */
add_filter('views_edit-survey_submissions', 'omsar_add_survey_submissions_export_button');
function omsar_add_survey_submissions_export_button($views) {
    // Check if user has access
    if (!function_exists('omsar_user_can_access_survey_submissions') || !omsar_user_can_access_survey_submissions()) {
        return $views;
    }
    
    // Build export URL with current filters and nonce
    $export_url = admin_url('admin.php');
    $export_url = add_query_arg('page', 'export_survey_submissions', $export_url);
    $export_url = add_query_arg('_wpnonce', wp_create_nonce('export_survey_submissions'), $export_url);
    
    $filter_code = isset($_GET['filter_verification_code']) ? sanitize_text_field($_GET['filter_verification_code']) : '';
    if (!empty($filter_code)) {
        $export_url = add_query_arg('filter_verification_code', $filter_code, $export_url);
    }
    
    // Add export button
    $views['export'] = '<a href="' . esc_url($export_url) . '" class="button" style="margin-left: 10px;">' . __('Export to CSV', 'omsar') . '</a>';
    
    return $views;
}

/**
 * Handle export request for survey_submissions posts
 * Intercept early to prevent WordPress from outputting HTML
 */
add_action('admin_init', 'omsar_handle_survey_submissions_export');
function omsar_handle_survey_submissions_export() {
    // Check if this is an export request
    if (!isset($_GET['page']) || $_GET['page'] !== 'export_survey_submissions') {
        return;
    }
    
    // Check if user has access
    if (!function_exists('omsar_user_can_access_survey_submissions') || !omsar_user_can_access_survey_submissions()) {
        wp_die(__('You do not have permission to export Survey Submissions.', 'omsar'));
    }
    
    // Check nonce for security
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'export_survey_submissions')) {
        wp_die(__('Security check failed.', 'omsar'));
    }
    
    // Call export function
    omsar_export_survey_submissions_csv();
}

/**
 * Register admin menu page (for URL routing)
 */
add_action('admin_menu', 'omsar_add_survey_submissions_export_page');
function omsar_add_survey_submissions_export_page() {
    add_submenu_page(
        null, // Don't add to menu
        __('Export Survey Submissions', 'omsar'),
        __('Export Survey Submissions', 'omsar'),
        'edit_posts',
        'export_survey_submissions',
        '__return_empty_string' // Empty callback since we handle it in admin_init
    );
}

/**
 * Export survey_submissions posts to CSV
 */
function omsar_export_survey_submissions_csv() {
    // Clear all output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Disable error reporting to prevent any notices/warnings from appearing
    @ini_set('display_errors', 0);
    @set_time_limit(0);
    
    // Set headers for CSV download - must be before any output
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="survey_submissions_' . date('Y-m-d_His') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8 (helps Excel recognize UTF-8)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add CSV headers
    fputcsv($output, array(
        'ID',
        'Submission Date',
        'Administration Name',
        'Number of Properties',
        'Full Name',
        'Job Title',
        'Phone',
        'Email',
        'Verification Code',
        'Property #',
        'Property Governorate',
        'Property District',
        'Property Area',
        'Property Number',
        'Section Number',
        'Area (sqm)',
        'Ownership Type',
        'First Lease Date',
        'Contract Start Date',
        'Contract End Date',
        'Lease Value',
        'Exchange Rate',
        'Price per sqm',
        'Certificate URL'
    ));
    
    // Add data rows in batches to avoid memory/time issues with large datasets
    $posts_per_page = 500;
    $paged = 1;

    do {
        $args = array(
            'post_type'      => 'survey_submissions',
            'post_status'    => 'any',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        );

        // Apply filter if set
        $filter_code = isset($_GET['filter_verification_code']) ? sanitize_text_field($_GET['filter_verification_code']) : '';
        if (!empty($filter_code)) {
            $args['meta_query'] = array(
                array(
                    'key'   => 'verification_code',
                    'value' => $filter_code,
                    'compare' => '=',
                ),
            );
        }

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            break;
        }

        foreach ($query->posts as $post_id) {
        
        // Get general information
        $administration_name = '';
        $number_of_properties = 0;
        if (function_exists('get_field')) {
            $administration_name = get_field('administration_name', $post_id);
            $number_of_properties = get_field('number_of_properties', $post_id);
        }
        if (empty($administration_name)) {
            $administration_name = get_post_meta($post_id, 'administration_name', true);
        }
        if (empty($number_of_properties)) {
            $number_of_properties = get_post_meta($post_id, 'number_of_properties', true);
        }
        
        // Get survey filler information
        $full_name = '';
        $job_title = '';
        $phone = '';
        $email = '';
        if (function_exists('get_field')) {
            $full_name = get_field('survey_full_name', $post_id);
            $job_title = get_field('survey_job_title', $post_id);
            $phone = get_field('survey_phone', $post_id);
            $email = get_field('survey_email', $post_id);
        }
        if (empty($full_name)) {
            $full_name = get_post_meta($post_id, 'survey_full_name', true);
        }
        if (empty($job_title)) {
            $job_title = get_post_meta($post_id, 'survey_job_title', true);
        }
        if (empty($phone)) {
            $phone = get_post_meta($post_id, 'survey_phone', true);
        }
        if (empty($email)) {
            $email = get_post_meta($post_id, 'survey_email', true);
        }
        
        // Get verification code
        $verification_code = '';
        if (function_exists('get_field')) {
            $verification_code = get_field('verification_code', $post_id);
        }
        if (empty($verification_code)) {
            $verification_code = get_post_meta($post_id, 'verification_code', true);
        }
        
        // Get post date
        $post_date = get_the_date('Y-m-d H:i:s', $post_id);
        
        // Get properties
        $properties = array();
        if (function_exists('get_field')) {
            $properties = get_field('properties', $post_id);
        }
        if (empty($properties) || !is_array($properties)) {
            // Try to get from post meta as fallback
            $properties_meta = get_post_meta($post_id, 'properties', true);
            if (is_array($properties_meta)) {
                $properties = $properties_meta;
            }
        }
        
        // If no properties, write one row with general info
        if (empty($properties) || !is_array($properties)) {
            fputcsv($output, array(
                $post_id,
                $post_date,
                $administration_name,
                $number_of_properties,
                $full_name,
                $job_title,
                $phone,
                $email,
                $verification_code,
                '', // Property #
                '', // Property Governorate
                '', // Property District
                '', // Property Area
                '', // Property Number
                '', // Section Number
                '', // Area (sqm)
                '', // Ownership Type
                '', // First Lease Date
                '', // Contract Start Date
                '', // Contract End Date
                '', // Lease Value
                '', // Exchange Rate
                '', // Price per sqm
                ''  // Certificate URL
            ));
        } else {
            // Write one row per property
            foreach ($properties as $index => $property) {
                $property_num = $index + 1;
                
                // Get property fields (handle both ACF field names and direct array keys)
                $governorate = isset($property['property_governorate']) ? $property['property_governorate'] : (isset($property['governorate']) ? $property['governorate'] : '');
                $district = isset($property['property_district']) ? $property['property_district'] : (isset($property['district']) ? $property['district'] : '');
                $area = isset($property['property_area']) ? $property['property_area'] : (isset($property['area']) ? $property['area'] : '');
                $property_number = isset($property['property_number']) ? $property['property_number'] : '';
                $section_number = isset($property['property_section_number']) ? $property['property_section_number'] : (isset($property['section_number']) ? $property['section_number'] : '');
                $area_sqm = isset($property['property_area_sqm']) ? $property['property_area_sqm'] : (isset($property['area_sqm']) ? $property['area_sqm'] : '');
                $ownership_type = isset($property['property_ownership_type']) ? $property['property_ownership_type'] : (isset($property['ownership_type']) ? $property['ownership_type'] : '');
                $first_lease_date = isset($property['property_first_lease_date']) ? $property['property_first_lease_date'] : (isset($property['first_lease_date']) ? $property['first_lease_date'] : '');
                $contract_start = isset($property['property_contract_start']) ? $property['property_contract_start'] : (isset($property['contract_start']) ? $property['contract_start'] : '');
                $contract_end = isset($property['property_contract_end']) ? $property['property_contract_end'] : (isset($property['contract_end']) ? $property['contract_end'] : '');
                $lease_value = isset($property['property_lease_value']) ? $property['property_lease_value'] : (isset($property['lease_value']) ? $property['lease_value'] : '');
                $exchange_rate = isset($property['property_exchange_rate']) ? $property['property_exchange_rate'] : (isset($property['exchange_rate']) ? $property['exchange_rate'] : '');
                $price_per_sqm = isset($property['property_price_per_sqm']) ? $property['property_price_per_sqm'] : (isset($property['price_per_sqm']) ? $property['price_per_sqm'] : '');
                
                // Get certificate URL if available
                $certificate_url = '';
                $certificate_id = isset($property['property_certificate']) ? $property['property_certificate'] : '';
                if (!empty($certificate_id)) {
                    if (is_numeric($certificate_id)) {
                        $certificate_url = wp_get_attachment_url($certificate_id);
                    } elseif (is_array($certificate_id) && isset($certificate_id['ID'])) {
                        $certificate_url = wp_get_attachment_url($certificate_id['ID']);
                    }
                }
                
                // Write row with property data
                fputcsv($output, array(
                    $post_id,
                    $post_date,
                    $administration_name,
                    $number_of_properties,
                    $full_name,
                    $job_title,
                    $phone,
                    $email,
                    $verification_code,
                    $property_num,
                    $governorate,
                    $district,
                    $area,
                    $property_number,
                    $section_number,
                    $area_sqm,
                    $ownership_type,
                    $first_lease_date,
                    $contract_start,
                    $contract_end,
                    $lease_value,
                    $exchange_rate,
                    $price_per_sqm,
                    $certificate_url
                ));
            }
        }

        }

        wp_reset_postdata();
        $paged++;
    } while (true);
    
    // Close output stream
    fclose($output);
    
    // Exit immediately to prevent WordPress from outputting anything else
    exit;
}
