<?php

namespace Breakdance\DynamicData;

class PostImageAttachments extends GalleryField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Post Image Attachments', 'breakdance');
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
        return 'post_image_attachments';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): GalleryData
    {
        $attachedImages = get_attached_media('image', get_the_ID());
        $gallery = new GalleryData();
        $gallery->images = array_map(static function($attachment) {
            return ImageData::fromAttachmentId($attachment->ID);
        }, $attachedImages);

        return $gallery;
    }
}
