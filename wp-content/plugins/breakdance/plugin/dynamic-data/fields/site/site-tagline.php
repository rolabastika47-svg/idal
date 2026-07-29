<?php

namespace Breakdance\DynamicData;

class SiteTagline extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Site Tagline', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Site Info', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'site_tagline';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('description'));
    }
}
