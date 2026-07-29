<?php

namespace Breakdance\Interactions\Triggers;

use Breakdance\Interactions\InteractionTrigger;

class PageScrolled extends InteractionTrigger
{
    /**
     * Returns the displayable label of the trigger.
     *
     * @return string
     */
    public static function name()
    {
        return __('Page Scrolled', 'breakdance');
    }

    /**
     * URL friendly slug of the trigger.
     *
     * @return string
     */
    public static function slug()
    {
        return 'page_scrolled';
    }
}
