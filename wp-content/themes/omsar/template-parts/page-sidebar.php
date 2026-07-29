<?php
/**
 * Template Part: Page Sidebar Navigation
 * 
 * Displays a sidebar navigation menu for pages and their children
 * 
 * @param int $parent_page_id - The parent page ID to build menu from
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if a page has content (WordPress content, Elementor, or Breakdance)
 * 
 * @param int|WP_Post $page_id Page ID or WP_Post object
 * @return bool True if page has content, false otherwise
 */
if (!function_exists('omsar_page_has_content')) {
    function omsar_page_has_content($page_id) {
        // Get page object if ID is passed
        if (is_numeric($page_id)) {
            $page = get_post($page_id);
        } else {
            $page = $page_id;
            $page_id = $page->ID;
        }
        
        if (!$page) {
            return false;
        }
        
        // Check WordPress post content
        $post_content = trim($page->post_content);
        if (!empty($post_content)) {
            // Remove HTML tags and check if there's actual content
            $text_content = strip_tags($post_content);
            $text_content = preg_replace('/\s+/', ' ', $text_content);
            $text_content = trim($text_content);
            if (!empty($text_content)) {
                return true;
            }
        }
        
        // Check if page is built with Elementor
        if (did_action('elementor/loaded') && class_exists('\Elementor\Plugin')) {
            if (isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->db)) {
                try {
                    $is_elementor = \Elementor\Plugin::$instance->db->is_built_with_elementor($page_id);
                    if ($is_elementor === true) {
                        // Check if Elementor has content
                        $elementor_data = get_post_meta($page_id, '_elementor_data', true);
                        if (!empty($elementor_data)) {
                            // Decode JSON and check if there are elements
                            $elementor_content = json_decode($elementor_data, true);
                            if (!empty($elementor_content) && is_array($elementor_content)) {
                                return true;
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Elementor check failed, continue
                } catch (Error $e) {
                    // Elementor check failed, continue
                }
            }
        }

        // Check if page is built with Breakdance
        if (metadata_exists('post', $page_id, '_breakdance_data')) {
            $breakdance_raw = get_post_meta($page_id, '_breakdance_data', true);
            if (!empty($breakdance_raw) && $breakdance_raw !== 'null' && $breakdance_raw !== '{}') {
                $breakdance_data = is_string($breakdance_raw) ? json_decode($breakdance_raw, true) : $breakdance_raw;
                if (is_array($breakdance_data) && !empty($breakdance_data)) {
                    if (!empty($breakdance_data['tree']['children']) || !empty($breakdance_data['tree'])) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}

/**
 * Determine if a page should be clickable based on content rules
 * 
 * Rules:
 * - Has content (WordPress, Elementor, or Breakdance) → clickable
 * - No content BUT has children → NOT clickable (only shows children)
 * - No content AND no children (leaf) → clickable
 * 
 * @param int|WP_Post $page_id Page ID or WP_Post object
 * @param bool $has_children Whether the page has child pages
 * @return bool True if page should be clickable, false otherwise
 */
if (!function_exists('omsar_page_is_clickable')) {
    function omsar_page_is_clickable($page_id, $has_children = false) {
        $has_content = omsar_page_has_content($page_id);
        
        // Rule 1: Has content → clickable
        if ($has_content) {
            return true;
        }
        
        // Rule 2: No content BUT has children → NOT clickable
        if ($has_children) {
            return false;
        }
        
        // Rule 3: No content AND no children (leaf) → clickable
        return true;
    }
}

// Get parent page ID from args or use current page
$parent_page_id = isset($args['parent_page_id']) ? $args['parent_page_id'] : get_the_ID();

// Get all child pages
$child_pages = get_pages(array(
    'child_of' => $parent_page_id,
    'sort_column' => 'post_date',
    'sort_order' => 'ASC'
));

// Get parent page
$parent_page = get_post($parent_page_id);

if (!$parent_page) {
    return;
}

// Get current page ID
$current_page_id = get_the_ID();

// Check if current page is the parent page (not a child or nested page)
$is_parent_page = ($current_page_id == $parent_page_id);

// Check if current page is the parent or a child
$is_parent_or_child = ($current_page_id == $parent_page_id) || ($parent_page->post_parent == $parent_page_id || wp_get_post_parent_id($current_page_id) == $parent_page_id || in_array($current_page_id, wp_list_pluck($child_pages, 'ID')));

// Build menu structure
$menu_items = array();

// Add "Overview" link (parent page) - will update has_children and is_clickable after getting children
$menu_items[] = array(
    'title' => function_exists('pll__') ? pll__('Overview') : __('Overview', 'omsar'),
    'url' => get_permalink($parent_page_id),
    'is_active' => ($current_page_id == $parent_page_id),
    'has_children' => false,
    'is_clickable' => true // Temporary, will be updated below
);

// Get all pages (including parent) to build hierarchy
$all_pages = get_pages(array(
    'child_of' => $parent_page_id,
    'sort_column' => 'post_date',
    'sort_order' => 'ASC'
));

// Build a hierarchical structure of all pages
$pages_by_parent = array();
foreach ($all_pages as $page) {
    $parent_id = $page->post_parent;
    if (!isset($pages_by_parent[$parent_id])) {
        $pages_by_parent[$parent_id] = array();
    }
    $pages_by_parent[$parent_id][] = $page;
}

// Recursive function to build menu items with any depth
function build_menu_items_recursive($parent_id, $pages_by_parent, $current_page_id, $depth = 0) {
    $items = array();
    
    if (!isset($pages_by_parent[$parent_id]) || empty($pages_by_parent[$parent_id])) {
        return $items;
    }
    
    // Sort pages by publish date (oldest first, newest last)
    $children = $pages_by_parent[$parent_id];
    usort($children, function($a, $b) {
        return strtotime($a->post_date) - strtotime($b->post_date);
    });
    
    foreach ($children as $child) {
        $is_current = ($current_page_id == $child->ID);
        $is_ancestor = false;
        
        // Check if current page is a descendant of this child
        if ($current_page_id != $child->ID) {
            $ancestors = get_post_ancestors($current_page_id);
            $is_ancestor = in_array($child->ID, $ancestors);
        }
        
        // Recursively get children of this child
        $child_items = build_menu_items_recursive($child->ID, $pages_by_parent, $current_page_id, $depth + 1);
        $has_children = !empty($child_items);
        
        // Determine if this page should be clickable based on content rules
        $is_clickable = omsar_page_is_clickable($child->ID, $has_children);
        
        $items[] = array(
            'ID' => $child->ID,
            'title' => $child->post_title,
            'url' => get_permalink($child->ID),
            'is_active' => $is_current || $is_ancestor,
            'has_children' => $has_children,
            'children' => $child_items,
            'depth' => $depth,
            'is_clickable' => $is_clickable
        );
    }
    
    return $items;
}

// Get direct children (second level)
$direct_children = build_menu_items_recursive($parent_page_id, $pages_by_parent, $current_page_id);

// Check if parent page has children (needed for Overview clickability)
$parent_has_children = !empty($direct_children);

// Update Overview menu item with correct has_children and is_clickable
if (!empty($menu_items) && isset($menu_items[0])) {
    $menu_items[0]['has_children'] = $parent_has_children;
    $menu_items[0]['is_clickable'] = omsar_page_is_clickable($parent_page_id, $parent_has_children);
}

// Add direct children as menu items
foreach ($direct_children as $child) {
    $menu_items[] = $child;
}

// Always show sidebar if we have at least the Overview link
// (This allows sidebar to show even if there are no children yet)

// Get sidebar colors from ACF options
$sidebar_dark_color = '';
$sidebar_light_color = '';
$sidebar_active_dark_color = '';
$sidebar_active_light_color = '';
$sidebar_sub_active_dark_color = '';
$sidebar_sub_active_light_color = '';

if (function_exists('get_field')) {
    $sidebar_dark_color = get_field('sidebar_dark_color', 'option');
    $sidebar_light_color = get_field('sidebar_light_color', 'option');
    $sidebar_active_dark_color = get_field('sidebar_active_dark_color', 'option');
    $sidebar_active_light_color = get_field('sidebar_active_light_color', 'option');
    $sidebar_sub_active_dark_color = get_field('sidebar_sub_active_dark_color', 'option');
    $sidebar_sub_active_light_color = get_field('sidebar_sub_active_light_color', 'option');
}

// Build inline style with CSS variables
$sidebar_style = '';
$style_parts = [];

if ($sidebar_dark_color) {
    $style_parts[] = '--sidebar-dark-color: ' . esc_attr($sidebar_dark_color) . ';';
}
if ($sidebar_light_color) {
    $style_parts[] = '--sidebar-light-color: ' . esc_attr($sidebar_light_color) . ';';
}
if ($sidebar_active_dark_color) {
    $style_parts[] = '--sidebar-active-dark-color: ' . esc_attr($sidebar_active_dark_color) . ';';
}
if ($sidebar_active_light_color) {
    $style_parts[] = '--sidebar-active-light-color: ' . esc_attr($sidebar_active_light_color) . ';';
}
if ($sidebar_sub_active_dark_color) {
    $style_parts[] = '--sidebar-sub-active-dark-color: ' . esc_attr($sidebar_sub_active_dark_color) . ';';
}
if ($sidebar_sub_active_light_color) {
    $style_parts[] = '--sidebar-sub-active-light-color: ' . esc_attr($sidebar_sub_active_light_color) . ';';
}

if (!empty($style_parts)) {
    $sidebar_style = ' style="' . implode(' ', $style_parts) . '"';
}

// For child/nested pages, add sidebar-animated class immediately to prevent animation
// For parent pages, let JavaScript handle the animation on initial load
$sidebar_class = 'page-sidebar';
if (!$is_parent_page) {
    $sidebar_class .= ' sidebar-animated';
}
?>

<aside class="<?php echo esc_attr($sidebar_class); ?>" data-parent-page-id="<?php echo esc_attr($parent_page_id); ?>" data-current-page-id="<?php echo esc_attr($current_page_id); ?>" data-is-parent-page="<?php echo $is_parent_page ? '1' : '0'; ?>"<?php echo $sidebar_style; ?>>
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <?php foreach ($menu_items as $index => $item): 
                $is_clickable = isset($item['is_clickable']) ? $item['is_clickable'] : true;
                $is_overview = ($index === 0); // Overview is always the first item
                $item_classes = 'sidebar-menu-item';
                $item_classes .= $item['is_active'] ? ' active' : '';
                $item_classes .= $item['has_children'] ? ' has-children' : '';
                $item_classes .= !$is_clickable ? ' not-clickable' : '';
            ?>
                <li class="<?php echo esc_attr($item_classes); ?>">
                    <?php if ($is_clickable): ?>
                        <a href="<?php echo esc_url($item['url']); ?>" class="sidebar-link <?php echo $item['is_active'] ? 'active' : ''; ?>">
                            <span class="link-text"><?php echo esc_html($item['title']); ?></span>
                            <?php if ($item['has_children'] && !$is_overview): ?>
                                <i class="bi bi-chevron-right sidebar-chevron sidebar-chevron-main"></i>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <span class="sidebar-link sidebar-link-disabled <?php echo $item['is_active'] ? 'active' : ''; ?>">
                            <span class="link-text"><?php echo esc_html($item['title']); ?></span>
                            <?php if ($item['has_children'] && !$is_overview): ?>
                                <i class="bi bi-chevron-right sidebar-chevron sidebar-chevron-main"></i>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($item['has_children'] && !empty($item['children'])): ?>
                        <ul class="sidebar-submenu">
                            <?php 
                            // Recursive function to render menu items
                            if (!function_exists('render_sidebar_menu_item')) {
                                function render_sidebar_menu_item($child_item, $current_page_id) {
                                    $child_id = isset($child_item['ID']) ? $child_item['ID'] : 0;
                                    $is_active = ($current_page_id == $child_id);
                                    $has_children = isset($child_item['has_children']) && $child_item['has_children'] && !empty($child_item['children']);
                                    $title = isset($child_item['title']) ? $child_item['title'] : '';
                                    $url = isset($child_item['url']) ? $child_item['url'] : '#';
                                    $is_clickable = isset($child_item['is_clickable']) ? $child_item['is_clickable'] : true;
                                    
                                    // Check if current page is a descendant
                                    if (!$is_active && $child_id) {
                                        $ancestors = get_post_ancestors($current_page_id);
                                        $is_active = in_array($child_id, $ancestors);
                                    }
                                    
                                    $item_classes = 'sidebar-submenu-item';
                                    $item_classes .= $is_active ? ' active' : '';
                                    $item_classes .= $has_children ? ' has-children' : '';
                                    $item_classes .= !$is_clickable ? ' not-clickable' : '';
                                    ?>
                                    <li class="<?php echo esc_attr($item_classes); ?>">
                                        <?php if ($is_clickable): ?>
                                            <a href="<?php echo esc_url($url); ?>" class="sidebar-sublink <?php echo $is_active ? 'active' : ''; ?>">
                                                <?php echo esc_html($title); ?>
                                                <?php if ($has_children): ?>
                                                    <i class="bi bi-chevron-right sidebar-chevron sidebar-chevron-nested"></i>
                                                <?php endif; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="sidebar-sublink sidebar-sublink-disabled <?php echo $is_active ? 'active' : ''; ?>">
                                                <?php echo esc_html($title); ?>
                                                <?php if ($has_children): ?>
                                                    <i class="bi bi-chevron-right sidebar-chevron sidebar-chevron-nested"></i>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($has_children && !empty($child_item['children'])): ?>
                                            <ul class="sidebar-submenu sidebar-submenu-nested">
                                                <?php foreach ($child_item['children'] as $nested_child): ?>
                                                    <?php render_sidebar_menu_item($nested_child, $current_page_id); ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                    <?php
                                }
                            }
                            
                            foreach ($item['children'] as $subchild): 
                                render_sidebar_menu_item($subchild, $current_page_id);
                            endforeach; 
                            ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

