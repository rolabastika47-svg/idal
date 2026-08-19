<?php

namespace Breakdance\DynamicData;

class PostPermalink extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Permalink', 'breakdance');
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
        return 'post_permalink';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('post', 'Search Post', [
                'type' => 'post_chooser',
                'layout' => 'vertical',
                'postChooserOptions' => [
                    'multiple' => false,
                    'showThumbnails' => false,
                    'postType' => 'any'
                ]
            ]),
            \Breakdance\Elements\control('post', 'Post ID', [
                'type' => 'number',
                'layout' => 'vertical',
            ]),
        ];
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
        $postId = !empty($attributes['post']) ? $attributes['post'] : 0;
        return StringData::fromString(get_permalink($postId));
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
