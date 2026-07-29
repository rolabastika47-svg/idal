<?php

namespace Breakdance\DynamicData;

use function Breakdance\LoopBuilder\getCurrentTerm;

class TermName extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Term Name', 'breakdance');
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
        return 'term_name';
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
        return StringData::fromString($term->name);
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
