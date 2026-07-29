<?php
namespace Breakdance\DynamicData;

class UtmTags extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Common UTM Tags', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('URL & Query', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'common_utm_tags';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'query'];
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('parameter_name', __('UTM Tag', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Source', 'breakdance'), 'value' => 'utm_source'],
                    ['text' => __('Medium', 'breakdance'), 'value' => 'utm_medium'],
                    ['text' => __('Campaign', 'breakdance'), 'value' => 'utm_campaign'],
                    ['text' => __('Term', 'breakdance'), 'value' => 'utm_term'],
                    ['text' => __('Content', 'breakdance'), 'value' => 'utm_content'],
                ]
            ]),
        ];
    }

    public function handler($attributes): StringData
    {
        if (!array_key_exists('parameter_name', $attributes)) {

            return StringData::fromString('');
        }
        $queryParameter = filter_input(INPUT_GET, $attributes['parameter_name'], FILTER_SANITIZE_SPECIAL_CHARS) ?: '';

        return StringData::fromString($queryParameter);
    }
}
