<?php

namespace Breakdance\DynamicData;

class PostContent extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Content', 'breakdance');
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
        return 'post_content';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        ob_start();
        the_content();
        return StringData::fromString(ob_get_clean());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
