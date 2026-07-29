<?php

namespace Breakdance\DynamicData;

class HomeUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Home URL', 'breakdance');
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
        return 'home_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url', 'string'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('url'));
    }
}

