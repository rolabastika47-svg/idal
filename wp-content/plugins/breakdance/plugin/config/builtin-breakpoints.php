<?php

namespace Breakdance\Config\Breakpoints;

define('BASE_BREAKPOINT_ID', 'breakpoint_base');
define('FIRST_RESPONSIVE_BREAKPOINT_ID', 'breakpoint_tablet_landscape');

/**
 * @return Breakpoint[]
 */
function get_builtin_breakpoints()
{
    return [
        breakpoint(BASE_BREAKPOINT_ID, __('Desktop', 'breakdance'), '100%', []),
        breakpoint(FIRST_RESPONSIVE_BREAKPOINT_ID, __('Tablet Landscape', 'breakdance'), 1024, ['maxWidth' => 1119]),
        breakpoint('breakpoint_tablet_portrait', __('Tablet Portrait', 'breakdance'), 768, ['maxWidth' => 1023]),
        breakpoint('breakpoint_phone_landscape', __('Phone Landscape', 'breakdance'), 480, ['maxWidth' => 767]),
        breakpoint('breakpoint_phone_portrait', __('Phone Portrait', 'breakdance'), 400, ['maxWidth' => 479]),
    ];
}
