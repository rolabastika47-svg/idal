<?php
/**
 * Recruitment Subscribers Access Control
 * 
 * Restricts access to the 'recruitment_sub' custom post type based on user meta.
 * Only users with the 'can_access_recruitment_sub' user meta enabled can:
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
 * Helper function to check if current user has access to recruitment_sub post type
 * 
 * @return bool True if user has access, false otherwise
 */
function omsar_user_can_access_recruitment_sub() {
    // Super admins and administrators always have access
    if (current_user_can('manage_options')) {
        return true;
    }
    
    // Check user meta for access permission
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    $can_access = get_user_meta($user_id, 'can_access_recruitment_sub', true);
    return !empty($can_access) && $can_access === '1';
}

/**
 * Add checkbox field to Add User form
 * Only shows capabilities that the current user already has.
 */
add_action('user_new_form', 'omsar_add_recruitment_sub_access_checkbox');
function omsar_add_recruitment_sub_access_checkbox() {
    $show_recruitment = omsar_user_can_access_recruitment_sub();
    $show_breadcrumb  = function_exists('omsar_user_can_access_breadcrumb_settings') && omsar_user_can_access_breadcrumb_settings();
    if (!$show_recruitment && !$show_breadcrumb) {
        return;
    }
    ?>
    <table class="form-table">
        <?php if ($show_recruitment) : ?>
        <tr>
            <th scope="row">
                <label for="can_access_recruitment_sub"><?php esc_html_e('Access to Recruitment Subscribers', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_recruitment_sub">
                    <input type="checkbox" name="can_access_recruitment_sub" id="can_access_recruitment_sub" value="1" />
                    <?php esc_html_e('Allow this user to access Recruitment Subscribers post type', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to view, create, and edit Recruitment Subscriber posts.', 'omsar'); ?>
                </p>
            </td>
        </tr>
        <?php endif; ?>
        <?php if ($show_breadcrumb) : ?>
        <tr>
            <th scope="row">
                <label for="can_access_breadcrumb_settings"><?php esc_html_e('Access to Breadcrumb Settings', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_breadcrumb_settings">
                    <input type="checkbox" name="can_access_breadcrumb_settings" id="can_access_breadcrumb_settings" value="1" />
                    <?php esc_html_e('Allow this user to access Breadcrumb Settings page', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to access and modify breadcrumb settings.', 'omsar'); ?>
                </p>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}

/**
 * Add checkbox field to Edit User form
 * Only shows capabilities that the current user already has. Never shown when editing own profile.
 */
add_action('show_user_profile', 'omsar_add_recruitment_sub_access_checkbox_edit');
add_action('edit_user_profile', 'omsar_add_recruitment_sub_access_checkbox_edit');
function omsar_add_recruitment_sub_access_checkbox_edit($user) {
    // Users cannot modify their own capabilities
    if ((int) $user->ID === (int) get_current_user_id()) {
        return;
    }
    // Don't show for super admins/admins (they always have access; no need to show our caps)
    if (current_user_can('manage_options') && user_can($user->ID, 'manage_options')) {
        return;
    }
    $show_recruitment = omsar_user_can_access_recruitment_sub();
    $show_breadcrumb  = function_exists('omsar_user_can_access_breadcrumb_settings') && omsar_user_can_access_breadcrumb_settings();
    if (!$show_recruitment && !$show_breadcrumb) {
        return;
    }
    $can_access_recruitment = get_user_meta($user->ID, 'can_access_recruitment_sub', true);
    $can_access_breadcrumb  = get_user_meta($user->ID, 'can_access_breadcrumb_settings', true);
    ?>
    <h2><?php esc_html_e('Access Permissions', 'omsar'); ?></h2>
    <table class="form-table">
        <?php if ($show_recruitment) : ?>
        <tr>
            <th scope="row">
                <label for="can_access_recruitment_sub"><?php esc_html_e('Access to Recruitment Subscribers', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_recruitment_sub">
                    <input type="checkbox" name="can_access_recruitment_sub" id="can_access_recruitment_sub" value="1" <?php checked($can_access_recruitment, '1'); ?> />
                    <?php esc_html_e('Allow this user to access Recruitment Subscribers post type', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to view, create, and edit Recruitment Subscriber posts.', 'omsar'); ?>
                </p>
            </td>
        </tr>
        <?php endif; ?>
        <?php if ($show_breadcrumb) : ?>
        <tr>
            <th scope="row">
                <label for="can_access_breadcrumb_settings"><?php esc_html_e('Access to Breadcrumb Settings', 'omsar'); ?></label>
            </th>
            <td>
                <label for="can_access_breadcrumb_settings">
                    <input type="checkbox" name="can_access_breadcrumb_settings" id="can_access_breadcrumb_settings" value="1" <?php checked($can_access_breadcrumb, '1'); ?> />
                    <?php esc_html_e('Allow this user to access Breadcrumb Settings page', 'omsar'); ?>
                </label>
                <p class="description">
                    <?php esc_html_e('When enabled, this user will be able to access and modify breadcrumb settings.', 'omsar'); ?>
                </p>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}

/**
 * Save checkbox value when creating a new user
 * Only saves capabilities that the current user has (cannot grant caps one doesn't have).
 */
add_action('user_register', 'omsar_save_recruitment_sub_access_checkbox');
function omsar_save_recruitment_sub_access_checkbox($user_id) {
    if (omsar_user_can_access_recruitment_sub()) {
        if (isset($_POST['can_access_recruitment_sub']) && $_POST['can_access_recruitment_sub'] === '1') {
            update_user_meta($user_id, 'can_access_recruitment_sub', '1');
        } else {
            update_user_meta($user_id, 'can_access_recruitment_sub', '0');
        }
    } else {
        update_user_meta($user_id, 'can_access_recruitment_sub', '0');
    }

    if (function_exists('omsar_user_can_access_breadcrumb_settings') && omsar_user_can_access_breadcrumb_settings()) {
        if (isset($_POST['can_access_breadcrumb_settings']) && $_POST['can_access_breadcrumb_settings'] === '1') {
            update_user_meta($user_id, 'can_access_breadcrumb_settings', '1');
        } else {
            update_user_meta($user_id, 'can_access_breadcrumb_settings', '0');
        }
    } else {
        update_user_meta($user_id, 'can_access_breadcrumb_settings', '0');
    }
}

/**
 * Save checkbox value when updating a user
 * Users cannot change their own capabilities. Only capabilities the current user has are saved.
 */
add_action('personal_options_update', 'omsar_save_recruitment_sub_access_checkbox_update');
add_action('edit_user_profile_update', 'omsar_save_recruitment_sub_access_checkbox_update');
function omsar_save_recruitment_sub_access_checkbox_update($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    // Users cannot modify their own capabilities
    if ((int) $user_id === (int) get_current_user_id()) {
        return;
    }
    if (omsar_user_can_access_recruitment_sub()) {
        if (isset($_POST['can_access_recruitment_sub']) && $_POST['can_access_recruitment_sub'] === '1') {
            update_user_meta($user_id, 'can_access_recruitment_sub', '1');
        } else {
            update_user_meta($user_id, 'can_access_recruitment_sub', '0');
        }
    }
    if (function_exists('omsar_user_can_access_breadcrumb_settings') && omsar_user_can_access_breadcrumb_settings()) {
        if (isset($_POST['can_access_breadcrumb_settings']) && $_POST['can_access_breadcrumb_settings'] === '1') {
            update_user_meta($user_id, 'can_access_breadcrumb_settings', '1');
        } else {
            update_user_meta($user_id, 'can_access_breadcrumb_settings', '0');
        }
    }
}

/**
 * Hide 'recruitment_sub' post type from admin menu for users without access
 */
add_action('admin_menu', 'omsar_remove_recruitment_sub_menu_for_unauthorized_users', 999);
function omsar_remove_recruitment_sub_menu_for_unauthorized_users() {
    // Check if user has access
    if (!omsar_user_can_access_recruitment_sub()) {
        // Remove the menu item
        remove_menu_page('edit.php?post_type=recruitment_sub');
    }
}

/**
 * Restrict access to 'recruitment_sub' posts via direct URL
 * This prevents users from accessing posts even if they know the URL
 */
add_action('admin_init', 'omsar_restrict_recruitment_sub_access');
function omsar_restrict_recruitment_sub_access() {
    // Only check in admin area
    if (!is_admin()) {
        return;
    }
    
    // Check URL parameter first (catches direct URL access)
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'recruitment_sub') {
        if (!omsar_user_can_access_recruitment_sub()) {
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
    
    // Check if we're on a recruitment_sub post type page
    if ($screen->post_type === 'recruitment_sub') {
        // Check if user has access
        if (!omsar_user_can_access_recruitment_sub()) {
            // Show error message
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
    
    // Also check for AJAX requests related to recruitment_sub
    if (defined('DOING_AJAX') && DOING_AJAX) {
        // Check if this is a recruitment_sub related AJAX request
        if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'recruitment_sub') {
            if (!omsar_user_can_access_recruitment_sub()) {
                wp_send_json_error(array(
                    'message' => __('You are not allowed to access this page.', 'omsar')
                ));
            }
        }
        
        // Check if this is a post edit request for recruitment_sub
        if (isset($_REQUEST['post']) && is_numeric($_REQUEST['post'])) {
            $post_id = intval($_REQUEST['post']);
            $post = get_post($post_id);
            if ($post && $post->post_type === 'recruitment_sub') {
                if (!omsar_user_can_access_recruitment_sub()) {
                    wp_send_json_error(array(
                        'message' => __('You are not allowed to access this page.', 'omsar')
                    ));
                }
            }
        }
    }
}

/**
 * Prevent users without access from creating new recruitment_sub posts
 * This handles the "Add New" button and direct post creation attempts
 */
add_action('load-post-new.php', 'omsar_restrict_recruitment_sub_new_post');
function omsar_restrict_recruitment_sub_new_post() {
    // Check if we're trying to create a new recruitment_sub post
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'recruitment_sub') {
        if (!omsar_user_can_access_recruitment_sub()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}

/**
 * Restrict access via REST API for recruitment_sub post type
 * This prevents unauthorized access through the WordPress REST API
 */
add_filter('rest_pre_dispatch', 'omsar_restrict_recruitment_sub_rest_api', 10, 3);
function omsar_restrict_recruitment_sub_rest_api($result, $server, $request) {
    // Get the route from the request
    $route = $request->get_route();
    
    // Check if this is a recruitment_sub related REST API request
    if (strpos($route, '/wp/v2/recruitment_sub') !== false || 
        strpos($route, '/wp/v2/recruitment-sub') !== false) {
        
        // Check if user has access
        if (!omsar_user_can_access_recruitment_sub()) {
            return new WP_Error(
                'rest_cannot_access',
                __('You do not have permission to access Recruitment Subscribers.', 'omsar'),
                array('status' => 403)
            );
        }
    }
    
    return $result;
}

/**
 * Filter query to prevent unauthorized users from seeing recruitment_sub posts in admin
 * This adds an extra layer of protection
 */
add_action('pre_get_posts', 'omsar_filter_recruitment_sub_admin_queries');
function omsar_filter_recruitment_sub_admin_queries($query) {
    // Only apply in admin area
    if (!is_admin()) {
        return;
    }
    
    // Only apply to main query
    if (!$query->is_main_query()) {
        return;
    }
    
    // Check if querying recruitment_sub post type
    $post_type = $query->get('post_type');
    if ($post_type === 'recruitment_sub' || (is_array($post_type) && in_array('recruitment_sub', $post_type))) {
        // Check if user has access
        if (!omsar_user_can_access_recruitment_sub()) {
            // Set query to return no posts
            $query->set('post__in', array(0));
        }
    }
}

/**
 * Remove "Add New" button and other action links for unauthorized users
 * This provides a cleaner UI experience
 */
add_filter('post_row_actions', 'omsar_remove_recruitment_sub_row_actions', 10, 2);
function omsar_remove_recruitment_sub_row_actions($actions, $post) {
    if ($post->post_type === 'recruitment_sub' && !omsar_user_can_access_recruitment_sub()) {
        // Remove all actions
        return array();
    }
    return $actions;
}

/**
 * Prevent unauthorized users from accessing recruitment_sub via admin bar
 */
add_action('admin_bar_menu', 'omsar_remove_recruitment_sub_admin_bar', 999);
function omsar_remove_recruitment_sub_admin_bar($wp_admin_bar) {
    if (!omsar_user_can_access_recruitment_sub()) {
        // Remove "New" menu item for recruitment_sub
        $wp_admin_bar->remove_node('new-recruitment_sub');
    }
}

/**
 * Restrict frontend access to recruitment_sub posts for unauthorized users
 * This prevents direct URL access from the frontend
 */
add_action('template_redirect', 'omsar_restrict_recruitment_sub_frontend_access');
function omsar_restrict_recruitment_sub_frontend_access() {
    // Only check on frontend
    if (is_admin()) {
        return;
    }
    
    // Check if viewing a recruitment_sub post
    if (is_singular('recruitment_sub')) {
        // Check if user has access
        if (!omsar_user_can_access_recruitment_sub()) {
            // Show 403 error
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
    
    // Also check for archive pages
    if (is_post_type_archive('recruitment_sub')) {
        if (!omsar_user_can_access_recruitment_sub()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}
