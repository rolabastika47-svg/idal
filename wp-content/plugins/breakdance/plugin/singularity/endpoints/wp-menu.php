<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_get_menu',
        '\Breakdance\Singularity\Endpoints\getMenu',
        'edit',
        true
    );

    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_set_menu',
        '\Breakdance\Singularity\Endpoints\setMenu',
        'edit',
        true,
        [
            'args' => [
                'menu' => FILTER_UNSAFE_RAW
            ],
        ]
    );
});


/**
 * Get the menu structure
 * @return array
 */
function getMenu()
{
    $menuId = getSingularityDefaultMenuOrCreateIfItDoesNotExist();

    if (!$menuId) {
        return ['data' => []];
    }

    $menu = wp_get_nav_menu_object($menuId);

    if (!$menu) {
        return ['data' => []];
    }

    $menuItems = wp_get_nav_menu_items($menu->term_id);

    if (!$menuItems) {
        return ['data' => []];
    }

    // Build hierarchical structure
    $menuStructure = buildMenuHierarchy($menuItems);

    return ['data' => $menuStructure];
}

/**
 * Build hierarchical menu structure from flat menu items
 * @param array $menuItems
 * @return array
 */
function buildMenuHierarchy($menuItems)
{
    // Include page items and custom link items that carry a stored page ID
    $relevantItems = array_filter($menuItems, function ($item) {
        if ($item->object === 'page') return true;
        if ($item->object === 'custom' && get_post_meta($item->ID, '_singularity_page_id', true)) return true;
        return false;
    });

    // Build a lookup of parent relationships
    $itemsByParent = [];
    foreach ($relevantItems as $item) {
        $parentId = (int) $item->menu_item_parent;
        if (!isset($itemsByParent[$parentId])) {
            $itemsByParent[$parentId] = [];
        }
        $itemsByParent[$parentId][] = $item;
    }

    // Build the tree recursively
    function buildTree($parentId, $itemsByParent)
    {
        if (!isset($itemsByParent[$parentId])) {
            return [];
        }

        $result = [];
        foreach ($itemsByParent[$parentId] as $item) {
            // Custom link nav parents use stored meta; regular page items use object_id
            $pageId = ($item->object === 'custom')
                ? (int) get_post_meta($item->ID, '_singularity_page_id', true)
                : (int) $item->object_id;

            $result[] = [
                'id'       => $pageId,
                'children' => buildTree((int) $item->ID, $itemsByParent)
            ];
        }

        return $result;
    }

    // Start with root items (parent = 0)
    return buildTree(0, $itemsByParent);
}

/**
 * Set the menu structure
 * @param string $menu
 * @return array
 */
function setMenu($menu)
{
    $menuData = json_decode($menu, true);
    return _setMenu($menuData);
}

/**
 * Set the entire menu structure
 * @param array $hierarchicalMenu
 */
function _setMenu($hierarchicalMenu)
{
    $menuId = getSingularityDefaultMenuOrCreateIfItDoesNotExist();
    if (!$menuId) {
        return ['error' => __("Failed to create or retrieve the default menu.", 'breakdance')];
    }

    // Clear existing menu
    clear_all_menu_items($menuId);

    // Recreate menu from structure
    foreach ($hierarchicalMenu as $menuItem) {
        addMenuItemsRecursively($menuId, $menuItem, 0);
    }

    return ['success' => __("Menu updated successfully.", 'breakdance')];
}

/**
 * Recursively add menu items with hierarchy
 * @param int $menuId
 * @param array $menuItem
 * @param int $parentMenuItemId
 * @return int|null The created menu item ID
 */
function addMenuItemsRecursively($menuId, $menuItem, $parentMenuItemId)
{
    $pageId = $menuItem['id'];
    $page = get_post($pageId);

    if (!$page || $page->post_type !== 'page') {
        return null;
    }

    $hasChildren = !empty($menuItem['children']);

    if ($hasChildren) {
        // Dropdown-only parent: not clickable, no link to the page
        $menuItemId = wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title'     => $page->post_title,
            'menu-item-url'       => '#',
            'menu-item-type'      => 'custom',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $parentMenuItemId,
        ]);
        // Store the page ID so we can figure out what page this custom link is associated with
        update_post_meta($menuItemId, '_singularity_page_id', $pageId);
    } else {
        $menuItemId = wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title'     => $page->post_title,
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $pageId,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $parentMenuItemId,
        ]);
    }

    foreach ($menuItem['children'] as $childItem) {
        addMenuItemsRecursively($menuId, $childItem, $menuItemId);
    }

    return $menuItemId;
}


function getSingularityDefaultMenuOrCreateIfItDoesNotExist()
{
    $menu_name = 'singularity_default_menu';
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? $menu->term_id : null;

    if ($menu_id) {
        return $menu_id;
    }

    $menu_id = wp_create_nav_menu($menu_name);

    if (is_wp_error($menu_id)) {
        return null;
    }

    return $menu_id;
}

function clear_all_menu_items($menu_id)
{
    if (! is_nav_menu($menu_id)) {
        return new \WP_Error('invalid_menu', __('Invalid menu ID provided.', 'breakdance'));
    }

    $menu_items = wp_get_nav_menu_items($menu_id);

    if (empty($menu_items)) {
        return true;
    }

    foreach ($menu_items as $item) {
        wp_delete_post($item->ID, true);
    }

    return true;
}


// getPageMenuInfo removed - use getMenu instead and check menu structure
