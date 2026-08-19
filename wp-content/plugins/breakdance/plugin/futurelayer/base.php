<?php

namespace Breakdance\FutureLayer;

include __DIR__ . "/control.php";
include __DIR__ . "/attributes.php";
include __DIR__ . "/should-show-node.php";

/**
 * @return bool
 */
function isFutureLayer()
{
    return \Breakdance\Data\get_global_option('isFutureLayer') === 'yes';
}

/**
 * @return bool
 */
function isDebugMode()
{
    return \Breakdance\Data\get_global_option('breakdance_futurelayer_debug_mode') === 'yes';
}
