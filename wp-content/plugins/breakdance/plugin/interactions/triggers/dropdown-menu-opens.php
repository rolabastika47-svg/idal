<?php

namespace Breakdance\Interactions\Triggers;

use Breakdance\Interactions\InteractionTrigger;

class DropdownMenuOpens extends InteractionTrigger
{
    /**
     * Returns the displayable label of the trigger.
     *
     * @return string
     */
    public static function name()
    {
        return __('Dropdown Menu Opens', 'breakdance');
    }

    /**
     * URL friendly slug of the trigger.
     *
     * @return string
     */
    public static function slug()
    {
        return 'dropdown_menu_opens';
    }
}
