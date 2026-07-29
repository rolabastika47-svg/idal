<?php

namespace Breakdance\DynamicData;

class WoocommerceProductSale extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Sale', 'breakdance');
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
        return 'product_sale';
    }

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
            \Breakdance\Elements\control('sale_text', __('Sale Text', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
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
        if (!array_key_exists('sale_text', $attributes)) {
            return StringData::emptyString();
        }
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        $product = wc_get_product($productId);
        if ($product && $product->is_on_sale()) {
            return StringData::fromString($attributes['sale_text']);
        }

        return StringData::emptyString();
    }
}
