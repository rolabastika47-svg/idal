<?php

namespace Breakdance\DynamicData;

class Date extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Current Date', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Utility', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'date';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('format', __('Format', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => __('Default', 'breakdance'), 'value' => '']],
                    \Breakdance\DynamicData\get_date_formats(),
                    [['text' => __('Custom', 'breakdance'), 'value' => 'Custom'], ['text' => __('Human', 'breakdance'), 'value' => 'Human']]
                ),
                [['text' => __('Custom', 'breakdance'), 'value' => 'Custom'], ['text' => __('Human', 'breakdance'), 'value' => 'Human']]
            ]),
            \Breakdance\Elements\control('custom_format', __('Custom Format', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'format' => 'F j, Y'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $format = $attributes['format'] ?? get_option('date_format');
        if (empty($format) || $format === 'Default') {
            $format = get_option('date_format');
        }
        if ($format === 'Custom') {
            $format = $attributes['custom_format'] ?? '';
        }
        if ($format === 'Human') {
            return StringData::fromString(human_time_diff(wp_date('U')));
        }
        return StringData::fromString(wp_date($format));
    }
}
