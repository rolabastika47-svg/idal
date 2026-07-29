<?php

namespace Breakdance\Singularity\Endpoints;

use function Breakdance\Util\WP\performant_get_posts;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_futurelayer_get_header_and_footer_blocks',
        '\Breakdance\Singularity\Endpoints\getHeaderAndFooterBlocks',
        'edit',
        true
    );
});

/**
 * @return array
 */
function getHeaderAndFooterBlocks()
{
    $allBlocks = performant_get_posts([
        'post_type' => BREAKDANCE_BLOCK_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ]);

    $headerBlockId = null;
    $footerBlockId = null;

    foreach ($allBlocks as $block) {
        if (strtolower($block->post_title) === 'header' && !$headerBlockId) {
            $headerBlockId = $block->ID;
        }
        if (strtolower($block->post_title) === 'footer' && !$footerBlockId) {
            $footerBlockId = $block->ID;
        }
    }

    return [
        'data' => [
            'header' => $headerBlockId,
            'footer' => $footerBlockId
        ]
    ];
}
