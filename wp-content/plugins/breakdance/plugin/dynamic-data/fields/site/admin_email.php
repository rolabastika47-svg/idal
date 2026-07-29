<?php

namespace Breakdance\DynamicData;

class AdminEmail extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Admin Email', 'breakdance');
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
        return 'admin_email';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('admin_email'));
    }
}
