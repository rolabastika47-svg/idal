<?php

namespace Breakdance\DynamicData;

class CurrentUserCustomFieldOembed extends OembedField
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
        return __('Current User', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'user_custom_field_oembed';
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
        $userId = get_current_user_id();
        if (empty($attributes['key']) || $userId === 0) {
            return OembedData::emptyOembed();
        }


        $url = get_user_meta($userId, $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
