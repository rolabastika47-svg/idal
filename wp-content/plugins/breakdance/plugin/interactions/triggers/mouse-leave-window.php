<?php

namespace Breakdance\Interactions\Triggers;

use Breakdance\Interactions\InteractionTrigger;

class MouseLeaveWindow extends InteractionTrigger
{
    /**
     * Returns the displayable label of the trigger.
     *
     * @return string
     */
    public static function name()
    {
        return __('Mouse Leave Window', 'breakdance');
    }

    /**
     * URL friendly slug of the trigger.
     *
     * @return string
     */
    public static function slug()
    {
        return 'mouse_leave_window';
    }
}
