<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Lib\Vendor\Sinergi\BrowserDetector\Browser;
use Breakdance\Lib\Vendor\Sinergi\BrowserDetector\Os;
use function Breakdance\Config\Breakpoints\get_breakpoints;
use function Breakdance\BreakdanceOxygen\Strings\__bdox;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerMiscConditions'
);

function registerMiscConditions()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'browser-name',
            'label' => __('Browser', 'breakdance'),
            'category' => __('Other', 'breakdance'),
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                $browser = new Browser();

                return [
                    [
                        'label' => __('Browser', 'breakdance'),
                        'items' => [
                            ['text' => __('Google Chrome', 'breakdance'), 'value' => Browser::CHROME],
                            ['text' => __('Mozilla Firefox', 'breakdance'), 'value' => Browser::FIREFOX],
                            ['text' => __('Safari', 'breakdance'), 'value' => Browser::SAFARI],
                            ['text' => __('Microsoft Edge', 'breakdance'), 'value' => Browser::EDGE],
                            ['text' => __('Internet Explorer', 'breakdance'), 'value' => Browser::IE],
                            ['text' => __('Samsung Browser', 'breakdance'), 'value' => Browser::SAMSUNG_BROWSER],
                            ['text' => __('Opera', 'breakdance'), 'value' => Browser::OPERA],
                        ]
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    if (!$values) {
                        return false;
                    }

                    $browser = new Browser();

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($browser->getName(), $values);
                        case OPERAND_NONE_OF:
                            return !in_array($browser->getName(), $values);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'operating-system-name',
            'label' => __('Operating System', 'breakdance'),
            'category' => __('Other', 'breakdance'),
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => __('Operating System / Device', 'breakdance'),
                        'items' => [
                            ['text' => __('Windows', 'breakdance'), 'value' => Os::WINDOWS],
                            ['text' => __('Mac OS', 'breakdance'), 'value' => Os::OSX],
                            ['text' => __('Linux', 'breakdance'), 'value' => Os::LINUX],
                            ['text' => __('iOS (iPhone)', 'breakdance'), 'value' => Os::IOS],
                            ['text' => __('Android', 'breakdance'), 'value' => Os::ANDROID],
                            ['text' => __('Chrome OS', 'breakdance'), 'value' => Os::CHROME_OS],
                        ]
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    if (!$values) {
                        return false;
                    }

                    $os = new Os();

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($os->getName(), $values);
                        case OPERAND_NONE_OF:
                            return !in_array($os->getName(), $values);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['All'],
            'availableForPostType' => ['breakdance_popup'],
            'slug' => 'breakpoints',
            'label' => __('Breakpoint', 'breakdance'),
            'category' => __('Other', 'breakdance'),
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => __('Breakpoint', 'breakdance'),
                        'items' => array_map(function ($breakpoint) {
                            return ['text' => $breakpoint['label'], 'value' => $breakpoint['id']];
                        },\Breakdance\Config\Breakpoints\get_breakpoints())
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    // Always return true as Breakpoint
                    // conditions must be handled on the frontend
                    return true;
                },
            'templatePreviewableItems' => false,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'content-created-with',
            'label' => __('Content Created With', 'breakdance'),
            'category' => __('Other', 'breakdance'),
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {

                $mode = (string) BREAKDANCE_MODE;

                return [
                    [
                        'label' => __('Builder', 'breakdance'),
                        'items' => [
                            ['text' => __bdox('plugin_name'), 'value' => $mode],
                        ]
                    ]
                ];
            },
            'callback' =>
            /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
            function ($operand, $values): bool {

                global $post;

                $mode = (string) BREAKDANCE_MODE;

                /** @var \WP_Post|null */
                $post = $post;

                $is_breakdance = $post && \Breakdance\Data\get_tree($post->ID) !== false;

                if ($operand === OPERAND_IS && in_array($mode, $values)) {
                    return $is_breakdance;
                } else if ($operand === OPERAND_IS_NOT && in_array($mode, $values)) {
                    return !$is_breakdance;
                }

                return false;
            },
            'templatePreviewableItems' => false,
        ]
    );

}
