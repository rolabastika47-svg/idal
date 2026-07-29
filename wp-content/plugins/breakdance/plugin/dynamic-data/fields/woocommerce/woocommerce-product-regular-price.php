<?php

namespace Breakdance\DynamicData;

class WoocommerceProductRegularPrice extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Regular Price', 'breakdance');
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
        return 'product_regular_price';
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
        ];
    }

    public function defaultAttributes()
    {
        return [];
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

        if (!$product) {
            return StringData::emptyString();
        }

        $unformatted_price = wc_get_price_to_display( $product, ['price' => $product->get_regular_price()] );

        if ($attributes['disable_formatting'] ?? false) {
            return StringData::fromString($unformatted_price);
        }

        $price = wc_price( $unformatted_price );

        return StringData::fromString($price);
    }
}
