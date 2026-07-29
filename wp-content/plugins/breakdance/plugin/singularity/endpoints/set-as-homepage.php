<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_set_as_homepage',
        '\Breakdance\Singularity\Endpoints\setAsHomepage',
        'edit',
        true,
        [
            'args' => [
                'id' => FILTER_SANITIZE_NUMBER_INT,
            ],
        ]
    );
});

/**
 * @param int $pageId
 * @return array
 */
function setAsHomepage($pageId)
{
    update_option('page_on_front', $pageId);
    update_option('show_on_front', 'page');

    /* translators: %s: page ID */
    return ['success' => sprintf(__("Page %s set as homepage successfully.", 'breakdance'), $pageId)];
}
