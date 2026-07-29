<?php

namespace Breakdance\DynamicData;

class CurrentUserBio extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('User Bio', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Current User', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'user_bio';
    }

    public function handler($attributes): StringData
    {
        $data = new StringData();
        $user = wp_get_current_user();
        $data->value = strip_shortcodes($user->description); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186

        return $data;
    }
}
