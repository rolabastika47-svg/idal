<?php
/**
 * Editor Access Control
 * 
 * Manages access permissions for the Editor role.
 * - Menu management: allows Editor role to manage Header navigation menu only.
 * - User management: allows Editor role to list, view, create, and delete users (but not assign or delete Administrator role).
 * 
 * @package OMSAR
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Helper function to check if current user is Editor (and not Administrator)
 * 
 * @return bool True if user is Editor role, false otherwise
 */
function omsar_is_editor_user() {
    $user = wp_get_current_user();
    if (!$user || !$user->exists()) {
        return false;
    }
    
    // Check if user has Editor role but not Administrator role
    return in_array('editor', $user->roles) && !in_array('administrator', $user->roles);
}

/**
 * Grant capabilities to Editor role
 */
function omsar_grant_editor_capabilities() {
    // Get the Editor role
    $editor_role = get_role('editor');
    
    if ($editor_role) {
        // Grant capabilities needed for menu management
        $editor_role->add_cap('edit_theme_options');
        
        // Grant capabilities needed for user management
        $editor_role->add_cap('list_users');
        $editor_role->add_cap('create_users');
        $editor_role->add_cap('edit_users');
        $editor_role->add_cap('delete_users'); // Allow deleting users
        $editor_role->add_cap('promote_users'); // Required to see the role dropdown
    }
}
add_action('admin_init', 'omsar_grant_editor_capabilities');

/**
 * Restrict Editor access to only menu management
 * Remove access to other Appearance submenus
 */
function omsar_restrict_editor_appearance_access() {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Remove all Appearance submenus except Menus
    global $submenu;
    
    if (isset($submenu['themes.php'])) {
        foreach ($submenu['themes.php'] as $key => $menu_item) {
            // Keep only the Menus submenu (nav-menus.php)
            if (strpos($menu_item[2], 'nav-menus.php') === false) {
                unset($submenu['themes.php'][$key]);
            }
        }
    }
    
    // Hide the Appearance menu parent item
    remove_menu_page('themes.php');
}
add_action('admin_menu', 'omsar_restrict_editor_appearance_access', 999);

/**
 * Add custom "Menus" menu item for Editor role
 */
function omsar_add_editor_menu_item() {
    // Only show for Editor role (not administrators)
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Add custom menu page that links to nav-menus.php
    add_menu_page(
        __('Menus', 'omsar'),                    // Page title
        __('Menus', 'omsar'),                    // Menu title
        'edit_theme_options',                    // Capability
        'omsar-editor-menus',                    // Menu slug
        '__return_empty_string',                 // Callback function (empty, we'll redirect)
        'dashicons-menu',                        // Icon
        60                                       // Position (after Appearance)
    );
}
add_action('admin_menu', 'omsar_add_editor_menu_item');

/**
 * Modify the menu URL to point directly to nav-menus.php
 */
function omsar_modify_menu_url() {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    global $menu;
    
    // Find our menu item and change its URL
    foreach ($menu as $key => $menu_item) {
        if (isset($menu_item[2]) && $menu_item[2] === 'omsar-editor-menus') {
            // Change the URL to nav-menus.php
            $menu[$key][2] = 'nav-menus.php';
            break;
        }
    }
}
add_action('admin_menu', 'omsar_modify_menu_url', 9999);

/**
 * Handle direct access to the old menu slug (redirect to nav-menus.php)
 */
