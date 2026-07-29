<?php

namespace Breakdance\Interactions\Triggers;

use Breakdance\Interactions\InteractionTrigger;
use function Breakdance\Elements\control;

class KeyDown extends InteractionTrigger
{
    /**
     * Returns the displayable label of the trigger.
     *
     * @return string
     */
    public static function name()
    {
        return __('Key Down', 'breakdance');
    }

    /**
     * URL friendly slug of the trigger.
     *
     * @return string
     */
    public static function slug()
    {
        return 'key_down';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('key', __('Key', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            control('ctrl_key', __('CTRL', 'breakdance'), [
                'type' => 'toggle',
                'layout' => 'inline',
            ]),
            control('shift_key', __('Shift', 'breakdance'), [
                'type' => 'toggle',
                'layout' => 'inline',
            ]),
        ];
    }
}
