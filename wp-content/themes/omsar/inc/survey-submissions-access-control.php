<?php
/**
 * Survey Submissions Access Control
 * 
 * Restricts access to the 'survey_submissions' custom post type based on user meta.
 * Only users with the 'can_access_survey_submissions' user meta enabled can:
 * - See the post type in the admin menu
 * - Access or edit posts directly via URL
 * - Create or edit posts of this type
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Helper function to check if current user has access to survey_submissions post type
 * 
 * @return bool True if user has access, false otherwise
 */
function omsar_user_can_access_survey_submissions() {
    // Super admins and administrators always have access
    if (current_user_can('manage_options')) {
        return true;
    }
    
    // Check user meta for access permission
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    $can_access = get_user_meta($user_id, 'can_access_survey_submissions', true);
    return !empty($can_access) && $can_access === '1';
}

/**
 * Add checkbox field to Add User form
 * Only shows if the current user has Access to Survey Submissions.
 */
add_action('user_new_form', 'omsar_add_survey_submissions_access_checkbox');
function omsar_add_survey_submissions_access_checkbox() {
    if (!omsar_user_can_access_survey_submissions()) {
        return;
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="can_access_survey_submissions"><?php esc_html_e('Access to Survey Submissions', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_survey_submissions">
                    <input type="checkbox" name="can_access_survey_submissions" id="can_access_survey_submissions" value="1" />
                    <?php esc_html_e('Allow this user to access Survey Submissions post type', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to view, create, and edit Survey Submission posts.', 'omsar'); ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Add checkbox field to Edit User form
 * Only shows if the current user has the cap. Never shown when editing own profile.
 */
add_action('show_user_profile', 'omsar_add_survey_submissions_access_checkbox_edit');
add_action('edit_user_profile', 'omsar_add_survey_submissions_access_checkbox_edit');
function omsar_add_survey_submissions_access_checkbox_edit($user) {
    // Users cannot modify their own capabilities
    if ((int) $user->ID === (int) get_current_user_id()) {
        return;
    }
    // Don't show for super admins/admins (they always have access)
    if (current_user_can('manage_options') && user_can($user->ID, 'manage_options')) {
        return;
    }
    if (!omsar_user_can_access_survey_submissions()) {
        return;
    }
    $can_access = get_user_meta($user->ID, 'can_access_survey_submissions', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="can_access_survey_submissions"><?php esc_html_e('Access to Survey Submissions', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_survey_submissions">
                    <input type="checkbox" name="can_access_survey_submissions" id="can_access_survey_submissions" value="1" <?php checked($can_access, '1'); ?> />
                    <?php esc_html_e('Allow this user to access Survey Submissions post type', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to view, create, and edit Survey Submission posts.', 'omsar'); ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save checkbox value when creating a new user
 * Only saves if the current user has Access to Survey Submissions (cannot grant cap one doesn't have).
 */
add_action('user_register', 'omsar_save_survey_submissions_access_checkbox');
function omsar_save_survey_submissions_access_checkbox($user_id) {
    if (omsar_user_can_access_survey_submissions()) {
        if (isset($_POST['can_access_survey_submissions']) && $_POST['can_access_survey_submissions'] === '1') {
            update_user_meta($user_id, 'can_access_survey_submissions', '1');
        } else {
            update_user_meta($user_id, 'can_access_survey_submissions', '0');
        }
    } else {
        update_user_meta($user_id, 'can_access_survey_submissions', '0');
    }
}

/**
 * Save checkbox value when updating a user
 * Users cannot change their own capabilities. Only saves if current user has the cap.
 */
add_action('personal_options_update', 'omsar_save_survey_submissions_access_checkbox_update');
add_action('edit_user_profile_update', 'omsar_save_survey_submissions_access_checkbox_update');
function omsar_save_survey_submissions_access_checkbox_update($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    // Users cannot modify their own capabilities
    if ((int) $user_id === (int) get_current_user_id()) {
        return;
    }
    if (!omsar_user_can_access_survey_submissions()) {
        return;
    }
    if (isset($_POST['can_access_survey_submissions']) && $_POST['can_access_survey_submissions'] === '1') {
        update_user_meta($user_id, 'can_access_survey_submissions', '1');
    } else {
        update_user_meta($user_id, 'can_access_survey_submissions', '0');
    }
}

/**
 * Hide 'survey_submissions' post type from admin menu for users without access
 */
add_action('admin_menu', 'omsar_remove_survey_submissions_menu_for_unauthorized_users', 999);
function omsar_remove_survey_submissions_menu_for_unauthorized_users() {
    // Check if user has access
    if (!omsar_user_can_access_survey_submissions()) {
        // Remove the menu item
        remove_menu_page('edit.php?post_type=survey_submissions');
    }
}

/**
 * Restrict access to 'survey_submissions' posts via direct URL
 * This prevents users from accessing posts even if they know the URL
 */
add_action('admin_init', 'omsar_restrict_survey_submissions_access');
function omsar_restrict_survey_submissions_access() {
    // Only check in admin area
    if (!is_admin()) {
        return;
    }
    
    // Check URL parameter first (catches direct URL access)
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'survey_submissions') {
        if (!omsar_user_can_access_survey_submissions()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
    
    // Get current screen
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    
    // Check if we're on a survey_submissions post type page
    if ($screen->post_type === 'survey_submissions') {
        // Check if user has access
        if (!omsar_user_can_access_survey_submissions()) {
            // Show error message
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
    
    // Also check for AJAX requests related to survey_submissions
    if (defined('DOING_AJAX') && DOING_AJAX) {
        // Check if this is a survey_submissions related AJAX request
        if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'survey_submissions') {
            if (!omsar_user_can_access_survey_submissions()) {
                wp_send_json_error(array(
                    'message' => __('You are not allowed to access this page.', 'omsar')
                ));
            }
        }
        
        // Check if this is a post edit request for survey_submissions
        if (isset($_REQUEST['post']) && is_numeric($_REQUEST['post'])) {
            $post_id = intval($_REQUEST['post']);
            $post = get_post($post_id);
            if ($post && $post->post_type === 'survey_submissions') {
                if (!omsar_user_can_access_survey_submissions()) {
                    wp_send_json_error(array(
                        'message' => __('You are not allowed to access this page.', 'omsar')
                    ));
                }
            }
        }
    }
}

/**
 * Prevent users without access from creating new survey_submissions posts
 * This handles the "Add New" button and direct post creation attempts
 */
add_action('load-post-new.php', 'omsar_restrict_survey_submissions_new_post');
function omsar_restrict_survey_submissions_new_post() {
    // Check if we're trying to create a new survey_submissions post
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'survey_submissions') {
        if (!omsar_user_can_access_survey_submissions()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}

/**
 * Restrict access via REST API for survey_submissions post type
 * This prevents unauthorized access through the WordPress REST API
 */
add_filter('rest_pre_dispatch', 'omsar_restrict_survey_submissions_rest_api', 10, 3);
function omsar_restrict_survey_submissions_rest_api($result, $server, $request) {
    // Get the route from the request
    $route = $request->get_route();
    
    // Check if this is a survey_submissions related REST API request
    if (strpos($route, '/wp/v2/survey_submissions') !== false || 
        strpos($route, '/wp/v2/survey-submissions') !== false) {
        
        // Check if user has access
        if (!omsar_user_can_access_survey_submissions()) {
            return new WP_Error(
                'rest_cannot_access',
                __('You do not have permission to access Survey Submissions.', 'omsar'),
                array('status' => 403)
            );
        }
    }
    
    return $result;
}

/**
 * Filter query to prevent unauthorized users from seeing survey_submissions posts in admin
 * This adds an extra layer of protection
 */
add_action('pre_get_posts', 'omsar_filter_survey_submissions_admin_queries');
function omsar_filter_survey_submissions_admin_queries($query) {
    // Only apply in admin area
    if (!is_admin()) {
        return;
    }
    
    // Only apply to main query
    if (!$query->is_main_query()) {
        return;
    }
    
    // Check if querying survey_submissions post type
    $post_type = $query->get('post_type');
    if ($post_type === 'survey_submissions' || (is_array($post_type) && in_array('survey_submissions', $post_type))) {
        // Check if user has access
        if (!omsar_user_can_access_survey_submissions()) {
            // Set query to return no posts
            $query->set('post__in', array(0));
        }
    }
}

/**
 * Remove "Add New" button and other action links for unauthorized users
 * This provides a cleaner UI experience
 */
add_filter('post_row_actions', 'omsar_remove_survey_submissions_row_actions', 10, 2);
function omsar_remove_survey_submissions_row_actions($actions, $post) {
    if ($post->post_type === 'survey_submissions' && !omsar_user_can_access_survey_submissions()) {
        // Remove all actions
        return array();
    }
    return $actions;
}

/**
 * Prevent unauthorized users from accessing survey_submissions via admin bar
 */
add_action('admin_bar_menu', 'omsar_remove_survey_submissions_admin_bar', 999);
function omsar_remove_survey_submissions_admin_bar($wp_admin_bar) {
    if (!omsar_user_can_access_survey_submissions()) {
        // Remove "New" menu item for survey_submissions
        $wp_admin_bar->remove_node('new-survey_submissions');
    }
}

/**
 * Restrict frontend access to survey_submissions posts for unauthorized users
 * This prevents direct URL access from the frontend
 */
add_action('template_redirect', 'omsar_restrict_survey_submissions_frontend_access');
function omsar_restrict_survey_submissions_frontend_access() {
    // Only check on frontend
    if (is_admin()) {
        return;
    }
    
    // Check if viewing a survey_submissions post
    if (is_singular('survey_submissions')) {
        // Check if user has access
        if (!omsar_user_can_access_survey_submissions()) {
            // Show 403 error
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
    
    // Also check for archive pages
    if (is_post_type_archive('survey_submissions')) {
        if (!omsar_user_can_access_survey_submissions()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}
