<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class RemoveClass extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Remove Class', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'remove_class';
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
            ]),
        ];
    }
}
