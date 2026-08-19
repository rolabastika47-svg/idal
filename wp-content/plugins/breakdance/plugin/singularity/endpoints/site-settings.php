<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_singularity_set_site_title',
        '\Breakdance\Singularity\Endpoints\setSiteTitle',
        'edit',
        true,
        [
            'args' => [
                'siteTitle' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param string $siteTitle
 * @return array
 */
function setSiteTitle($siteTitle)
{
    update_option('blogname', $siteTitle);
    return ['success' => 'Site title updated successfully.'];
}
