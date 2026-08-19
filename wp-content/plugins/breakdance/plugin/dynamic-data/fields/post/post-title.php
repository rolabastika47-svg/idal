<?php

namespace Breakdance\DynamicData;

class PostTitle extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Title', 'breakdance');
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
        return 'post_title';
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
    public function handler($attributes): StringData
    {
        $postId = !empty($attributes['post']) ? $attributes['post'] : 0;
        return StringData::fromString(get_the_title($postId));
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
