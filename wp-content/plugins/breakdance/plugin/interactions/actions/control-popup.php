<?php

namespace Breakdance\Interactions\Actions;

use Breakdance\Interactions\InteractionAction;
use function Breakdance\Elements\control;

class ControlPopup extends InteractionAction
{
    /**
     * Returns the displayable label of the action.
     *
     * @return string
     */
    public static function name()
    {
        return __('Control Popup', 'breakdance');
    }

    /**
     * URL friendly slug of the action.
     *
     * @return string
     */
    public static function slug()
    {
        return 'control_popup';
    }

    /**
     * Get controls for the builder
     * @return Control[]
     */
    public static function controls()
    {
        return [
            control('popup', __('Popup', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            control('popup_action', __('Popup Action', 'breakdance'), [
                'type' => 'button_bar',
                'layout' => 'vertical',
                'items' => [
                    ['value' => 'open', 'text' => __('Open', 'breakdance')],
                    ['value' => 'close', 'text' => __('Close', 'breakdance')],
                    ['value' => 'toggle', 'text' => __('Toggle', 'breakdance')]
                ]
            ])
        ];
    }
}
