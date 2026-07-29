<?php

namespace Breakdance\DynamicData;

class UrlParameter extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('URL Parameter', 'breakdance');
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
        return 'url_parameters';
    }

    public function returnTypes()
    {
        return ['query', 'string'];
    }

    public function controls()
    {
        return [
            \Breakdance\Elements\control('parameter_name', __('Parameter Name', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
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
