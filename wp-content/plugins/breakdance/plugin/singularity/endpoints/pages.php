<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

use function Breakdance\Util\WP\performant_get_posts;
use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\Data\set_meta;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_get_pages',
        '\Breakdance\Singularity\Endpoints\getPages',
        'edit',
        true,
        [
            'args' => [
                'postType' => FILTER_UNSAFE_RAW
            ],
        ]
    );
});

/**
 * @param string $postType
 * @return array{data: mixed[]}
 */
function getPages($postType)
{
    // Get ALL pages (flat structure, no hierarchy)
    $pages = performant_get_posts([
        'post_type' => $postType,
        'post_status' => 'any',
        'posts_per_page' => -1,
    ]);

    $formattedPages = array_map(
        '\Breakdance\Singularity\Endpoints\formatPage',
        $pages
    );

    return ['data' => $formattedPages];
}

/**
 * @param \WP_Post $post
 * @return mixed
 */
function formatPage($post)
{

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress PossiblyFalseArgument
     */
    $futureLayerMeta = json_decode(\Breakdance\Data\get_meta($post->ID, __bdox('_meta_prefix') . 'futurelayer_meta'));

    // Flat structure - just include parentId instead of recursively loading children
    $formatted = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'postType' => $post->post_type,
        'relativeUrl' => getRelativeUrlForPage($post->ID),
        'futureLayerMeta' => $futureLayerMeta ?: false,
        'generatingInBackground' => false,
        'parentId' => $post->post_parent ? $post->post_parent : null
    ];

    return $formatted;
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_get_page',
        '\Breakdance\Singularity\Endpoints\getPage',
        'edit',
        true,
        [
            'args' => [
                'id' => FILTER_SANITIZE_NUMBER_INT
            ],
        ]
    );
});

/**
 * @param int $id
 * @return array{data: mixed}
 */
function getPage($id)
{
    $post = get_post($id);

    if (!$post) {
        return ['error' => 'Page not found'];
    }

    return ['data' => formatPage($post)];
}


/**
 * @return array
 */
function deleteAllPagesHeadersAndFootersAndClearMenu()
{

    $failedToDeleteSomething = false;

    $pages = get_posts([
        'post_type' => ['page', BREAKDANCE_BLOCK_POST_TYPE],
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ]);

    foreach ($pages as $pageId) {
        $trashed = wp_trash_post($pageId);

        if (!$trashed) {
            $failedToDeleteSomething = true;
        }
    }

    $menuId = \Breakdance\Singularity\Endpoints\getSingularityDefaultMenuOrCreateIfItDoesNotExist();
    if ($menuId) {
        \Breakdance\Singularity\Endpoints\clear_all_menu_items($menuId);
    }

    if ($failedToDeleteSomething) {
        return ['error' => __("Failed to delete all.", 'breakdance')];
    }

    return ['success' => "Deleted all successfully."];
}