function omsar_handle_old_menu_slug() {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // If someone tries to access the old menu slug, redirect to nav-menus.php
    if (isset($_GET['page']) && $_GET['page'] === 'omsar-editor-menus') {
        // Check user capability
        if (!current_user_can('edit_theme_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'omsar'));
        }
        
        // Redirect to nav-menus.php
        wp_redirect(admin_url('nav-menus.php'));
        exit;
    }
}
add_action('admin_init', 'omsar_handle_old_menu_slug');

/**
 * Map meta capabilities for menu management
 * Ensures Editors can manage menus even if they don't have full edit_theme_options
 */
function omsar_map_menu_meta_cap($caps, $cap, $user_id, $args) {
    // Only process for Editor role
    $user = get_userdata($user_id);
    if (!$user || !in_array('editor', $user->roles) || in_array('administrator', $user->roles)) {
        return $caps;
    }
    
    // Map menu-related capabilities
    $menu_caps = array(
        'edit_theme_options',
        'manage_nav_menus',
    );
    
    if (in_array($cap, $menu_caps)) {
        // Only check screen in admin area (get_current_screen() is only available in admin)
        if (is_admin() && function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && (
                $screen->id === 'nav-menus' ||
                $screen->id === 'nav-menus-network' ||
                (isset($_GET['page']) && $_GET['page'] === 'omsar-editor-menus')
            )) {
                // Grant capability for menu management only
                $caps = array('edit_theme_options');
            }
        } elseif (is_admin() && isset($_GET['page']) && $_GET['page'] === 'omsar-editor-menus') {
            // Fallback: check URL parameter if screen is not available yet
            $caps = array('edit_theme_options');
        }
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'omsar_map_menu_meta_cap', 10, 4);

/**
 * Prevent Editor from accessing other Appearance pages directly via URL
 */
function omsar_prevent_editor_appearance_access() {
    // Only run in admin area
    if (!is_admin()) {
        return;
    }
    
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Get current screen (only available in admin)
    if (!function_exists('get_current_screen')) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    
    // List of Appearance submenu pages to block
    $blocked_pages = array(
        'themes',
        'theme-install',
        'theme-editor',
        'customize',
        'widgets',
        'custom-background',
        'custom-header',
    );
    
    // Check if current page is a blocked Appearance page
    foreach ($blocked_pages as $page) {
        if (strpos($screen->id, $page) !== false || 
            (isset($_GET['page']) && strpos($_GET['page'], $page) !== false)) {
            wp_die(
                __('You do not have sufficient permissions to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}
add_action('current_screen', 'omsar_prevent_editor_appearance_access');

/**
 * Restrict editable roles for Editor users to only show Editor role
 * This prevents Editors from seeing or selecting any other roles in the dropdown
 */
function omsar_remove_administrator_from_editable_roles($editable_roles) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return $editable_roles;
    }
    
    // Only keep the Editor role, remove all other roles
    if (isset($editable_roles['editor'])) {
        return array('editor' => $editable_roles['editor']);
    }
    
    // If editor role doesn't exist, return empty array
    return array();
}
add_filter('editable_roles', 'omsar_remove_administrator_from_editable_roles');

/**
 * Intercept POST data early to prevent Administrator role assignment
 * This modifies the POST data before WordPress processes it
 */
function omsar_intercept_admin_role_assignment() {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Only run in admin area and on user edit/create pages
    if (!is_admin() || !isset($_POST['action'])) {
        return;
    }
    
    // Check if we're creating or updating a user
    $is_user_action = (
        (isset($_POST['action']) && $_POST['action'] === 'createuser') ||
        (isset($_POST['action']) && $_POST['action'] === 'update') ||
        (isset($_GET['action']) && $_GET['action'] === 'createuser')
    );
    
    if ($is_user_action && isset($_POST['role']) && $_POST['role'] === 'administrator') {
        // Change Administrator role to Editor
        $_POST['role'] = 'editor';
        
        // Store notice to show later
        add_action('admin_notices', function() {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__('Notice:', 'omsar') . '</strong> ' . esc_html__('You do not have permission to assign the Administrator role. The user has been assigned the Editor role instead.', 'omsar') . '</p></div>';
        });
    }
}
add_action('admin_init', 'omsar_intercept_admin_role_assignment', 1);

/**
 * Validate role assignment before user is created/updated
 * This catches the role assignment and shows validation errors
 */
function omsar_validate_role_before_save($errors, $update, $user) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return $errors;
    }
    
    // Check if Administrator role is being assigned
    if (isset($_POST['role']) && $_POST['role'] === 'administrator') {
        // Change to Editor role
        $_POST['role'] = 'editor';
        
        // Add error message
        $errors->add('admin_role_error', __('You do not have permission to assign the Administrator role. The role has been changed to Editor.', 'omsar'), array('form-field' => 'role'));
    }
    
    return $errors;
}
add_filter('user_profile_update_errors', 'omsar_validate_role_before_save', 10, 3);

/**
 * Final safeguard: Intercept role assignment via set_user_role action
 * This prevents Administrator role even if somehow it gets through
 */
function omsar_prevent_admin_role_on_set($user_id, $role, $old_roles) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // If Administrator role is being assigned, change it
    if ($role === 'administrator') {
        $user = get_userdata($user_id);
        if ($user && !in_array('administrator', $old_roles)) {
            // Only prevent if user didn't already have admin role
            // Remove the action temporarily to avoid infinite loop
            remove_action('set_user_role', 'omsar_prevent_admin_role_on_set', 10);
            $user->set_role('editor');
            add_action('set_user_role', 'omsar_prevent_admin_role_on_set', 10, 3);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__('Notice:', 'omsar') . '</strong> ' . esc_html__('You do not have permission to assign the Administrator role. The user has been assigned the Editor role instead.', 'omsar') . '</p></div>';
            });
        }
    }
}
add_action('set_user_role', 'omsar_prevent_admin_role_on_set', 10, 3);

