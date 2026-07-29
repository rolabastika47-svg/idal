<?php

namespace Breakdance\DynamicData;

class AcfGoogleMapsField extends AcfField {

    /**
     * @return array|Control[]
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('acf_google_map_output', __('Output as', 'breakdance'), ['type' => 'dropdown',
                    'items' => [
                        ['text' => __('Address', 'breakdance'), 'value' => 'address'],
                        ['text' => __('Latitude / Longitude', 'breakdance'), 'value' => 'coordinates'],
                    ]]
            ),
        ];
    }

    /**
     * @return string[]
     */
    public function defaultAttributes()
    {
        return [
            'acf_google_map_output' => 'address'
        ];
    }

    /**
     * @return string[]
     */
    public function returnTypes()
    {
        return ['string', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $map = AcfField::getValue($this->field);
        if (!$map) {
            return StringData::fromString('');
        }
        if ($attributes['acf_google_map_output'] === 'coordinates') {
            return StringData::fromString($map['lat'] . ',' . $map['lng']);
        }
        return StringData::fromString($map['address']);
    }
}
