<?php

namespace Breakdance\DynamicData;

class PostTitle extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Title', 'breakdance');
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
        return 'post_title';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_the_title());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
