<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-customer-orders',
        'label' => __('Order Count', 'breakdance'),
        'category' => __('Customer', 'breakdance'),
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                if (!\is_user_logged_in()) {
                    return false;
                }
                $user = wp_get_current_user();
                $totalValue = (int) $value;
                $orderCount = (int) wc_get_customer_order_count($user->ID);
                if ($operand === OPERAND_IS) {
                    return $orderCount === $totalValue;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $orderCount !== $totalValue;
                }
                if ($operand === OPERAND_GREATER_THAN) {
                    return $orderCount > $totalValue;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $orderCount < $totalValue;
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-customer-spend',
        /* translators: %s is the currency symbol */
        'label' => sprintf(__('Total Spend (%s)', 'breakdance'), html_entity_decode(get_woocommerce_currency_symbol())),
        'category' => __('Customer', 'breakdance'),
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                if (!\is_user_logged_in()) {
                    return false;
                }
                $user = wp_get_current_user();
                $totalValue = (float) $value;
                $totalSpend = (float) wc_get_customer_total_spent($user->ID);
                if ($operand === OPERAND_IS) {
                    return $totalSpend === $totalValue;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $totalSpend !== $totalValue;
                }
                if ($operand === OPERAND_GREATER_THAN) {
                    return $totalSpend > $totalValue;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $totalSpend < $totalValue;
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-customer-purchased-product',
        'label' => __('Purchased Product', 'breakdance'),
        'category' => __('Customer', 'breakdance'),
        'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF, OPERAND_ALL_OF],
        'values' => function () {

            $products = array_merge(
                [
                    ['text' => __('Current Product', 'breakdance'), 'value' => 'current']
                ],
                getPostsForMultiselect(['post_type' => 'product'])
            );

            return [[
                'label' => __('Products', 'breakdance'),
                'items' => $products
            ]];
        },
        'callback' => /**
         * @param mixed $operand
         * @param string[] $value
         * @return bool
         */
            function ($operand, $value): bool {
                if (!\is_user_logged_in()) {
                    return false;
                }
                /** @var \WP_User $user */
                $user = wp_get_current_user();

                $results = array_map(static function($productId) use ($user) {
                    if ($productId === 'current') {
                        $productId = get_the_ID();
                    }
                    if (!$productId) {
                        return false;
                    }
                    return wc_customer_bought_product($user->user_email, $user->ID, (int) $productId);
                }, $value);
                if ($operand === OPERAND_ONE_OF) {
                    return in_array(true, $results);
                }
                if ($operand === OPERAND_NONE_OF) {
                    return !in_array(true, $results);
                }
                if ($operand === OPERAND_ALL_OF) {
                    return !in_array(false, $results);
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);
