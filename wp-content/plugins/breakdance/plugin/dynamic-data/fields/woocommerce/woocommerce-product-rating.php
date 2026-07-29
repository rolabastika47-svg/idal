<?php

namespace Breakdance\DynamicData;

class WoocommerceProductRating extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Rating', 'breakdance');
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
        return 'product_rating';
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
            \Breakdance\Elements\control('rating_type', __('Rating Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => __('Rating', 'breakdance'), 'value' => 'rating'],
                    ['text' => __('Rating Count', 'breakdance'), 'value' => 'rating_count'],
                    ['text' => __('Review Count', 'breakdance'), 'value' => 'review_count'],
                ])
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }
        $product = wc_get_product($productId);

        if (!$product || !array_key_exists('rating_type', $attributes)) {
            return StringData::emptyString();
        }

        if ($attributes['rating_type'] === 'rating') {
            return StringData::fromString($product->get_average_rating());
        }

        if ($attributes['rating_type'] === 'rating_count') {
            return StringData::fromString($product->get_rating_count());
        }

        if ($attributes['rating_type'] === 'review_count') {
            return StringData::fromString($product->get_review_count());
        }

        return StringData::emptyString();
    }
}
