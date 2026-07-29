<?php

namespace Breakdance\DynamicData;

class WoocommerceProductPriceRange extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Price Range', 'breakdance');
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
        return 'product_price'; // This slug is kept for backward compatibility.
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

        return StringData::fromString($product->get_price_html());
    }
}
