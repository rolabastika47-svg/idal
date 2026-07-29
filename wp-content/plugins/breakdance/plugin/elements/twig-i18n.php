<?php

namespace Breakdance\I18n;

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_html__',
    'Breakdance\I18n\twig_esc_html__',
    '(text, domain) => wp.i18n.__(text, domain)'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_attr__',
    'Breakdance\I18n\twig_esc_attr__',
    '(text, domain) => wp.i18n.__(text, domain)'
);
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_html_x',
    'Breakdance\I18n\twig_esc_html_x',
    '(text, domain) => wp.i18n.__(text, domain)'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_attr_x',
    'Breakdance\I18n\twig_esc_attr_x',
    '(text, domain) => wp.i18n.__(text, domain)'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'sprintf',
    'Breakdance\I18n\twig_sprintf',
    '(text, ...args) => wp.i18n.sprintf(text, ...args)'
);

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_html__($text, $domain) {
    return esc_html__($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_attr__($text, $domain) {
    return esc_attr__($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_html_x($text, $domain) {
    return esc_html_x($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_attr_x($text, $domain) {
    return esc_attr_x($text, $domain);
}

/**
 *
 * @param string $text
 * @param string|int|float ...$values
 * @return string
 */
function twig_sprintf(string $text, ...$values): string {
    return sprintf($text, ...$values);
}
