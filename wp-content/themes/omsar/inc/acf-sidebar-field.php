<?php
/**
 * ACF Field Group: Page Settings
 * 
 * Registers ACF field group for sidebar display option on pages
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF field group for page settings
 * This function will be called via acf/init hook
 */
function omsar_register_sidebar_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Check if field group already exists to prevent duplicates
    $existing_groups = acf_get_local_field_groups();
    foreach ($existing_groups as $group) {
        if (isset($group['key']) && $group['key'] === 'group_page_sidebar_settings') {
            // Field group already exists, don't register again
            return;
        }
    }
    
    acf_add_local_field_group(array(
        'key' => 'group_page_sidebar_settings',
        'title' => 'Page Settings',
            'fields' => array(
                array(
                    'key' => 'field_display_sidebar',
                    'label' => 'Display Sidebar',
                    'name' => 'display_sidebar',
                    'type' => 'true_false',
                    'instructions' => 'Enable this option to display the sidebar on this page and its children. If disabled, the sidebar will not appear on this page or any of its child pages.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => 'Display sidebar on this page',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'Yes',
                    'ui_off_text' => 'No',
                ),
                array(
                    'key' => 'field_show_breadcrumb_page',
                    'label' => 'Show Breadcrumb',
                    'name' => 'show_breadcrumb_page',
                    'type' => 'true_false',
                    'instructions' => 'By default, breadcrumbs are hidden on pages. Enable this option to display breadcrumbs on this page.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => 'Show breadcrumb on this page',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'Yes',
                    'ui_off_text' => 'No',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'projects',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
}

// Register the field group when ACF is ready
add_action('acf/init', 'omsar_register_sidebar_acf_fields');


