<?php

namespace Breakdance\Interactions\Triggers;

use Breakdance\Interactions\InteractionTrigger;

class MouseMoveInViewport extends InteractionTrigger
{
    /**
     * Returns the displayable label of the trigger.
     *
     * @return string
     */
    public static function name()
    {
        return __('Mouse Move In Viewport', 'breakdance');
    }

    /**
     * URL friendly slug of the trigger.
     *
     * @return string
     */
    public static function slug()
    {
        return 'mouse_move_in_viewport';
    }

    /**
     * Specifies elements for which this trigger is available.
     *
     * @return string[]
     */
    public function availableFor()
    {
        return [];
    }
}
