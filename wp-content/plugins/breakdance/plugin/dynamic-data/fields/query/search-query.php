<?php

namespace Breakdance\DynamicData;

class SearchQuery extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Search Query', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('URL & Query', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'search_query';
    }

    public function returnTypes()
    {
        return ['query', 'string'];
    }

    public function handler($attributes): StringData
    {
        return StringData::fromString(get_search_query());
    }
}
