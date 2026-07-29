<?php

namespace Breakdance\DynamicData;

class WoocommerceProductSalePrice extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Sale Price', 'breakdance');
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
        return 'product_sale_price';
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
            \Breakdance\Elements\control('disable_formatting', __('Disable Formatting', 'breakdance'), [
                'type' => 'toggle'
            ]),
            \Breakdance\Elements\control('fallback_to_regular_price', __('Fallback to Regular Price', 'breakdance'), [
                'type' => 'toggle'
            ]),
        ];
    }

    public function defaultAttributes()
    {
        return [
            'price_type' => 'regular'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        $productId = $post->ID ?? null;
        $fallbackToRegularPrice = $attributes['fallback_to_regular_price'] ?? false;

        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        $product = wc_get_product($productId);

        if (!$product) {
            return StringData::emptyString();
        }

        if (!$product->is_on_sale() && !$fallbackToRegularPrice) {
            // If the product is not on sale, we return an empty string.
            return StringData::emptyString();
        }

        $unformatted_price = wc_get_price_to_display( $product, ['price' => $product->get_sale_price()] );

        if ($attributes['disable_formatting'] ?? false) {
            return StringData::fromString($unformatted_price);
        }

        $price = wc_price( $unformatted_price );

        return StringData::fromString($price);
    }
}
