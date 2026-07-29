<?php

namespace Breakdance\DynamicData;

class Shortcode extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Shortcode', 'breakdance');
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
        return 'shortcode';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('shortcode', __('Shortcode', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'textOptions' => ['multiline' => true],
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (!array_key_exists('shortcode', $attributes)) {
            return StringData::emptyString();
        }

        ob_start();
        $output = do_shortcode($attributes['shortcode']);

        $shortcodeOutput = ob_get_clean();
        if ($shortcodeOutput) {
            return StringData::fromString($shortcodeOutput);
        }

        return StringData::fromString($output);
    }
}
