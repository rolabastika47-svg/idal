<?php

namespace Breakdance\DynamicData;

class SiteUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Site URL', 'breakdance');
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
        return 'site_url';
    }

    public function returnTypes()
    {
        return ['url', 'string'];
    }

    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('wpurl'));
    }
}
