<?php

namespace Breakdance\DynamicData;

class PostCustomFieldOembed extends OembedField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Custom Field (Oembed URL)', 'breakdance');
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
        return 'post_custom_field_oembed';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('key', __('Key', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): OembedData
    {
        if (empty($attributes['key'])) {
            return OembedData::emptyOembed();
        }

        $url = get_post_meta(get_the_ID(), $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
