<?php

namespace Breakdance\DynamicData;

class SiteTitle extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Site Title', 'breakdance');
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
        return 'site_title';
    }

    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('name'));
    }
}