add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_delete_page',
        '\Breakdance\Singularity\Endpoints\deletePage',
        'edit',
        true,
        [
            'args' => [
                'id' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param int $pageId
 * @return array
 */
function deletePage($pageId)
{
    // Move child pages to top-level before trashing the parent
    $children = get_posts([
        'post_type' => 'page',
        'post_parent' => $pageId,
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ]);

    foreach ($children as $childId) {
        wp_update_post([
            'ID' => $childId,
            'post_parent' => 0,
        ]);
    }

    $trashed = wp_trash_post($pageId);
    if (!$trashed) {
        /* translators: %s: page ID */
        return ['error' => sprintf(__('Failed to delete %s.', 'breakdance'), $pageId)];
    }

    /* translators: %s: page ID */
    return ['success' => sprintf(__('Deleted %s successfully.', 'breakdance'), $pageId)];
}

/**
 * Recursively remove a page from the menu structure
 * @param array $menu
 * @param int $pageId
 * @return array
 */
function removePageFromMenu($menu, $pageId)
{
    $result = [];

    foreach ($menu as $item) {
        // Skip the item if it matches the page to delete
        if ($item['id'] === $pageId) {
            continue;
        }

        // Recursively process children
        if (isset($item['children']) && is_array($item['children'])) {
            $item['children'] = removePageFromMenu($item['children'], $pageId);
        }

        $result[] = $item;
    }

    return $result;
}


add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_add_page',
        '\Breakdance\Singularity\Endpoints\addPage',
        'edit',
        true,
        [
            'args' => [
                'title' => FILTER_UNSAFE_RAW,
                'futureLayerMeta' => FILTER_UNSAFE_RAW,
                'post_type' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param string $title
 * @param string $futureLayerMeta
 * @param string $post_type
 * @return array
 */
function addPage($title, $futureLayerMeta, $post_type)
{
    $pageId = wp_insert_post(
        [
            'post_type' => $post_type,
            'post_title' => $title,
            'post_status' => 'publish',
        ],
        true
    );

    if (is_wp_error($pageId)) {
        return ['error' => __("Failed to create page.", 'breakdance')];
    }

    set_meta($pageId, __bdox('_meta_prefix') . 'futurelayer_meta', $futureLayerMeta);

    return ['success' => "Created " . $post_type . " successfully.", 'id' => $pageId, 'relativeUrl' => getRelativeUrlForPage($pageId)];
}


add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_add_or_update_page',
        '\Breakdance\Singularity\Endpoints\addOrUpdatePage',
        'edit',
        true,
        [
            'args' => [
                'title' => FILTER_UNSAFE_RAW,
                'postType' => FILTER_UNSAFE_RAW,
                'tree' => FILTER_UNSAFE_RAW,
                'futureLayerMeta' => FILTER_UNSAFE_RAW,
                'templateSettings' => FILTER_UNSAFE_RAW,
                'id' => FILTER_SANITIZE_NUMBER_INT,
                'shouldSetAsHomepage' => FILTER_UNSAFE_RAW,
            ],
            'optional_args' => ['tree', 'futureLayerMeta', 'templateSettings', 'id', 'shouldSetAsHomepage'],
        ]
    );
});



/**
 * @param string $title
 * @param string $postType
 * @param string $tree
 * @param string $futureLayerMeta
 * @param string $templateSettings
 * @param int $id
 * @param bool $shouldSetAsHomepage
 * @return array
 */
function addOrUpdatePage($title, $postType, $tree, $futureLayerMeta, $templateSettings, $id, $shouldSetAsHomepage)
{

    $post_data = [
        'post_type' => $postType,
        'post_title' => $title,
        'post_status' => 'publish',
    ];

    if ($id && get_post($id)) {
        $post_data['ID'] = $id;

        // Explicitly preserve post_parent when updating, if you dont, WP will lose it.
        // i suspect this applies to other fields to
        $existing_post = get_post($id);
        if ($existing_post) {
            $post_data['post_parent'] = $existing_post->post_parent;
        }
    }

    $id = wp_insert_post($post_data, true);

    if (is_wp_error($id)) {
        return ['error' => "Failed to " . ($post_data['ID'] ?? false ? 'update' : 'create') . " " . $postType];
    }

    if ($tree) {
        set_meta(
            $id,
            __bdox('_meta_prefix') . 'data',
            [
                'tree_json_string' => $tree,
            ]
        );
    }

    if ($futureLayerMeta) {
        set_meta(
            $id,
            __bdox('_meta_prefix') . 'futurelayer_meta',
            $futureLayerMeta
        );
    }

    if ($templateSettings) {
        \Breakdance\Data\set_meta(
            $id,
            __bdox('_meta_prefix') . 'template_settings',
            $templateSettings
        );
    }

    if ($shouldSetAsHomepage) {
        \Breakdance\Singularity\Endpoints\setPageToFrontpage($id);
    }

    return ['success' => "Created " . $postType . " successfully.", 'id' => $id, 'relativeUrl' => getRelativeUrlForPage($id)];
}












add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_create_pages_from_hierarchical_sitemap',
        '\Breakdance\Singularity\Endpoints\createPagesFromHierarchicalSitemap',
        'edit',
        true,
        [
            'args' => [
                'sitemap' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param string $sitemap
 * @return array
 */
function createPagesFromHierarchicalSitemap($sitemap)
{

    $sitemapData = json_decode($sitemap, true);

    if (!$sitemapData || !is_array($sitemapData)) {
        return ['error' => 'Invalid sitemap data'];
    }

    handleInitialSetupAndCleanup();

    $topLevelPageIds = [];

    foreach ($sitemapData as $pageData) {
        $pageId = createPageRecursively($pageData, 0);
        if (is_wp_error($pageId)) {
            return ['error' => 'Failed to create pages'];
        }
        $topLevelPageIds[] = $pageId;
    }

    // Fetch and format ALL pages (not just top-level)
    $allPages = get_posts([
        'post_type' => 'page',
        'post_status' => 'any',
        'posts_per_page' => -1,
    ]);

    $formattedPages = array_map(
        '\Breakdance\Singularity\Endpoints\formatPage',
        $allPages
    );

    return ['data' => $formattedPages];
}

/**
 * @param array $pageData
 * @param int $parentId
 * @return int|\WP_Error
 */
function createPageRecursively($pageData, $parentId)
{
    $title = $pageData['title'] ?? '';

    if (empty($title)) {
        return new \WP_Error('missing_title', 'Page title is required');
    }

    // Create the page
    $pageId = wp_insert_post(
        [
            'post_type' => 'page',
            'post_title' => $title,
            'post_status' => 'publish',
            'post_parent' => $parentId,
        ],
        true
    );

    if (is_wp_error($pageId)) {
        return $pageId;
    }

    // Save futureLayerMeta if present (already JSON string)
    if (isset($pageData['futureLayerMeta']) && $pageData['futureLayerMeta'] !== false) {
        set_meta(
            $pageId,
            __bdox('_meta_prefix') . 'futurelayer_meta',
            $pageData['futureLayerMeta']
        );
    }

    // Process children if they exist
    if (isset($pageData['children']) && is_array($pageData['children'])) {
        foreach ($pageData['children'] as $childData) {
            $childId = createPageRecursively($childData, $pageId);
            if (is_wp_error($childId)) {
                return $childId;
            }
        }
    }

    return $pageId;
}

/**
 * @param $post_id int
 */
function getRelativeUrlForPage($post_id)
{

    // LLM generated...

    $permalink = get_permalink($post_id);

    if (! $permalink) {
        return '';
    }

    $parsed_url = wp_parse_url($permalink);

    $relative_url = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

    if (!empty($parsed_url['query'])) {
        $relative_url .= '?' . $parsed_url['query'];
    }

    if (!empty($parsed_url['fragment'])) {
        $relative_url .= '#' . $parsed_url['fragment'];
    }

    return $relative_url;
}


/**
 * @param number $pageId
 */
function setPageToFrontpage($pageId)
{
    update_option('show_on_front', 'page');
    update_option('page_on_front', $pageId);
}


function handleInitialSetupAndCleanup()
{
    \Breakdance\Data\set_global_option('isFutureLayer', 'yes');
    /** @psalm-suppress UndefinedFunction */
    deleteAllPagesHeadersAndFootersAndClearMenu();
}
