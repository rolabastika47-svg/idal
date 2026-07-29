<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class ToggleClass extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Toggle Class', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'toggle_class';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('css_class', __('CSS Class', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ])
        ];
    }
}
