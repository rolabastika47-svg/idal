<?php

namespace Breakdance\FutureLayer;

add_filter("breakdance_element_attributes", "\Breakdance\FutureLayer\addAttributes", 100, 1);

/**
 * @param  ElementAttribute[] $attributes
 *
 * @return array
 */
function addAttributes($attributes)
{
    $attributes[] = [
        "name" => "data-futurelayer-removed",
        "template" => "{{ settings.futurelayer.removed ? 'true' }}",
    ];

    return $attributes;
}