/**
 * Prevent Editor from deleting Administrator users (safeguard)
 * Since administrators are now hidden from the list, this serves as a final safeguard
 * in case someone tries to delete an admin through other means
 */
function omsar_prevent_editor_delete_administrator($user_id) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Get the user being deleted
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    
    // Check if the user being deleted is an Administrator
    if (in_array('administrator', $user->roles)) {
        wp_die(
            __('You do not have permission to delete Administrator users.', 'omsar'),
            __('Access Denied', 'omsar'),
            array('response' => 403)
        );
    }
}
add_action('delete_user', 'omsar_prevent_editor_delete_administrator', 1);
add_action('wp_delete_user', 'omsar_prevent_editor_delete_administrator', 1);

/**
 * Map delete_users meta capability to allow Editors to delete Editor users
 * This ensures the delete_users capability works properly for Editors
 */
function omsar_map_delete_users_cap($caps, $cap, $user_id, $args) {
    // Only process for Editor role
    if (!omsar_is_editor_user()) {
        return $caps;
    }
    
    // Handle delete_user capability (WordPress uses this for checking if a specific user can be deleted)
    if ($cap === 'delete_user') {
        // If a specific user ID is provided
        if (!empty($args[0])) {
            $target_user_id = absint($args[0]);
            $target_user = get_userdata($target_user_id);
            
            if ($target_user) {
                // Don't allow deleting administrators
                if (in_array('administrator', $target_user->roles)) {
                    $caps = array('do_not_allow');
                } 
                // Don't allow deleting yourself
                elseif ($target_user_id == $user_id) {
                    $caps = array('do_not_allow');
                }
                // Allow deleting editor users (and other non-admin roles)
                else {
                    $caps = array('delete_users');
                }
            } else {
                // User doesn't exist, don't allow
                $caps = array('do_not_allow');
            }
        } else {
            // No user ID provided, require general delete_users capability
            $caps = array('delete_users');
        }
    }
    // Handle delete_users capability (general capability check)
    elseif ($cap === 'delete_users') {
        // Allow general delete_users capability for editors
        $caps = array('delete_users');
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'omsar_map_delete_users_cap', 10, 4);

/**
 * Filter users list to only show Editor users (hide Administrators)
 * This prevents Editors from seeing Administrator users in the users list
 */
function omsar_filter_users_list_for_editors($query) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return;
    }
    
    // Only run in admin area
    if (!is_admin()) {
        return;
    }
    
    // Check if we're on the users page
    // Check screen if available, otherwise check URL
    $is_users_page = false;
    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
        if ($screen && ($screen->id === 'users' || $screen->base === 'users')) {
            $is_users_page = true;
        }
    }
    
    // Fallback: check if we're on users.php
    if (!$is_users_page && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'users.php') !== false) {
        $is_users_page = true;
    }
    
    if (!$is_users_page) {
        return;
    }
    
    // If role is already set to something other than editor, don't override
    // But if it's set to administrator, change it to editor
    $current_role = $query->get('role');
    if ($current_role === 'administrator') {
        $query->set('role', 'editor');
    } elseif (empty($current_role)) {
        // If no role filter is set, only show editors
        $query->set('role', 'editor');
    }
    
    // Always exclude administrators
    $role_not_in = $query->get('role__not_in');
    if (!is_array($role_not_in)) {
        $role_not_in = array();
    }
    if (!in_array('administrator', $role_not_in)) {
        $role_not_in[] = 'administrator';
    }
    $query->set('role__not_in', $role_not_in);
}
add_action('pre_get_users', 'omsar_filter_users_list_for_editors');

