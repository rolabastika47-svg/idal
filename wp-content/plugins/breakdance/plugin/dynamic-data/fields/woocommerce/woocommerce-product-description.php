<?php

namespace Breakdance\DynamicData;

class WoocommerceProductDescription extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('Product Description', 'breakdance');
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
        return 'product_description';
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
            \Breakdance\Elements\control('description_type', __('Description Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => __('Short Description', 'breakdance'), 'value' => 'short'],
                    ['text' => __('Long Description', 'breakdance'), 'value' => 'long'],
                ]),
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'description_type' => 'long'
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
        if (!$product || !array_key_exists('description_type', $attributes)) {
            return StringData::emptyString();
        }

        if ($attributes['description_type'] === 'short') {
            return StringData::fromString($product->get_short_description());
        }
        return StringData::fromString($product->get_description());
    }
}
