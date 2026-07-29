<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class ControlSlider extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Control Slider', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'control_slider';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('slider_action', __('Action', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    [ 'text' => __('Next Slide', 'breakdance'), 'value' => 'next' ],
                    [ 'text' => __('Previous Slide', 'breakdance'), 'value' => 'prev' ],
                    [ 'text' => __('Go to Slide', 'breakdance'), 'value' => 'goto']
                ],
            ]),
        ];
    }
}
