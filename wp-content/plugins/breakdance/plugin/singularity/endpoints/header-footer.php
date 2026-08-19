<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\Data\set_meta;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_create_header_and_footer',
        '\Breakdance\Singularity\Endpoints\createHeaderAndFooter',
        'edit',
        true,
        [
            'args' => [
                'headerData' => FILTER_UNSAFE_RAW,
                'footerData' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param string $headerData
 * @param string $footerData
 * @return array
 */
function createHeaderAndFooter($headerData, $footerData)
{
    $parsedHeaderData = json_decode($headerData, true);
    $parsedFooterData = json_decode($footerData, true);

    $headerId = createHeaderOrFooterBlock(
        $parsedHeaderData['title'],
        $parsedHeaderData['postType'],
        $parsedHeaderData['tree'] ?? false,
    );

    $footerId = createHeaderOrFooterBlock(
        $parsedFooterData['title'],
        $parsedFooterData['postType'],
        $parsedFooterData['tree'] ?? false,
    );

    return [
        'success' => 'Created header and footer successfully.',
        'header' => [
            'id' => $headerId
        ],
        'footer' => [
            'id' => $footerId
        ]
    ];
}

/**
 * @param string $title
 * @param string $postType
 * @param string|false $tree
 * @return int|false
 */
function createHeaderOrFooterBlock($title, $postType, $tree)
{
    $id = wp_insert_post(
        [
            'post_type' => $postType,
            'post_title' => $title,
            'post_status' => 'publish',
        ],
        true
    );

    if (is_wp_error($id)) {
        return false;
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

    return $id;
}
