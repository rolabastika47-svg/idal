<?php

namespace Breakdance\FutureLayer;

add_filter('breakdance_render_show_node', '\Breakdance\FutureLayer\shouldShowNode', 70, 2);

/**
 * @param boolean $shouldShow
 * @param array $node
 * @return boolean
 */
function shouldShowNode($shouldShow, $node)
{
    if (!$shouldShow) {
        return false;
    }

    /** @var boolean $isRemoved */
    $isRemoved = $node['data']['properties']['settings']['futurelayer']['removed'] ?? false;

    if ($isRemoved) {
        return false;
    }

    return $shouldShow;
}
