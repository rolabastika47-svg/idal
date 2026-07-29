<?php
/**
 * Sidebar Helper Functions
 * 
 * Functions to manage page sidebar display
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if sidebar should be displayed on current page
 * 
 * @param int $parent_page_id - The parent page ID that should have sidebar
 * @return bool
 */
function omsar_should_display_sidebar($parent_page_id = null) {
    // Don't display on homepage
    if (is_front_page()) {
        return false;
    }
    
    // Only display on pages
    if (!is_page()) {
        return false;
    }
    
    $current_page_id = get_the_ID();
    
    // If parent_page_id is provided, verify current page belongs to that hierarchy
    // AND verify that the parent has sidebar enabled
    if ($parent_page_id !== null) {
        // First check if current page is in the hierarchy
        if (!omsar_is_page_in_hierarchy($current_page_id, $parent_page_id)) {
            return false;
        }
        
        // Then verify that the parent has sidebar enabled
        if (function_exists('get_field')) {
            $parent_display_sidebar = get_field('display_sidebar', $parent_page_id);
            // Check if parent has sidebar enabled
            if ($parent_display_sidebar === true || $parent_display_sidebar === 1 || $parent_display_sidebar === '1') {
                return true;
            }
        }
        
        // If parent doesn't have sidebar explicitly enabled, don't show
        return false;
    }
    
    // Check ACF field first - if explicitly disabled, don't show sidebar
    if (function_exists('get_field')) {
        // Check current page
        $display_sidebar = get_field('display_sidebar', $current_page_id);
        
        // If explicitly set to false (0 or empty), don't show sidebar
        if ($display_sidebar === false || $display_sidebar === 0 || $display_sidebar === '0') {
            return false;
        }
        
        // Check parent pages - if any parent has sidebar disabled, don't show
        $ancestors = get_post_ancestors($current_page_id);
        foreach ($ancestors as $ancestor_id) {
            $parent_display_sidebar = get_field('display_sidebar', $ancestor_id);
            // If parent explicitly disabled sidebar, don't show on children
            if ($parent_display_sidebar === false || $parent_display_sidebar === 0 || $parent_display_sidebar === '0') {
                return false;
            }
        }
        
        // If explicitly enabled (1 or true), show sidebar
        if ($display_sidebar === true || $display_sidebar === 1 || $display_sidebar === '1') {
            return true;
        }
        
        // Check if any ancestor has sidebar explicitly enabled - inherit it
        if (!empty($ancestors)) {
            foreach ($ancestors as $ancestor_id) {
                $ancestor_display_sidebar = get_field('display_sidebar', $ancestor_id);
                // If ancestor has sidebar enabled, inherit it
                if ($ancestor_display_sidebar === true || $ancestor_display_sidebar === 1 || $ancestor_display_sidebar === '1') {
                    return true;
                }
            }
        }
    }
    
    // Auto-detect: Check if current page or any ancestor has children
    // This allows sidebar to show automatically on pages with children
    // But only if sidebar is not explicitly disabled
    
    // Get the parent page ID for sidebar
    $sidebar_parent_id = omsar_get_sidebar_parent_page_id();
    
    // If we found a parent, check if current page is part of that hierarchy
    if ($sidebar_parent_id) {
        return omsar_is_page_in_hierarchy($current_page_id, $sidebar_parent_id);
    }
    
    // Fallback: Check current page
    $children = get_pages(array('child_of' => $current_page_id));
    if (!empty($children)) {
        return true;
    }
    
    // Fallback: Check if current page is a child and parent has children
    $current_parent = wp_get_post_parent_id($current_page_id);
    if ($current_parent) {
        $siblings = get_pages(array('child_of' => $current_parent));
        if (!empty($siblings)) {
            return true;
        }
    }
    
    // Fallback: Check ancestors - walk up the tree to find any ancestor with children
    $ancestors = get_post_ancestors($current_page_id);
    if (!empty($ancestors)) {
        // Check from topmost parent down (reverse order)
        $ancestors_reversed = array_reverse($ancestors);
        foreach ($ancestors_reversed as $ancestor_id) {
            // Check direct children first (more efficient)
            $direct_children = get_pages(array(
                'parent' => $ancestor_id,
                'number' => 1,
                'post_status' => 'publish'
            ));
            if (!empty($direct_children)) {
                return true;
            }
            // Check all descendants if no direct children found
            $ancestor_children = get_pages(array(
                'child_of' => $ancestor_id,
                'number' => 1,
                'post_status' => 'publish'
            ));
            if (!empty($ancestor_children)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Check if a page is part of a parent's hierarchy (at any level)
 * 
 * @param int $page_id The page ID to check
 * @param int $parent_id The parent page ID
 * @return bool
 */
function omsar_is_page_in_hierarchy($page_id, $parent_id) {
    // Same page
    if ($page_id == $parent_id) {
        return true;
    }
    
    // Method 1: Check if parent is in ancestors (most reliable)
    $ancestors = get_post_ancestors($page_id);
    if (!empty($ancestors) && in_array($parent_id, $ancestors)) {
        return true;
    }
    
    // Method 2: Walk up the parent chain (works for any depth)
    $current_check_id = $page_id;
    $max_depth = 20; // Prevent infinite loops
    $depth = 0;
    
    while ($current_check_id && $depth < $max_depth) {
        $direct_parent = wp_get_post_parent_id($current_check_id);
        if ($direct_parent == $parent_id) {
            return true;
        }
        if (!$direct_parent) {
            break;
        }
        $current_check_id = $direct_parent;
        $depth++;
    }
    
    // Method 3: Check if page is a descendant of parent (at any level)
    // This is a fallback in case the above methods don't work
    $all_descendants = get_pages(array(
        'child_of' => $parent_id,
        'post_status' => 'publish'
    ));
    
    if (!empty($all_descendants)) {
        $descendant_ids = wp_list_pluck($all_descendants, 'ID');
        if (in_array($page_id, $descendant_ids)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get the parent page ID for sidebar
 * This can be set via ACF field or auto-detected from page hierarchy
 * 
 * @return int|false
 */
function omsar_get_sidebar_parent_page_id() {
    $current_page_id = get_the_ID();
    
    // Check if current page has sidebar parent set via ACF
    if (function_exists('get_field')) {
        // Get all ancestors (direct parent first, topmost parent last)
        $ancestors = get_post_ancestors($current_page_id);
        
        // Build list of all pages to check: current page + all ancestors
        $all_pages_to_check = array($current_page_id);
        if (!empty($ancestors)) {
            $all_pages_to_check = array_merge($all_pages_to_check, $ancestors);
        }
        
        // Reverse to check from topmost parent down to current page
        // This ensures we find the topmost parent with sidebar enabled
        $reversed_pages = array_reverse($all_pages_to_check);
        
        // First priority: Find the topmost page (highest in hierarchy) with sidebar enabled
        foreach ($reversed_pages as $page_id_to_check) {
            $page_display_sidebar = get_field('display_sidebar', $page_id_to_check);
            // If this page has sidebar enabled, use it as parent
            // Since we're checking from topmost down, this will be the topmost parent
            if ($page_display_sidebar === true || $page_display_sidebar === 1 || $page_display_sidebar === '1') {
                return $page_id_to_check;
            }
        }
        
        // Second priority: Check for explicit sidebar_parent_page field (from topmost down)
        foreach ($reversed_pages as $page_id_to_check) {
            $sidebar_parent = get_field('sidebar_parent_page', $page_id_to_check);
            if ($sidebar_parent) {
                return is_array($sidebar_parent) ? $sidebar_parent['ID'] : $sidebar_parent;
            }
        }
    }
    
    // Auto-detect: Find the topmost parent that has children
    // This should be consistent regardless of which child page we're on
    // All descendants of the same parent should show the same sidebar
    
    // Get all ancestors (returns direct parent first, then topmost parent last)
    $ancestors = get_post_ancestors($current_page_id);
    
    // Build a list of all pages in the hierarchy: current page + all ancestors
    $all_pages_in_hierarchy = array($current_page_id);
    if (!empty($ancestors)) {
        $all_pages_in_hierarchy = array_merge($all_pages_in_hierarchy, $ancestors);
    }
    
    // Reverse to check from topmost parent down to current page
    $all_pages_in_hierarchy = array_reverse($all_pages_in_hierarchy);
    
    // Find the topmost page that has children - this will be our consistent sidebar parent
    $sidebar_parent_id = false;
    
    foreach ($all_pages_in_hierarchy as $page_id_to_check) {
        // Skip if this page has sidebar explicitly disabled
        if (function_exists('get_field')) {
            $page_display_sidebar = get_field('display_sidebar', $page_id_to_check);
            if ($page_display_sidebar === false || $page_display_sidebar === 0 || $page_display_sidebar === '0') {
                continue;
            }
        }
        
        // Check if this page has any children (at any level)
        // First check direct children (more efficient)
        $direct_children = get_pages(array(
            'parent' => $page_id_to_check,
            'number' => 1,
            'post_status' => 'publish'
        ));
        
        // If no direct children, check all descendants
        if (empty($direct_children)) {
            $page_children = get_pages(array(
                'child_of' => $page_id_to_check,
                'number' => 1,
                'post_status' => 'publish'
            ));
        } else {
            $page_children = $direct_children;
        }
        
        if (!empty($page_children)) {
            // This is the topmost page with children - use it as sidebar parent
            $sidebar_parent_id = $page_id_to_check;
            break; // Stop at the first (topmost) page with children
        }
    }
    
    // If we found a parent with children, return it
    if ($sidebar_parent_id) {
        return $sidebar_parent_id;
    }
    
    // Fallback: Check ancestors in reverse order (topmost first) for any with children
    if (!empty($ancestors)) {
        $reversed_ancestors = array_reverse($ancestors); // Topmost parent first
        foreach ($reversed_ancestors as $ancestor_id) {
            // Skip if this ancestor has sidebar explicitly disabled
            if (function_exists('get_field')) {
                $ancestor_display_sidebar = get_field('display_sidebar', $ancestor_id);
                if ($ancestor_display_sidebar === false || $ancestor_display_sidebar === 0 || $ancestor_display_sidebar === '0') {
                    continue;
                }
            }
            
            // Check for direct children first
            $direct_children = get_pages(array(
                'parent' => $ancestor_id,
                'number' => 1,
                'post_status' => 'publish'
            ));
            
            if (!empty($direct_children)) {
                return $ancestor_id;
            }
            
            // Check for any descendants
            $all_descendants = get_pages(array(
                'child_of' => $ancestor_id,
                'number' => 1,
                'post_status' => 'publish'
            ));
            
            if (!empty($all_descendants)) {
                return $ancestor_id;
            }
        }
    }
    
    // If current page has children, use it
    $direct_children = get_pages(array(
        'parent' => $current_page_id,
        'number' => 1,
        'post_status' => 'publish'
    ));
    if (!empty($direct_children)) {
        return $current_page_id;
    }
    
    $children = get_pages(array(
        'child_of' => $current_page_id,
        'number' => 1,
        'post_status' => 'publish'
    ));
    if (!empty($children)) {
        return $current_page_id;
    }
    
    // Last resort: If we have ancestors, return the topmost one (if not disabled)
    if (!empty($ancestors)) {
        $topmost_ancestor = end($ancestors); // Last in array is topmost parent
        if (function_exists('get_field')) {
            $topmost_display_sidebar = get_field('display_sidebar', $topmost_ancestor);
            if ($topmost_display_sidebar !== false && $topmost_display_sidebar !== 0 && $topmost_display_sidebar !== '0') {
                return $topmost_ancestor;
            }
        } else {
            return $topmost_ancestor;
        }
    }
    
    return false;
}

/**
 * Display the page sidebar
 */
function omsar_display_page_sidebar() {
    $current_page_id = get_the_ID();
    
    // Get the parent page ID for sidebar
    $parent_page_id = omsar_get_sidebar_parent_page_id();
    
    if (!$parent_page_id) {
        return;
    }
    
    // Verify that current page is part of this parent's hierarchy
    // This ensures all pages in the same hierarchy show the same sidebar
    if (!omsar_is_page_in_hierarchy($current_page_id, $parent_page_id)) {
        return;
    }
    
    // Check if sidebar should be displayed
    if (!omsar_should_display_sidebar($parent_page_id)) {
        return;
    }
    
    get_template_part('template-parts/page-sidebar', null, array(
        'parent_page_id' => $parent_page_id
    ));
}

