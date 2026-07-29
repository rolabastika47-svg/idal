<?php

namespace Breakdance\DynamicData;

class AuthorWebsite extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Author Website', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Author', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'author_website';
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
        global $post;
        if (!$post) {
            return StringData::emptyString();
        }
        $author_id = $post->post_author;
        return StringData::fromString(get_the_author_meta('user_url', $author_id));
    }
}
