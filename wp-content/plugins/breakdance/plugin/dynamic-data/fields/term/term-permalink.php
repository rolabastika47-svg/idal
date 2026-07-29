<?php

namespace Breakdance\DynamicData;

use function Breakdance\LoopBuilder\getCurrentTerm;

class TermPermalink extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Term Permalink', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Term', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'term_permalink';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        /**
         * @var \WP_Term|null
         * @psalm-suppress UndefinedFunction
         */
        $term = getCurrentTerm(true);

        return StringData::fromString(get_term_link($term));
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
