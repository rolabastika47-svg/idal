<?php

namespace Breakdance\DynamicData;

class SiteRss extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('RSS URL', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Site Info', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'site_rss';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('type', __('Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Atom URL', 'breakdance'), 'value' => 'atom_url'],
                    ['text' => __('RDF URL', 'breakdance'), 'value' => 'rdf_url'],
                    ['text' => __('RSS URL', 'breakdance'), 'value' => 'rss_url'],
                    ['text' => __('RSS2 URL', 'breakdance'), 'value' => 'rss2_url'],
                    ['text' => __('Pingback URL', 'breakdance'), 'value' => 'pingback_url'],
                    ['text' => __('Comments Atom URL', 'breakdance'), 'value' => 'comments_atom_url'],
                    ['text' => __('Comments RSS2 URL', 'breakdance'), 'value' => 'comments_rss2_url'],
                ]
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'type' => 'rss2_url',
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo($attributes['type']));
    }
}
