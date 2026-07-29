<?php

namespace Breakdance\DynamicData;

use function Breakdance\LoopBuilder\getCurrentTerm;

class TermCount extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Term Count', 'breakdance');
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
        return 'term_count';
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
        return StringData::fromString((string) $term->count);
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
