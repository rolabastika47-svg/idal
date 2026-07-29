<?php

namespace Breakdance\DynamicData;

class PostDate extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Date', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Post', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_date';
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'type' => 'modified',
            'format' => 'F j, Y'
        ];
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
                )
            ]),
            \Breakdance\Elements\control('format_custom', __('Custom Format', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
            \Breakdance\Elements\control('type', __('Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Post Published', 'breakdance'), 'value' => 'published'],
                    ['text' => __('Post Modified', 'breakdance'), 'value' => 'modified'],
                ]
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $value = $this->getDateFromAttributes($attributes);
        if (!is_string($value)) {
            return StringData::emptyString();
        }
        return StringData::fromString($value);
    }

    /**
     * @param mixed $attributes
     * @return mixed|string|void
     */
    private function getDateFromAttributes($attributes)
    {
        $format = $attributes['format'] ?? '';
        $type = $attributes['type'] ?? 'modified';

        if ($format === 'Custom') {
            $format = $attributes['format_custom'] ?? '';
        }
        if ($type === 'modified') {
            if ($format === 'Human') {
                return human_time_diff(get_the_modified_date('U'));
            }
            return apply_filters('the_modified_date', get_the_modified_date($format));
        }
        if ($format === 'Human') {
            return human_time_diff(get_the_date('U'));
        }
        return apply_filters('the_date', get_the_date($format));
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
