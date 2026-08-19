<?php

namespace Breakdance\DynamicData;

use Breakdance\Components\ComponentInputValueHolder;
use function Breakdance\isRequestFromBuilderDynamicDataGet;

class OxyComponentField extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Component Field';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Component';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'oxy_component';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('key', 'Key', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchFunction' => 'breakdance_get_component_fields',
                    ],
                ],
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url', 'image_url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (empty($attributes['key'])) {
            return StringData::emptyString();
        }

        $component = ComponentInputValueHolder::getCurrentComponent();

        if ($component === null) {
            if (isRequestFromBuilderDynamicDataGet()) {
                // Currently, we can't preview the component field in the builder, because the component is not available.
                // So we return a placeholder for illustration purposes.
                return StringData::fromString("{{$attributes['key']}}");
            } else {
                return StringData::emptyString();
            }
        }

        $properties = $component['properties'] ?? [];
        $value = $properties[$attributes['key']] ?? '';

        if (is_array($value) && in_array('url', $value)) {
            return StringData::fromString($value['url']);
        }

        if (!is_string($value)) {
            return StringData::emptyString();
        }

        if (is_bool($value)) {
            return StringData::fromString($value ? '1' : '0');
        }

        return StringData::fromString($value);
    }
}
