<?php

namespace Breakdance\DynamicData;

abstract class OembedField extends Field
{

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['video'];
    }

    public function fallbackControl()
    {
        return \Breakdance\Elements\control(
            'fallback_video',
            __('Fallback/Default Video', 'breakdance'),
            [
                'type' => 'video',
                'layout' => 'vertical',
            ]);
    }

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): OembedData;
}
