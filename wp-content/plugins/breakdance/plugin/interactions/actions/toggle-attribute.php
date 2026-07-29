<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class ToggleAttribute extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Toggle Attribute', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'toggle_attribute';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('attribute_name', __('Attribute Name', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            control('attribute_value', __('Attribute Value', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ])
        ];
    }
}
