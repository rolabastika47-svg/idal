<?php

namespace Breakdance\DynamicData;

class ToolsetLinkField extends ToolsetField
{

    public function controls()
    {
        return [
            \Breakdance\Elements\control('toolset_link_target', __('Target', 'breakdance'), [
                    'type' => 'dropdown',
                    'items' => [
                        ['text' => __('Blank', 'breakdance'), 'value' => '_blank'],
                        ['text' => __('Self', 'breakdance'), 'value' => '_self'],
                        ['text' => __('Parent', 'breakdance'), 'value' => '_parent'],
                        ['text' => __('Top', 'breakdance'), 'value' => '_top'],
                    ],
                ]
            ),
        ];
    }

    public function defaultAttributes()
    {
        return ['toolset_link_target' => '_self'];
    }

    public function handler($attributes): StringData
    {
        $linkTarget = $attributes['toolset_link_target'] ?? '_self';
        $url = ToolsetField::getValue($this->field, ['target' => $linkTarget]);
        return StringData::fromString($url);
    }
}
