<?php

namespace Breakdance\DynamicData;

use function Breakdance\LoopBuilder\getCurrentTerm;

class TermDescription extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Term Description', 'breakdance');
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
        return 'term_description';
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
        return StringData::fromString(term_description($term));
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
