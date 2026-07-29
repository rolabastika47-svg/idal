<?php

namespace Breakdance\DynamicData;

class WoocommerceProductImage extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Image', 'breakdance');
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
        return 'product_image';
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
        )];
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

        $imageId = $product->get_image_id();
        return ImageData::fromAttachmentId($imageId);
    }
}

