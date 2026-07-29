<?php

namespace Breakdance\DynamicData;

use function Breakdance\LoopBuilder\getCurrentTerm;

class TermCustomField extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Term Custom Field', 'breakdance');
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
        return 'term_custom_field';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('key', __('Key', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (empty($attributes['key'])) {
            return StringData::emptyString();
        }

        $term = getCurrentTerm(true);
        $termId = $term->term_id;

        $value = get_term_meta($termId, $attributes['key'], true);
        if (!$value) {
            return StringData::emptyString();
        }
        return StringData::fromString($value);
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
