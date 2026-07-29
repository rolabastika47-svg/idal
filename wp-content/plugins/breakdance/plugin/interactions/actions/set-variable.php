<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class SetVariable extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Set Variable', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'set_variable';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('variable_name', __('Variable Name', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            control('variable_value', __('Variable Value', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ])
        ];
    }
}
