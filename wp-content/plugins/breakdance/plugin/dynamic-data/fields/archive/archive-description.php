<?php

namespace Breakdance\DynamicData;

class ArchiveDescription extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Archive Description', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Archive', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'archive_description';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_the_archive_description());
    }
}
