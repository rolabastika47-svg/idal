<?php

namespace Breakdance\DynamicData;

class PostCommentsNumber extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Comments Number', 'breakdance');
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
        return 'post_comments_number';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('zero', __('No comments', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('one', __('One comment', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('more', __('Multiple comments', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $value = get_comments_number_text(
            $attributes['zero'] ?? __('No comments', 'breakdance'),
            $attributes['one'] ?? __('One comment', 'breakdance'),
            $attributes['more'] ?? __('% comment', 'breakdance')
        );
        return StringData::fromString($value);
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
