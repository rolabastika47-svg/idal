<?php

namespace Breakdance\DynamicData;

class WoocommerceProductStock extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Stock', 'breakdance');
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
        return 'product_stock';
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
            \Breakdance\Elements\control('in_stock_text', __('In Stock Text', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('out_of_stock_text', __('Out Of Stock Text', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('backorder_text', __('On Backorder Text', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    public function defaultAttributes()
    {
        return [
            'in_stock_text' => __('In stock', 'breakdance'),
            'out_of_stock_text' => __('Out of stock', 'breakdance'),
            'backorder_text' => __('On backorder', 'breakdance'),
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
        if (!$product) {
            return StringData::emptyString();
        }

        $stockStatus = $product->get_stock_status();
        if (empty($stockStatus)) {
            return StringData::emptyString();
        }

        $messages = [
            'instock' => $attributes['in_stock_text'] ?? '',
            'outofstock' => $attributes['out_of_stock_text'] ?? '',
            'onbackorder' => $attributes['backorder_text'] ?? '',
        ];

        if (!array_key_exists($stockStatus, $messages)) {
            return StringData::emptyString();
        }

        return StringData::fromString($messages[$stockStatus]);
    }
}
