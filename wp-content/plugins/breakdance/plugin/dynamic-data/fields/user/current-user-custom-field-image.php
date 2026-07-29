<?php

namespace Breakdance\DynamicData;

class CurrentUserCustomFieldImage extends ImageField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Custom Field (Image URL)', 'breakdance');
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
        return 'user_custom_field_image';
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
        return [
            'image_url'
        ];
    }
    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $userId = get_current_user_id();
        if (empty($attributes['key']) || $userId === 0) {
            return ImageData::emptyImage();
        }


        $urlOrAttachmentId = get_user_meta($userId, $attributes['key'], true);
        if (filter_var($urlOrAttachmentId, FILTER_VALIDATE_URL)) {
            $url = strip_shortcodes($urlOrAttachmentId); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186
            return ImageData::fromUrl($url);
        }

        return ImageData::fromAttachmentId($urlOrAttachmentId);
    }
}
