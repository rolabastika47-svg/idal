<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-downloadable',
        'label' => __('Downloadable', 'breakdance'),
        'category' => __('Product', 'breakdance'),
        'operands' => [OPERAND_IS],
        'valueInputType' => 'dropdown',
        'values' => function () {
            return [
                [
                    'label' => __('Status', 'breakdance'),
                    'items' => [
                        ['text' => __('downloadable', 'breakdance'), 'value' => 'yes'],
                        ['text' => __('not downloadable', 'breakdance'), 'value' => 'no']
                    ]
                ]
            ];
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                if ($value === 'yes') {
                    return $product->is_downloadable();
                }
                if ($value === 'no') {
                    return !$product->is_downloadable();
                }
                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $query['meta_query'][] = [
                    'compare' => '=',
                    'key' => '_downloadable',
                    'value' => $value
                ];
                return $query;
            },
    ]
);
