<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class ScrollTo extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Scroll To', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'scroll_to';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('scroll_offset', __('Scroll Offset', 'breakdance'), [
                'type' => 'number'
            ]),
            control('scroll_delay', __('Scroll Delay', 'breakdance'), [
                'type' => 'unit',
                'unitOptions' => [
                    'types' => ['ms', 's']
                ],
            ]),
        ];
    }
}
