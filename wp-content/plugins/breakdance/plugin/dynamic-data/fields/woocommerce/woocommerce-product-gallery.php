<?php

namespace Breakdance\DynamicData;

class WoocommerceProductGallery extends GalleryField {

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
        return 'product_gallery';
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
            \Breakdance\Elements\control('exclude_product_image', __('Exclude Product Image', 'breakdance'), [
                    'type' => 'toggle',
                    'layout' => 'vertical',
                ]
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): GalleryData
    {
        global $post;
        $gallery = new GalleryData();

        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        $product = wc_get_product($productId);
        if (!$product) {
            return $gallery;
        }

        $image_ids = $product->get_gallery_image_ids();

        $excludeProductImage = filter_var($attributes['exclude_product_image'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$excludeProductImage && $product->get_image_id()) {
            $image_ids[] = $product->get_image_id();
        }

        $gallery->images = array_map(function($imageId) {
            return ImageData::fromAttachmentId($imageId);
        }, $image_ids);

        return $gallery;
    }
}
