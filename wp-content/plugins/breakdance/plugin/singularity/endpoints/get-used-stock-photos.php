<?php

// @psalm-ignore-file

namespace Breakdance\Singularity\Endpoints;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_used_stock_photos',
        '\Breakdance\Singularity\Endpoints\getUsedStockPhotos',
        'edit',
        true,
        [
            'args' => []
        ]
    );
});

function getUsedStockPhotos()
{
    global $wpdb;

    $used_stock_photo_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->postmeta}
         WHERE meta_key = %s
         ",
        '_breakdance_stock_photo_id'
    ));

    return [
        'used_stock_photo_ids' => array_values(array_unique($used_stock_photo_ids))
    ];
}