/**
 * Ensure delete action appears for Editor users when viewing Editor users
 * WordPress checks if current user can delete the target user, so we need to ensure this works
 * We force add the delete action for editor users since we've granted the capability
 */
function omsar_ensure_delete_action_for_editors($actions, $user_object) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return $actions;
    }
    
    // Remove delete action if user is Administrator (safeguard)
    if (in_array('administrator', $user_object->roles)) {
        unset($actions['delete']);
        return $actions;
    }
    
    // For all non-administrator users, always add delete action (except for self)
    $current_user_id = get_current_user_id();
    $target_user_id = $user_object->ID;
    
    // Don't allow deleting yourself
    if ($current_user_id == $target_user_id) {
        unset($actions['delete']);
        return $actions;
    }
    
    // For any non-administrator user (editors and other roles), force add the delete action
    // WordPress might not show it due to capability checks, so we add it explicitly
    // Since we're filtering the list to only show editors, this will primarily affect editor users
    if (!in_array('administrator', $user_object->roles)) {
        $delete_url = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => 'delete',
                    'user' => $target_user_id
                ),
                admin_url('users.php')
            ),
            'delete-user_' . $target_user_id
        );
        
        // Always add/override the delete action
        $actions['delete'] = '<a href="' . esc_url($delete_url) . '" class="delete">' . __('Delete') . '</a>';
    }
    
    return $actions;
}
add_filter('user_row_actions', 'omsar_ensure_delete_action_for_editors', 20, 2);

/**
 * Filter the users list views to hide Administrator role filter
 * This removes the "Administrator (X)" link from the role filters
 */
function omsar_filter_users_list_views($views) {
    // Only apply to Editor role
    if (!omsar_is_editor_user()) {
        return $views;
    }
    
    // Remove administrator view
    if (isset($views['administrator'])) {
        unset($views['administrator']);
    }
    
    // Get accurate editor count
    $user_count = count_users();
    $total_editors = isset($user_count['avail_roles']['editor']) ? (int)$user_count['avail_roles']['editor'] : 0;
    
    // Update the "All" count to only include editors
    if (isset($views['all'])) {
        // Replace the count in the "All" link
        $views['all'] = preg_replace(
            '/<span class="count">\((\d+)\)<\/span>/',
            '<span class="count">(' . $total_editors . ')</span>',
            $views['all']
        );
        // Also handle format without span
        $views['all'] = preg_replace(
            '/\(\d+\)/',
            '(' . $total_editors . ')',
            $views['all']
        );
    }
    
    // Update editor count if it exists
    if (isset($views['editor'])) {
        $views['editor'] = preg_replace(
            '/<span class="count">\((\d+)\)<\/span>/',
            '<span class="count">(' . $total_editors . ')</span>',
            $views['editor']
        );
        $views['editor'] = preg_replace(
            '/\(\d+\)/',
            '(' . $total_editors . ')',
            $views['editor']
        );
    }
    
    return $views;
}
add_filter('views_users', 'omsar_filter_users_list_views');

/**
 * Clean up: Remove capabilities when plugin/theme is deactivated
 * (Optional - uncomment if you want to remove capabilities on deactivation)
 */
/*
function omsar_remove_editor_capabilities() {
    $editor_role = get_role('editor');
    
    if ($editor_role) {
        $editor_role->remove_cap('edit_theme_options');
        $editor_role->remove_cap('list_users');
        $editor_role->remove_cap('create_users');
        $editor_role->remove_cap('edit_users');
        $editor_role->remove_cap('delete_users');
        $editor_role->remove_cap('promote_users');
    }
}
register_deactivation_hook(__FILE__, 'omsar_remove_editor_capabilities');
*/

// File: editor-hide-tools.php

add_action('admin_menu', function() {
    // Get current user info
    $current_user = wp_get_current_user();

    // Check if the user has the 'editor' role
    if (in_array('editor', $current_user->roles)) {
        // Remove the Tools menu
        remove_menu_page('tools.php');
    }
}, 999); // Use high priority to make sure it runs after menus are added