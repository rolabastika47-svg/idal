<?php

namespace Breakdance\DynamicData;

class WoocommerceProductGalleryImage extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Gallery', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('WooCommerce', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'product_gallery_image';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('product', __('Product', 'breakdance'), [
                    'type' => 'post_chooser',
                    'layout' => 'vertical',
                    'postChooserOptions' => [
                        'multiple' => false,
                        'showThumbnails' => false,
                        'postType' => 'Product'
                    ]
                ]
            ),
            \Breakdance\Elements\control(
                'image_index',
                __('Image Index', 'breakdance'),
                [
                    'type' => 'number',
                    'layout' => 'vertical',
                ]
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        global $post;

        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        $product = wc_get_product($productId);
        if (!$product) {
            return ImageData::emptyImage();
        }

        $imageIds = $product->get_gallery_image_ids();

        $imageIndex = $attributes['image_index'] ?? 0;
        if (isset($imageIds[$imageIndex])) {
            return ImageData::fromAttachmentId($imageIds[$imageIndex]);
        }


        return ImageData::emptyImage();
    }
}
