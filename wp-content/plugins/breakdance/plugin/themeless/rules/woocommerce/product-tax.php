<?php

namespace Breakdance\Themeless\Rules;

if (function_exists('wc_get_product_tax_class_options')) {
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-tax-status',
            'label' => __('Tax Status', 'breakdance'),
            'category' => __('Price', 'breakdance'),
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [[
                    'label' => __('Status', 'breakdance'),
                    'items' => [
                        ['text' => __('Taxable', 'breakdance'), 'value' => 'taxable'],
                        ['text' => __('Shipping only', 'breakdance'), 'value' => 'shipping'],
                        ['text' => __('None', 'breakdance'), 'value' => 'none'],
                    ]
                ]];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $product;
                    if (!$product) {
                        return false;
                    }
                    /** @var \WC_Product $product */
                    $product = $product;
                    return in_array($product->get_tax_status(), $value);
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param DropdownData[]|false $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    if (!$value) {
                        return $query;
                    }
                    $compare = operandToQueryCompare($operand);
                    $query['meta_query'][] = [
                        'compare' => $compare,
                        'key' => '_tax_status',
                        'value' => array_map(static function ($option) {
                            return $option['value'];
                        }, $value),
                    ];

                    return $query;
                },
        ]
    );
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-tax-class',
            'label' => __('Tax Class', 'breakdance'),
            'category' => __('Price', 'breakdance'),
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                /** @var array<string,string> $taxClasses */
                $taxClasses = wc_get_product_tax_class_options();
                $items = [];
                foreach ($taxClasses as $taxClassValue => $taxClassLabel) {
                    $items[] = ['text' => $taxClassLabel, 'value' => (string) $taxClassValue];
                }
                return [[
                    'label' => __('Class', 'breakdance'),
                    'items' => $items
                ]];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $product;
                    if (!$product) {
                        return false;
                    }
                    /** @var \WC_Product $product */
                    $product = $product;
                    return in_array($product->get_tax_class(), $value);
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param DropdownData[]|false $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    if (!$value) {
                        return $query;
                    }
                    $compare = operandToQueryCompare($operand);
                    $query['meta_query'][] = [
                        'compare' => $compare,
                        'key' => '_tax_class',
                        'value' => array_map(static function ($option) {
                            return $option['value'];
                        }, $value),
                    ];

                    return $query;
                },
        ]
    );
}
