<?php

namespace Breakdance\DynamicData;

class PostTerms extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Terms', 'breakdance');
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
        return 'post_terms';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('taxonomy', __('Taxonomy', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => \Breakdance\DynamicData\get_taxonomies_for_builder_post()
            ]),
            \Breakdance\Elements\control('link', __('Link', 'breakdance'), [
                'type' => 'toggle',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('separator', __('Separator', 'breakdance'), ['type' => 'text']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $separator = $attributes['separator'] ?? ', ';
        $taxonomy = $attributes['taxonomy'] ?? '';
        $link = filter_var($attributes['link'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $terms = get_the_term_list(get_the_ID(), $taxonomy, '', $separator);

        // a similar check is duplicated a few times
        if (is_wp_error($terms)) {
            $message = "WP_Error: " . $terms->get_error_message(); // should we actually get all the error messages and join them?
            return StringData::fromString($message);
        }

        if (!$terms) {
            return StringData::emptyString();
        }

        if ($link) {
            return StringData::fromString($terms);
        }
        return StringData::fromString(strip_tags($terms));
    }

    /**
     * @inheritDoc
     */
    function proOnly()
    {
        return false;
    }
}
